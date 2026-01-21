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
use App\Services\PaymentService;

class SelfServiceController extends Controller
{
    private function securityHeaders(): array
    {
        return [
            'X-Frame-Options' => 'DENY',
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'no-referrer',
            'X-XSS-Protection' => '1; mode=block',
            'Content-Security-Policy' => "default-src 'self' https:; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:;",
        ];
    }
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
            'payment_method' => ['required', 'in:initialize'],
        ], [
            //
        ]);
        if (! preg_match('/^\\+[1-9]\\d{1,14}$/', (string) ($reg->phone ?? ''))) {
            return redirect()->route('portal.pay')->withErrors(['phone' => 'Phone must be in E.164 format']);
        }
        $reference = Str::upper(Str::random(8));
        $successUrl = route('payment.callback.success');
        $failureUrl = route('payment.callback.failure');
        $payload = [
            'phone' => $reg->phone,
            'amount' => 0.01,
            'currency' => 'USD',
            'successUrl' => $successUrl,
            'cancelUrl' => $failureUrl,
            'order_info' => [
                'item_name' => $service->slug,
                'order_no' => $reference,
            ],
        ];
        try {
            Log::info('Portal Payment Initialize Attempt', [
                'reg_id' => $reg->id,
                'service_id' => $service->id,
                'phone' => $reg->phone,
                'amount' => 0.01,
                'reference' => $reference,
                'successUrl' => $successUrl,
                'cancelUrl' => $failureUrl,
            ]);
            $ps = new PaymentService();
            $result = $ps->createTransaction($payload);

            $approved = (string) ($result['approved_url'] ?? '');
            if (! $approved || ! filter_var($approved, FILTER_VALIDATE_URL)) {
                Log::warning('Portal Payment Initialize Missing Approved URL', [
                    'reg_id' => $reg->id,
                    'result' => $result,
                ]);
                return redirect()->route('portal.pay')->withErrors(['payment' => 'Payment initialization failed: Missing approval URL']);
            }
            $transactionId = (string) ($result['transaction_id'] ?? 'txn_' . Str::random(12));
            $payment = OnlinePayment::create([
                'pending_registration_id' => $reg->id,
                'provider' => 'somx',
                'payment_method' => $validated['payment_method'],
                'amount' => $service->price,
                'currency' => 'USD',
                'status' => 'initiated',
                'transaction_id' => $transactionId,
                'reference' => $reference,
                'receipt_number' => 'IPAMS-SRV-' . str_pad((string) $reg->id, 6, '0', STR_PAD_LEFT),
                'metadata' => [
                    'approved_url' => $approved,
                    'payload' => $payload,
                    'somx' => $result['raw'] ?? null,
                ],
            ]);
            Log::info('Portal Payment Initialize Succeeded', [
                'approved_url' => $approved,
                'transaction_id' => $transactionId,
                'payment_id' => $payment->id,
            ]);

            return redirect()->away($approved);
        } catch (\Throwable $e) {
            Log::error('Portal Payment Initialize Failed', [
                'error' => $e->getMessage(),
                'reg_id' => $reg->id,
                'payload' => $payload,
            ]);
            return redirect()->route('portal.pay')->withErrors(['payment' => 'Payment initialization failed: ' . $e->getMessage()]);
        }
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

    public function callbackSuccess(Request $request)
    {
        $method = $request->method();
        Log::info('Payment callback received (success)', [
            'method' => $method,
            'payload' => $request->all(),
            'ip' => $request->ip(),
            'ua' => substr((string) $request->userAgent(), 0, 255),
        ]);

        $transactionId = (string) $request->input('transactionId', $request->input('transaction_id', ''));
        $reference = (string) $request->input('order_no', $request->input('reference', ''));

        $payment = null;
        if ($transactionId !== '') {
            $payment = OnlinePayment::where('transaction_id', $transactionId)->latest()->first();
        }
        if (! $payment && $reference !== '') {
            $payment = OnlinePayment::where('reference', $reference)->latest()->first();
        }
        if (! $payment) {
            Log::warning('Payment not found on success callback', [
                'transactionId' => $transactionId,
                'reference' => $reference,
            ]);
            return response()->view('payment.failure', [
                'title' => 'Payment Not Found',
                'message' => 'We could not locate your payment. Please try again.',
                'errors' => ['Payment record missing'],
            ], 404)->withHeaders($this->securityHeaders());
        }
        if ($transactionId === '' && $payment) {
            $transactionId = (string) $payment->transaction_id;
        }

        $statusRaw = strtolower((string) $request->input('status', $request->input('transactionStatus', $request->input('state', ''))));
        $code = (string) ($request->input('responseCode', $request->input('response_code', '')));
        $ok = in_array($statusRaw, ['success', 'succeeded', 'approved', 'paid', 'completed', 'complete', 'processed'], true)
            || in_array($code, ['00', '0'], true);
        Log::info('Payment verification result (callback only)', [
            'transactionId' => $transactionId,
            'status' => $statusRaw,
            'response_code' => $code,
            'payload' => $request->all(),
        ]);
        $payment->status = $ok ? 'succeeded' : 'failed';
        $payment->verified_at = now();
        $meta = (array) $payment->metadata;
        $meta['gateway_status'] = $statusRaw;
        $meta['gateway_response_code'] = $code;
        $meta['callback'] = [
            'method' => $method,
            'payload' => $request->all(),
        ];
        $payment->metadata = $meta;
        $payment->save();

        if (! $ok) {
            return response()->view('payment.failure', [
                'title' => 'Payment Verification Failed',
                'message' => 'Your payment could not be verified.',
                'errors' => [$statusRaw ?: 'verification_failed'],
            ], 400)->withHeaders($this->securityHeaders());
        }

        $reg = PendingRegistration::find($payment->pending_registration_id);
        if ($reg) {
            request()->session()->put('portal_reg_id', $reg->id);
            $reg->update(['status' => 'paid', 'step' => 3]);
        }
        $receiptUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute('portal.receipt.public', now()->addDays(7), ['payment' => $payment->id]);
        $meta = (array) $payment->metadata;
        $meta['receipt_url'] = $receiptUrl;
        $payment->metadata = $meta;
        $payment->save();
        $next = route('portal.details');
        if ($reg) {
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
                $next = route($map[$reg->service_slug]);
            }
        }

        return redirect($next)->with(['success' => 'Payment successful', 'receipt_url' => $receiptUrl]);
    }

    public function callbackFailure(Request $request)
    {

        $method = $request->method();
        Log::warning('Payment callback received (failure)', [
            'method' => $method,
            'payload' => $request->all(),
            'ip' => $request->ip(),
            'ua' => substr((string) $request->userAgent(), 0, 255),
        ]);

        $transactionId = (string) $request->input('transactionId', $request->input('transaction_id', ''));
        $reference = (string) $request->input('order_no', $request->input('reference', ''));
        $errorMsg = (string) $request->input('error', $request->input('message', 'Payment was cancelled or failed.'));

        $payment = null;
        if ($transactionId !== '') {
            $payment = OnlinePayment::where('transaction_id', $transactionId)->latest()->first();
        }
        if (! $payment && $reference !== '') {
            $payment = OnlinePayment::where('reference', $reference)->latest()->first();
        }

        if ($payment) {
            $payment->status = 'failed';
            $meta = (array) $payment->metadata;
            $meta['failure'] = [
                'method' => $method,
                'payload' => $request->all(),
                'error' => $errorMsg,
            ];
            $payment->metadata = $meta;
            $payment->save();
        }

        $support = 'For assistance, contact support at support@example.com or try again.';
        $retryUrl = route('portal.pay');
        $homeUrl = route('landing.page.index');

        return response()->view('payment.failure', [
            'title' => 'Payment Failed',
            'message' => $errorMsg,
            'errors' => [$errorMsg],
            'retryUrl' => $retryUrl,
            'homeUrl' => $homeUrl,
            'support' => $support,
        ], 400)->withHeaders($this->securityHeaders());
    }
}
