<?php

namespace App\Http\Controllers;

use App\Mail\SelfServiceConfirmation;
use App\Models\OnlinePayment;
use App\Models\PendingRegistration;
use App\Models\PendingRegistrationDocument;
use App\Models\Project;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

class SelfServiceController extends Controller
{
    public function start()
    {
        $services = Service::orderBy('name')->get();
        return view('portal.select-service', compact('services'));
    }

    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],
        ]);
        $service = Service::findOrFail($validated['service_id']);
        $reg = PendingRegistration::create([
            'service_id' => $service->id,
            'service_slug' => $service->slug,
            'full_name' => '',
            'email' => '',
            'phone' => '',
            'status' => 'draft',
            'step' => 2,
            'resume_token' => (string) Str::uuid(),
            'data' => [],
        ]);
        $request->session()->put('portal_reg_id', $reg->id);
        return redirect()->route('portal.info');
    }

    public function info(Request $request)
    {
        $reg = $this->current($request);
        return view('portal.info', compact('reg'));
    }

    public function storeInfo(Request $request)
    {
        $reg = $this->current($request);
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'national_id' => ['nullable', 'string', 'max:255'],
        ]);
        $data = $reg->data ?: [];
        $data['national_id'] = $validated['national_id'] ?? null;
        $reg->update([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'data' => $data,
            'step' => 3,
        ]);
        return redirect()->route('portal.details');
    }

    public function details(Request $request)
    {
        $reg = $this->current($request);
        if ($reg->service_slug === 'project-registration' || $reg->service_slug === 'construction-permit-application') {
            return view('portal.details-project', compact('reg'));
        }
        return redirect()->route('portal.docs');
    }

    public function storeDetails(Request $request)
    {
        $reg = $this->current($request);
        if ($reg->service_slug === 'project-registration' || $reg->service_slug === 'construction-permit-application') {
            $validated = $request->validate([
                'project_name' => ['required', 'string', 'max:255'],
                'location_text' => ['required', 'string', 'max:255'],
            ]);
            $data = $reg->data ?: [];
            $data['project_name'] = $validated['project_name'];
            $data['location_text'] = $validated['location_text'];
            $reg->update(['data' => $data, 'step' => 4]);
        }
        return redirect()->route('portal.docs');
    }

    public function docs(Request $request)
    {
        $reg = $this->current($request);
        $docs = PendingRegistrationDocument::where('pending_registration_id', $reg->id)->latest()->get();
        return view('portal.docs', compact('reg','docs'));
    }

    public function storeDocs(Request $request)
    {
        $reg = $this->current($request);
        $request->validate([
            'documents.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
        ]);
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('self_docs', 'public');
                PendingRegistrationDocument::create([
                    'pending_registration_id' => $reg->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientOriginalExtension(),
                ]);
            }
        }
        $reg->update(['step' => 5]);
        return redirect()->route('portal.pay');
    }

    public function pay(Request $request)
    {
        $reg = $this->current($request);
        $service = Service::findOrFail($reg->service_id);
        return view('portal.pay', ['reg' => $reg, 'service' => $service]);
    }

    public function processPay(Request $request)
    {
        $reg = $this->current($request);
        $service = Service::findOrFail($reg->service_id);
        $validated = $request->validate([
            'payment_method' => ['required', 'in:card,paypal'],
            'card_name' => ['required_if:payment_method,card','string','max:255'],
            'card_number' => ['required_if:payment_method,card','string','min:12','max:19'],
            'card_expiry' => ['required_if:payment_method,card','string','regex:/^(0[1-9]|1[0-2])\\/\\d{2}$/'],
            'card_cvc' => ['required_if:payment_method,card','string','min:3','max:4'],
        ], [
            'card_expiry.regex' => 'Expiry must be MM/YY',
        ]);
        $provider = $validated['payment_method'] === 'paypal' ? 'paypal' : 'fake';
        $status = $service->price > 0 ? 'succeeded' : 'succeeded';
        $transactionId = 'txn_'.Str::random(12);
        $payment = OnlinePayment::create([
            'pending_registration_id' => $reg->id,
            'provider' => $provider,
            'payment_method' => $validated['payment_method'],
            'amount' => $service->price,
            'currency' => 'USD',
            'status' => $status,
            'transaction_id' => $transactionId,
            'reference' => Str::upper(Str::random(8)),
            'receipt_number' => 'IPAMS-SRV-'.str_pad((string) $reg->id, 6, '0', STR_PAD_LEFT),
            'verified_at' => now(),
            'metadata' => ['masked_card' => isset($validated['card_number']) ? substr($validated['card_number'], -4) : null],
        ]);
        $reg->update(['status' => 'paid', 'step' => 6]);

        if (in_array($reg->service_slug, ['project-registration','construction-permit-application'])) {
            Project::create([
                'project_name' => $reg->data['project_name'] ?? 'Unnamed',
                'location_text' => $reg->data['location_text'] ?? '',
                'developer_id' => null,
                'status' => 'Submitted',
                'registrant_name' => $reg->full_name,
                'registrant_phone' => $reg->phone ?? '',
                'registrant_email' => $reg->email,
            ]);
        }

        $password = Str::random(12);
        $user = User::create([
            'email' => $reg->email,
            'password' => $password,
            'first_name' => explode(' ', trim($reg->full_name))[0] ?? $reg->full_name,
            'last_name' => trim(Str::after($reg->full_name, explode(' ', trim($reg->full_name))[0] ?? '')) ?: '',
            'contact_phone' => $reg->phone ?? '',
            'role' => 'user',
            'active' => true,
        ]);
        Auth::login($user);

        $receiptUrl = URL::temporarySignedRoute('portal.receipt.public', now()->addDays(7), ['payment' => $payment->id]);
        try {
            Mail::to($user->email)->send(new SelfServiceConfirmation($user, $reg, $payment, $password, $receiptUrl));
        } catch (\Throwable $e) {
        }

        return redirect()->route('portal.receipt');
    }

    public function receipt(Request $request)
    {
        $reg = $this->current($request);
        $payment = OnlinePayment::where('pending_registration_id', $reg->id)->latest()->firstOrFail();
        $service = Service::findOrFail($reg->service_id);
        return view('portal.receipt', compact('reg','payment','service'));
    }

    public function publicReceipt(OnlinePayment $payment)
    {
        $reg = PendingRegistration::findOrFail($payment->pending_registration_id);
        $service = Service::findOrFail($reg->service_id);
        return view('portal.receipt-public', compact('reg','payment','service'));
    }

    public function resume(Request $request, string $token)
    {
        $reg = PendingRegistration::where('resume_token', $token)->firstOrFail();
        $request->session()->put('portal_reg_id', $reg->id);
        return redirect()->route('portal.info');
    }

    private function current(Request $request): PendingRegistration
    {
        $id = $request->session()->get('portal_reg_id');
        abort_unless($id, 404);
        return PendingRegistration::findOrFail($id);
    }
}
