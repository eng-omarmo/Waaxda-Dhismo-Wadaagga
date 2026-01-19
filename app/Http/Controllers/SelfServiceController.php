<?php

namespace App\Http\Controllers;

use App\Mail\SelfServiceConfirmation;
use App\Models\OnlinePayment;
use App\Models\PendingRegistration;
use App\Models\PendingRegistrationDocument;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class SelfServiceController extends Controller
{
    public function start()
    {
        // Service ID is required - if not provided, redirect to landing page
        $serviceIdParam = request()->query('serviceId', request()->query('service'));

        if ($serviceIdParam === null) {
            // No serviceId provided - redirect to landing page to select service
            return redirect()->route('landing.page.index')->with('info', 'Please select a service to continue.');
        }

        // Validate serviceId format
        if (! is_numeric($serviceIdParam) || (int) $serviceIdParam < 1) {
            Log::warning('Invalid serviceId format', [
                'endpoint' => 'portal.start',
                'serviceId' => $serviceIdParam,
                'ip' => request()->ip(),
                'ua' => substr((string) request()->userAgent(), 0, 255),
            ]);
            return redirect()->route('landing.page.index')->withErrors(['serviceId' => 'Invalid service ID. Please select a service from the homepage.']);
        }

        // Find service
        $service = Service::find((int) $serviceIdParam);
        if (! $service) {
            Log::warning('Service not found for serviceId', [
                'endpoint' => 'portal.start',
                'serviceId' => (int) $serviceIdParam,
                'ip' => request()->ip(),
                'ua' => substr((string) request()->userAgent(), 0, 255),
            ]);
            return redirect()->route('landing.page.index')->withErrors(['serviceId' => 'Service not found. Please select a service from the homepage.']);
        }

        // Create pending registration and go directly to info step
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
        request()->session()->put('portal_reg_id', $reg->id);

        return redirect()->route('portal.info');
    }

    public function storeService(Request $request)
    {
        // Legacy route - redirect to start with serviceId in query
        $serviceIdParam = $request->input('serviceId', $request->input('service_id'));

        if (! $serviceIdParam || ! is_numeric($serviceIdParam) || (int) $serviceIdParam < 1) {
            return redirect()->route('landing.page.index')->withErrors(['serviceId' => 'Invalid service ID. Please select a service from the homepage.']);
        }

        // Redirect to start with serviceId in query string (which will create reg and go to info)
        return redirect()->route('portal.start', ['serviceId' => $serviceIdParam]);
    }

    public function info(Request $request)
    {
        $reg = $this->current($request);

        $service = $reg->service_id ? Service::find($reg->service_id) : null;

        return view('portal.info', ['reg' => $reg, 'service' => $service]);
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
        if ($reg->service_slug === 'project-registration' || $reg->service_slug === 'construction-permit-application') {
            $details = $request->validate([
                'project_name' => ['required', 'string', 'max:255'],
                'location_text' => ['required', 'string', 'max:255'],
            ]);
            $data['project_name'] = $details['project_name'];
            $data['location_text'] = $details['location_text'];
        }
        $reg->update([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'data' => $data,
            'step' => 5,
        ]);
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
        if ($request->filled('payment_method')) {
            return $this->processPay($request);
        }
        return redirect()->route('portal.info');
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

        return view('portal.docs', compact('reg', 'docs'));
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
        if (! $reg->service_id) {
            $serviceIdParam = $request->input('serviceId', $request->query('serviceId', $request->query('service')));
            if (! $serviceIdParam || ! is_numeric($serviceIdParam) || (int) $serviceIdParam < 1) {
                Log::warning('Missing or invalid serviceId before pay', [
                    'endpoint' => 'portal.pay',
                    'serviceId' => $serviceIdParam,
                    'reg_id' => $reg->id,
                    'ip' => $request->ip(),
                    'ua' => substr((string) $request->userAgent(), 0, 255),
                ]);
                throw new HttpResponseException(response('Missing or invalid serviceId', 400));
            }
            $service = Service::find((int) $serviceIdParam);
            if (! $service) {
                Log::warning('Service not found before pay', [
                    'endpoint' => 'portal.pay',
                    'serviceId' => (int) $serviceIdParam,
                    'reg_id' => $reg->id,
                    'ip' => $request->ip(),
                    'ua' => substr((string) $request->userAgent(), 0, 255),
                ]);
                throw new HttpResponseException(response('Missing or invalid serviceId', 400));
            }
            $reg->update([
                'service_id' => $service->id,
                'service_slug' => $service->slug,
            ]);
        }
        $service = Service::findOrFail($reg->service_id);

        return view('portal.pay', ['reg' => $reg, 'service' => $service]);
    }

    public function processPay(Request $request)
    {
        $reg = $this->current($request);
        $service = Service::findOrFail($reg->service_id);
        $validated = $request->validate([
            'payment_method' => ['required', 'in:initialize,manual'],
        ], [
            //
        ]);
        $provider = $validated['payment_method'] === 'manual' ? 'manual' : 'initialize';
        $status = 'initiated';
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
            'metadata' => [],
        ]);
        $reg->update(['status' => 'paid', 'step' => 3]);

        $password = $reg->data['password_plain'] ?? Str::random(12);
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

        $sr = ServiceRequest::firstOrCreate(
            [
                'service_id' => $service->id,
                'user_email' => $reg->email,
                'status' => 'pending',
            ],
            [
                'user_id' => Auth::id(),
                'user_full_name' => $reg->full_name ?: ($user->first_name.' '.$user->last_name),
                'user_phone' => $reg->phone ?? null,
                'user_national_id' => $reg->data['national_id'] ?? null,
                'request_details' => [
                    'registration_id' => $reg->id,
                    'online_payment_id' => $payment->id,
                    'transaction_id' => $payment->transaction_id,
                    'reference' => $payment->reference,
                    'transaction_reference' => $payment->reference,
                    'amount' => $payment->amount,
                    'source' => 'portal_initialize',
                ],
            ]
        );
        if (! $sr->wasRecentlyCreated) {
            $sr->request_details = array_merge((array) $sr->request_details, [
                'online_payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'reference' => $payment->reference,
                'transaction_reference' => $payment->reference,
                'amount' => $payment->amount,
                'updated_at' => now()->toDateTimeString(),
            ]);
            $sr->save();
        }

        \App\Models\ManualOperationLog::create([
            'user_id' => Auth::id(),
            'action' => 'portal_payment',
            'target_type' => 'PendingRegistration',
            'target_id' => (string) $reg->id,
            'details' => [
                'service_id' => $reg->service_id,
                'amount' => $payment->amount,
                'provider' => $payment->provider,
            ],
        ]);

        if ($reg->service_slug === 'construction-permit-application') {
            return redirect()->route('services.construction-permit', ['payment' => $payment->id]);
        }

        $map = [
            'project-registration' => 'services.project-registration',
            'construction-permit-application' => 'services.construction-permit',
            'developer-registration' => 'services.developer-registration',
            'business-license' => 'services.business-license',
            'ownership-certificate' => 'services.ownership-certificate',
            'ownership-transfer' => 'services.ownership-transfer',
            'property-transfer-services' => 'services.ownership-transfer',
        ];
        if (isset($map[$reg->service_slug])) {
            return redirect()->route($map[$reg->service_slug]);
        }

        return redirect()->route('portal.details');
    }

    public function receipt(Request $request)
    {
        $reg = $this->current($request);
        $payment = OnlinePayment::where('pending_registration_id', $reg->id)->latest()->firstOrFail();
        $service = Service::findOrFail($reg->service_id);

        return view('portal.receipt', compact('reg', 'payment', 'service'));
    }

    public function publicReceipt(OnlinePayment $payment)
    {
        $reg = PendingRegistration::findOrFail($payment->pending_registration_id);
        $service = Service::findOrFail($reg->service_id);

        return view('portal.receipt-public', compact('reg', 'payment', 'service'));
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
        if (! $id) {
            $token = (string) $request->query('token', '');
            if ($token !== '') {
                $reg = PendingRegistration::where('resume_token', $token)->first();
                if ($reg) {
                    $request->session()->put('portal_reg_id', $reg->id);

                    return $reg;
                }
            }
            throw new HttpResponseException(redirect()->route('portal.start'));
        }

        return PendingRegistration::findOrFail($id);
    }
}
