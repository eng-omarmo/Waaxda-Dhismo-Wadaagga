<?php

namespace App\Http\Controllers;

use App\Models\ManualOperationLog;
use App\Models\PaymentVerification;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\Certificate;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class AdminManualRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceRequest::query()->with('service');
        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }
        if ($q = $request->string('q')->toString()) {
            $query->where(function ($w) use ($q) {
                $w->where('user_full_name', 'like', "%$q%")
                  ->orWhere('user_email', 'like', "%$q%")
                  ->orWhere('user_phone', 'like', "%$q%");
            });
        }
        $requests = $query->latest()->paginate(10)->withQueryString();
        $statuses = ['pending','verified','rejected','discrepancy'];
        return view('admin.manual.requests.index', compact('requests','statuses'));
    }

    public function create()
    {
        $services = Service::orderBy('name')->get();
        return view('admin.manual.requests.create', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'user_full_name' => ['required', 'string', 'max:255'],
            'user_email' => ['required', 'email', 'max:255'],
            'user_phone' => ['nullable', 'string', 'max:50'],
            'user_national_id' => ['nullable', 'string', 'max:255'],
            'request_details' => ['nullable', 'array'],
        ]);

        $sr = ServiceRequest::create([
            'service_id' => $validated['service_id'],
            'user_id' => null,
            'user_full_name' => $validated['user_full_name'],
            'user_email' => $validated['user_email'],
            'user_phone' => $validated['user_phone'] ?? null,
            'user_national_id' => $validated['user_national_id'] ?? null,
            'request_details' => $validated['request_details'] ?? null,
            'status' => 'pending',
        ]);

        ManualOperationLog::create([
            'user_id' => Auth::id(),
            'action' => 'create_request',
            'target_type' => 'ServiceRequest',
            'target_id' => (string) $sr->id,
            'details' => ['service_id' => $sr->service_id],
        ]);

        return redirect()->route('admin.manual-requests.show', $sr)->with('status', 'Request created');
    }

    public function show(ServiceRequest $manual_request)
    {
        $manual_request->load('service', 'payments');
        return view('admin.manual.requests.show', ['request' => $manual_request]);
    }

    public function verifyPayment(Request $request, ServiceRequest $manual_request)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'reference_number' => ['required', 'string', 'max:255', 'unique:payment_verifications,reference_number'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $service = $manual_request->service;
        $diff = abs((float) $validated['amount'] - (float) $service->price);
        $status = $diff < 0.01 ? 'verified' : 'discrepancy';

        $pv = PaymentVerification::create([
            'service_request_id' => $manual_request->id,
            'amount' => $validated['amount'],
            'payment_date' => $validated['payment_date'],
            'reference_number' => $validated['reference_number'],
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'status' => $status,
            'notes' => $validated['notes'] ?? null,
        ]);

        $manual_request->status = $status;
        $manual_request->processed_by = Auth::id();
        $manual_request->processed_at = now();
        $manual_request->save();

        ManualOperationLog::create([
            'user_id' => Auth::id(),
            'action' => 'verify_payment',
            'target_type' => 'ServiceRequest',
            'target_id' => (string) $manual_request->id,
            'details' => ['payment_id' => $pv->id, 'status' => $status],
        ]);

        if ($status === 'verified') {
            $project = Project::where('registrant_email', $manual_request->user_email)->latest()->first();
            if ($project) {
                $cert = Certificate::issueForProject($project, $manual_request->service, Auth::id());
                ManualOperationLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'issue_certificate',
                    'target_type' => 'Project',
                    'target_id' => (string) $project->id,
                    'details' => ['certificate_id' => $cert->id],
                ]);
            }
        }

        try {
            if ($status === 'verified') {
                Mail::to($manual_request->user_email)->send(new \App\Mail\ServiceRequestVerified($manual_request, $pv));
                return redirect()->route('admin.manual-requests.show', $manual_request)->with('status', 'Payment verified and user notified');
            } else {
                Mail::to($manual_request->user_email)->send(new \App\Mail\ServiceProcessingException($manual_request, 'Payment amount discrepancy'));
                return redirect()->route('admin.manual-requests.show', $manual_request)->with('status', 'Discrepancy recorded and user notified');
            }
        } catch (\Throwable $e) {
            return redirect()->route('admin.manual-requests.show', $manual_request)->with('error', 'Notification failed');
        }
    }

    public function reconcile(Request $request, ServiceRequest $manual_request, PaymentVerification $payment)
    {
        if ($payment->service_request_id !== $manual_request->id) {
            abort(404);
        }

        $validated = $request->validate([
            'reconciled_amount' => ['required', 'numeric', 'min:0.01'],
            'reconciliation_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $payment->reconciled_amount = $validated['reconciled_amount'];
        $payment->reconciliation_notes = $validated['reconciliation_notes'] ?? null;
        $payment->status = 'verified';
        $payment->save();

        $manual_request->status = 'verified';
        $manual_request->save();

        ManualOperationLog::create([
            'user_id' => Auth::id(),
            'action' => 'reconcile_payment',
            'target_type' => 'ServiceRequest',
            'target_id' => (string) $manual_request->id,
            'details' => ['payment_id' => $payment->id],
        ]);

        $project = Project::where('registrant_email', $manual_request->user_email)->latest()->first();
        if ($project) {
            $cert = Certificate::issueForProject($project, $manual_request->service, Auth::id());
            ManualOperationLog::create([
                'user_id' => Auth::id(),
                'action' => 'issue_certificate',
                'target_type' => 'Project',
                'target_id' => (string) $project->id,
                'details' => ['certificate_id' => $cert->id],
            ]);
        }

        try {
            Mail::to($manual_request->user_email)->send(new \App\Mail\ServiceRequestVerified($manual_request, $payment));
        } catch (\Throwable $e) {
        }

        return redirect()->route('admin.manual-requests.show', $manual_request)->with('status', 'Payment reconciled and user notified');
    }

    public function reject(Request $request, ServiceRequest $manual_request)
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $manual_request->status = 'rejected';
        $manual_request->processed_by = Auth::id();
        $manual_request->processed_at = now();
        $manual_request->save();

        ManualOperationLog::create([
            'user_id' => Auth::id(),
            'action' => 'reject_request',
            'target_type' => 'ServiceRequest',
            'target_id' => (string) $manual_request->id,
            'details' => ['reason' => $validated['reason']],
        ]);

        try {
            Mail::to($manual_request->user_email)->send(new \App\Mail\ServiceProcessingException($manual_request, $validated['reason']));
        } catch (\Throwable $e) {
        }

        return redirect()->route('admin.manual-requests.show', $manual_request)->with('status', 'Request rejected and user notified');
    }

    public function receipt(ServiceRequest $manual_request, PaymentVerification $payment)
    {
        if ($payment->service_request_id !== $manual_request->id) {
            abort(404);
        }
        $receiptNumber = 'IPAMS-'.str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT);
        return view('admin.manual.requests.receipt', [
            'request' => $manual_request,
            'payment' => $payment,
            'receiptNumber' => $receiptNumber,
        ]);
    }

    public function publicReceipt(PaymentVerification $payment)
    {
        $manual_request = ServiceRequest::with('service')->findOrFail($payment->service_request_id);
        $receiptNumber = 'IPAMS-'.str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT);
        return view('receipt', [
            'request' => $manual_request,
            'payment' => $payment,
            'receiptNumber' => $receiptNumber,
        ]);
    }
}
