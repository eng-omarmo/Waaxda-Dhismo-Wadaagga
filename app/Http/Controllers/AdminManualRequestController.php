<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\BusinessLicense;
use App\Models\Certificate;
use App\Models\ManualOperationLog;
use App\Models\Organization;
use App\Models\OwnerProfile;
use App\Models\PaymentVerification;
use App\Models\Project;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Support\StandardIdentifier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

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
        $perPage = min(max((int) $request->query('per_page', 10), 1), 100);
        $requests = $query->latest()->paginate($perPage)->withQueryString();
        $statuses = ['pending', 'verified', 'rejected', 'discrepancy'];

        return view('admin.manual.requests.index', compact('requests', 'statuses'));
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

        $service = Service::find($sr->service_id);
        $schema = $this->buildFormSchema($service);
        $details = (array) $sr->request_details;
        $details['form_schema'] = $schema;
        $details['form_status'] = 'open';
        $details['form_values'] = [];
        $details['form_audit'] = [];
        $sr->request_details = $details;
        $sr->save();

        ManualOperationLog::create([
            'user_id' => Auth::id(),
            'action' => 'create_request',
            'target_type' => 'ServiceRequest',
            'target_id' => (string) $sr->id,
            'details' => ['service_id' => $sr->service_id],
        ]);

        ManualOperationLog::create([
            'user_id' => Auth::id(),
            'action' => 'form_generated',
            'target_type' => 'ServiceRequest',
            'target_id' => (string) $sr->id,
            'details' => ['schema_title' => $schema['title'] ?? 'Data Collection Form'],
        ]);

        return redirect()->route('admin.manual-requests.form', $sr)->with('status', 'Request created and form generated');
    }

    public function show(ServiceRequest $manual_request)
    {
        $manual_request->load('service', 'payments');

        return view('admin.manual.requests.show', ['request' => $manual_request]);
    }

    public function form(ServiceRequest $manual_request)
    {
        $manual_request->load('service');
        $details = (array) $manual_request->request_details;
        $schema = (array) ($details['form_schema'] ?? []);
        if (empty($schema)) {
            $schema = $this->buildFormSchema($manual_request->service);
            $details['form_schema'] = $schema;
            $manual_request->request_details = $details;
            $manual_request->save();
        }
        $values = (array) ($details['form_values'] ?? []);

        return view('admin.manual.requests.form', [
            'request' => $manual_request,
            'schema' => $schema,
            'values' => $values,
        ]);
    }

    public function submitForm(Request $request, ServiceRequest $manual_request)
    {
        $manual_request->load('service');
        $details = (array) $manual_request->request_details;
        $schema = (array) ($details['form_schema'] ?? []);
        $fields = (array) ($schema['fields'] ?? []);
        $rules = [];
        foreach ($fields as $f) {
            $name = (string) ($f['name'] ?? '');
            if ($name === '') {
                continue;
            }
            $base = match ($f['type'] ?? 'text') {
                'email' => 'email',
                'number' => 'numeric',
                'date' => 'date',
                'select' => 'string',
                default => 'string',
            };
            $rule = [$base, 'max:2000'];
            if (! empty($f['required'])) {
                array_unshift($rule, 'required');
            } else {
                array_unshift($rule, 'nullable');
            }
            $rules["form_values.$name"] = $rule;
        }
        $validated = $request->validate($rules);
        $newValues = (array) ($validated['form_values'] ?? []);
        $oldValues = (array) ($details['form_values'] ?? []);
        $changes = [];
        foreach ($newValues as $k => $v) {
            $prev = $oldValues[$k] ?? null;
            if ((string) $prev !== (string) $v) {
                $changes[$k] = ['from' => $prev, 'to' => $v];
            }
        }
        $details['form_values'] = $newValues;
        $audit = (array) ($details['form_audit'] ?? []);
        $audit[] = [
            'submitted_by' => Auth::id(),
            'submitted_at' => now()->toDateTimeString(),
            'changes' => $changes,
            'values' => $newValues,
        ];
        $details['form_audit'] = $audit;
        $manual_request->request_details = $details;
        $manual_request->save();

        ManualOperationLog::create([
            'user_id' => Auth::id(),
            'action' => 'form_submitted',
            'target_type' => 'ServiceRequest',
            'target_id' => (string) $manual_request->id,
            'details' => ['fields_changed' => array_keys($changes)],
        ]);

        return redirect()->route('admin.manual-requests.show', $manual_request)->with('status', 'Form submitted and linked to request');
    }

    private function buildFormSchema(?Service $service): array
    {
        $slug = $service?->slug ?? '';
        $title = 'Data Collection Form';
        $instructions = 'Complete all required fields. Review for accuracy before submission.';
        $fields = [];
        if ($slug === 'project-registration') {
            $title = 'Project Registration – Data Collection';
            $fields = [
                ['name' => 'project_name', 'label' => 'Project Name', 'type' => 'text', 'required' => true],
                ['name' => 'location_text', 'label' => 'Location', 'type' => 'text', 'required' => true],
                ['name' => 'developer_name', 'label' => 'Developer Name', 'type' => 'text', 'required' => false],
                ['name' => 'registrant_national_id', 'label' => 'Registrant National ID', 'type' => 'text', 'required' => true],
            ];
        } elseif ($slug === 'business-license') {
            $title = 'Business License – Data Collection';
            $fields = [
                ['name' => 'company_name', 'label' => 'Company Name', 'type' => 'text', 'required' => true],
                ['name' => 'license_type', 'label' => 'License Type', 'type' => 'select', 'options' => ['Rental', 'Commercial'], 'required' => true],
                ['name' => 'registrant_email', 'label' => 'Registrant Email', 'type' => 'email', 'required' => true],
                ['name' => 'registrant_phone', 'label' => 'Registrant Phone', 'type' => 'text', 'required' => false],
            ];
        } elseif ($slug === 'construction-permit-application' || $slug === 'construction-permit') {
            $title = 'Construction Permit – Data Collection';
            $fields = [
                ['name' => 'applicant_full_name', 'label' => 'Applicant Full Name', 'type' => 'text', 'required' => true],
                ['name' => 'applicant_role', 'label' => 'Applicant Role', 'type' => 'select', 'options' => ['Owner', 'Legal Representative', 'Developer'], 'required' => true],
                ['name' => 'plot_number', 'label' => 'Plot Number', 'type' => 'text', 'required' => true],
                ['name' => 'land_title_number', 'label' => 'Land Title Number', 'type' => 'text', 'required' => false],
                ['name' => 'land_size_sqm', 'label' => 'Land Size (sqm)', 'type' => 'number', 'required' => true],
                ['name' => 'land_location_district', 'label' => 'Location District', 'type' => 'text', 'required' => true],
            ];
        } elseif ($slug === 'developer-registration' || $slug === 'organization-registration') {
            $title = 'Organization Registration – Data Collection';
            $fields = [
                ['name' => 'organization_name', 'label' => 'Organization Name', 'type' => 'text', 'required' => true],
                ['name' => 'registration_number', 'label' => 'Registration Number', 'type' => 'text', 'required' => false],
                ['name' => 'contact_email', 'label' => 'Contact Email', 'type' => 'email', 'required' => true],
                ['name' => 'contact_phone', 'label' => 'Contact Phone', 'type' => 'text', 'required' => true],
            ];
        } elseif ($slug === 'ownership-certificate') {
            $title = 'Ownership Certificate – Data Collection';
            $fields = [
                ['name' => 'apartment_number', 'label' => 'Apartment Number', 'type' => 'text', 'required' => true],
                ['name' => 'owner_name', 'label' => 'Owner Name', 'type' => 'text', 'required' => true],
                ['name' => 'owner_national_id', 'label' => 'Owner National ID', 'type' => 'text', 'required' => true],
            ];
        } elseif ($slug === 'property-transfer-services' || $slug === 'ownership-transfer') {
            $title = 'Ownership Transfer – Data Collection';
            $fields = [
                ['name' => 'previous_owner_name', 'label' => 'Previous Owner Name', 'type' => 'text', 'required' => true],
                ['name' => 'previous_owner_id', 'label' => 'Previous Owner ID', 'type' => 'text', 'required' => true],
                ['name' => 'new_owner_name', 'label' => 'New Owner Name', 'type' => 'text', 'required' => true],
                ['name' => 'new_owner_id', 'label' => 'New Owner ID', 'type' => 'text', 'required' => true],
                ['name' => 'transfer_reason', 'label' => 'Transfer Reason', 'type' => 'select', 'options' => ['Sale', 'Inheritance', 'Gift'], 'required' => true],
            ];
        } else {
            $fields = [
                ['name' => 'notes', 'label' => 'Notes', 'type' => 'text', 'required' => false],
            ];
        }

        return [
            'title' => $title,
            'instructions' => $instructions,
            'fields' => $fields,
        ];
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
            try {
                $this->generateCertificate($request, $manual_request);
            } catch (\Throwable $e) {
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

        try {
            $this->generateCertificate($request, $manual_request);
        } catch (\Throwable $e) {
        }

        try {
            Mail::to($manual_request->user_email)->send(new \App\Mail\ServiceRequestVerified($manual_request, $payment));
        } catch (\Throwable $e) {
        }

        return redirect()->route('admin.manual-requests.show', $manual_request)->with('status', 'Payment reconciled and user notified');
    }

    public function generateCertificate(Request $request, ServiceRequest $manual_request)
    {
        if (! auth()->check()) {
            abort(403);
        }
        if ($manual_request->status !== 'verified') {
            return redirect()->route('admin.manual-requests.show', $manual_request)->with('error', 'Payment must be verified before generating certificate');
        }
        $pv = $manual_request->payments()->where('status', 'verified')->latest()->first();
        if (! $pv) {
            return redirect()->route('admin.manual-requests.show', $manual_request)->with('error', 'No verified payment found');
        }
        $service = $manual_request->service;
        if (! $service) {
            return redirect()->route('admin.manual-requests.show', $manual_request)->with('error', 'Service not found for request');
        }

        $classification = match ($service->slug) {
            'project-registration' => 'Project Registration',
            'developer-registration' => 'Organization Registration',
            'property-transfer-services' => 'Property Transfer',
            'business-license' => 'Business License',
            'construction-permit-application', 'construction-permit' => 'Construction Permit',
            default => 'Service Certificate',
        };

        $details = (array) $manual_request->request_details;
        $rows = [];
        $rows[] = ['Service', $service->name];
        $rows[] = ['Applicant', $manual_request->user_full_name];
        $rows[] = ['Email', $manual_request->user_email];
        if ($manual_request->user_phone) {
            $rows[] = ['Phone', $manual_request->user_phone];
        }
        $rows[] = ['Payment Reference', $pv->reference_number];
        foreach ($details as $k => $v) {
            $label = strtoupper(str_replace(['_', '-'], ' ', (string) $k));
            $val = is_scalar($v) ? (string) $v : json_encode($v);
            $rows[] = [$label, $val];
        }
        $entityDetailsHtml = '<table style="width:100%;border-collapse:collapse">';
        foreach ($rows as $row) {
            $entityDetailsHtml .= '<tr><td style="padding:6px;border:1px solid #ddd"><strong>'.e($row[0]).'</strong></td><td style="padding:6px;border:1px solid #ddd">'.e($row[1]).'</td></tr>';
        }
        $entityDetailsHtml .= '</table>';

        $uid = (string) Str::uuid();
        $number = 'IPAMS-COC-'.date('Y').'-'.str_pad((string) $manual_request->id, 6, '0', STR_PAD_LEFT).'-'.$service->id.'-'.substr($uid, 0, 8);
        $standardId = StandardIdentifier::normalize('other', 'SR-'.$manual_request->id);
        $issuedAt = now()->toDateString();
        $title = $service->name.' Certificate';
        $hash = hash('sha256', implode('|', [
            $uid,
            $number,
            (string) $service->id,
            (string) $issuedAt,
            (string) $title,
            (string) $standardId,
            (string) $pv->reference_number,
        ]));

        [$receiverType, $receiverId] = $this->persistDomainEntity($manual_request);

        $certificate = Certificate::create([
            'receiver_type' => $receiverType,
            'receiver_id' => $receiverId,
            'service_id' => $service->id,
            'certificate_number' => $number,
            'certificate_uid' => $uid,
            'issued_at' => $issuedAt,
            'issued_by' => Auth::id(),
            'issued_to' => $manual_request->user_full_name,
            'certificate_hash' => $hash,
            'status' => 'valid',
            'metadata' => [
                'title' => $title,
                'fields' => [
                    'standardized_id' => $standardId,
                    'entity_classification' => $classification,
                    'entity_details_html' => $entityDetailsHtml,
                    'officer_signature_name' => optional(Auth::user())->first_name.' '.optional(Auth::user())->last_name,
                    'payment_reference' => $pv->reference_number,
                ],
                'service_request_id' => $manual_request->id,
                'payment_id' => $pv->id,
                'format_options' => ['pdf' => true],
            ],
        ]);

        $verificationLink = URL::signedRoute('certificate.public', ['certificate' => $certificate]);
        $qrSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="120" height="120"><rect width="120" height="120" fill="#fff" stroke="#000"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-size="8">SCAN</text></svg>';
        $meta = (array) $certificate->metadata;
        $fields = (array) ($meta['fields'] ?? []);
        $fields['verification_link'] = $verificationLink;
        $fields['qr_svg'] = $qrSvg;
        $meta['fields'] = $fields;
        $meta['verification_link'] = $verificationLink;
        $certificate->metadata = $meta;
        $certificate->save();

        $html = '<div class="p-4" style="font-family:Arial,sans-serif"><div class="d-flex align-items-center mb-3"><h3 class="mb-0" style="margin:0;padding:0">'.e($title).'</h3></div><div class="mb-2">Service: '.e($service->name).'</div><div class="mb-2">Date: '.e($issuedAt).'</div><div class="mb-2">UID: '.e($uid).'</div><div class="mb-2">Standardized ID: '.e($standardId).'</div><hr><div class="mb-2"><strong>Entity Classification</strong>: '.e($classification).'</div><div class="mb-3"><strong>Details</strong></div>'.$entityDetailsHtml.'<hr><div class="d-flex align-items-center gap-3"><div>'.$qrSvg.'</div><div style="font-size:12px">Verify: '.e($verificationLink).'</div></div><div class="mt-3" style="font-size:12px">Authorizing Officer: '.e($fields['officer_signature_name'] ?? 'Officer').'</div></div>';
        $pdfData = Pdf::loadHTML($html)->setPaper('a4')->output();
        $dir = 'certificates';
        $filename = $number.'.pdf';
        $path = $dir.'/'.$filename;
        Storage::disk('local')->put($path, $pdfData);
        $certificate->metadata = array_merge($certificate->metadata ?? [], ['archived_pdf_path' => $path]);
        $certificate->save();

        ManualOperationLog::create([
            'user_id' => Auth::id(),
            'action' => 'issue_certificate_manual',
            'target_type' => 'ServiceRequest',
            'target_id' => (string) $manual_request->id,
            'details' => ['certificate_id' => $certificate->id, 'payment_id' => $pv->id],
        ]);

        return redirect()->route('admin.manual-requests.show', $manual_request)->with('status', 'Certificate generated successfully');
    }

    private function persistDomainEntity(ServiceRequest $manual_request): array
    {
        $service = $manual_request->service;
        $details = (array) $manual_request->request_details;
        $values = (array) ($details['form_values'] ?? []);
        $type = null;
        $id = null;
        switch ($service->slug) {
            case 'project-registration':
                $proj = Project::where('registrant_email', $manual_request->user_email)
                    ->orWhere('registrant_phone', $manual_request->user_phone)
                    ->latest()->first();
                if (! $proj) {
                    $proj = Project::create([
                        'project_name' => (string) ($values['project_name'] ?? 'New Project'),
                        'location_text' => (string) ($values['location_text'] ?? 'Unknown'),
                        'developer_id' => null,
                        'status' => 'Approved',
                        'registrant_name' => $manual_request->user_full_name,
                        'registrant_phone' => $manual_request->user_phone,
                        'registrant_email' => $manual_request->user_email,
                    ]);
                }
                $type = Project::class;
                $id = $proj->id;
                break;
            case 'business-license':
                $bl = BusinessLicense::where('registrant_email', $manual_request->user_email)
                    ->orWhere('registrant_phone', $manual_request->user_phone)
                    ->latest()->first();
                if (! $bl) {
                    $bl = BusinessLicense::create([
                        'company_name' => (string) ($values['company_name'] ?? 'Company'),
                        'project_id' => null,
                        'license_type' => (string) ($values['license_type'] ?? 'Commercial'),
                        'status' => 'approved',
                        'verification_status' => 'verified',
                        'expires_at' => null,
                        'admin_comments' => null,
                        'registrant_name' => $manual_request->user_full_name,
                        'registrant_email' => $manual_request->user_email,
                        'registrant_phone' => $manual_request->user_phone,
                        'approved_by' => Auth::id(),
                        'approved_at' => now(),
                    ]);
                }
                $type = BusinessLicense::class;
                $id = $bl->id;
                break;
            case 'developer-registration':
            case 'organization-registration':
                $org = Organization::where('contact_email', $manual_request->user_email)
                    ->orWhere('contact_phone', $manual_request->user_phone)
                    ->latest()->first();
                if (! $org) {
                    $org = Organization::create([
                        'name' => (string) ($values['organization_name'] ?? 'Organization'),
                        'registration_number' => (string) ($values['registration_number'] ?? ''),
                        'address' => (string) ($values['address'] ?? 'N/A'),
                        'type' => 'Developer',
                        'contact_full_name' => $manual_request->user_full_name,
                        'contact_role' => 'Representative',
                        'contact_phone' => $manual_request->user_phone,
                        'contact_email' => $manual_request->user_email,
                        'status' => 'approved',
                        'admin_notes' => null,
                    ]);
                }
                $type = Organization::class;
                $id = (string) $org->id;
                break;
            case 'ownership-certificate':
            case 'property-transfer-services':
                $apt = Apartment::where('contact_phone', $manual_request->user_phone)->latest()->first();
                if (! $apt) {
                    $owner = OwnerProfile::firstOrCreate(
                        ['national_id' => (string) ($values['owner_national_id'] ?? '')],
                        [
                            'full_name' => (string) ($values['owner_name'] ?? $manual_request->user_full_name),
                            'tax_id_number' => null,
                            'contact_phone' => $manual_request->user_phone,
                            'contact_email' => $manual_request->user_email,
                            'address_text' => (string) ($values['owner_address'] ?? ''),
                        ]
                    );
                    $apt = Apartment::create([
                        'name' => (string) ($values['apartment_number'] ?? 'Apartment'),
                        'address_city' => (string) ($values['address_city'] ?? ''),
                        'contact_name' => $manual_request->user_full_name,
                        'contact_phone' => $manual_request->user_phone,
                        'contact_email' => $manual_request->user_email,
                        'notes' => null,
                        'owner_profile_id' => $owner->id,
                    ]);
                }
                $type = Apartment::class;
                $id = (string) $apt->id;
                break;
        }

        return [$type, $id];
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
