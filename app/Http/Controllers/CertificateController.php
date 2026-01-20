<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\BusinessLicense;
use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\ManualOperationLog;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Support\StandardIdentifier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificateController extends Controller
{
    public function index(Request $request)
    {

        $certs = Certificate::with(['receiver', 'service'])->latest()->paginate(10)->withQueryString();

        return view('admin.certificates.index', compact('certs'));
    }

    public function create()
    {
        $services = Service::orderBy('name')->get();

        return view('admin.certificates.create', compact('services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'project_id' => ['required', 'string', 'exists:projects,id'],
            'certificate_title' => ['required', 'string', 'max:255'],
            'issued_at' => ['required', 'date'],
            'template_slug' => ['nullable', 'string', 'max:255'],
            'fields' => ['nullable', 'array'],
            'identifier_type' => ['nullable', 'in:project,license,permit,other'],
            'identifier_value' => ['nullable', 'string', 'max:64'],
        ]);

        $service = Service::findOrFail($request->service_id);
        $project = Project::findOrFail($request->project_id);

        if ($request->filled('identifier_type') && ! StandardIdentifier::validateType($request->identifier_type)) {
            return back()->withErrors(['identifier_type' => 'Unsupported identifier type'])->withInput();
        }
        if ($request->filled('identifier_type') && ! $request->filled('identifier_value')) {
            return back()->withErrors(['identifier_value' => 'Identifier value is required'])->withInput();
        }
        if ($request->filled('identifier_type') && ! StandardIdentifier::validate($request->identifier_type, $request->identifier_value)) {
            return back()->withErrors(['identifier_value' => 'Invalid identifier format'])->withInput();
        }
        if (StandardIdentifier::conflict($project->id, $request->identifier_type, $request->identifier_value)) {
            return back()->withErrors(['identifier_value' => 'Conflicting identifier: project ID mismatch'])->withInput();
        }
        $standardId = StandardIdentifier::compute($project->id, $request->identifier_type, $request->identifier_value);
        if (! $standardId) {
            return back()->withErrors(['identifier_value' => 'Missing required identifier'])->withInput();
        }

        $template = null;
        if ($request->filled('template_slug')) {
            $template = CertificateTemplate::where('template_slug', $request->template_slug)
                ->where(function ($q) use ($service) {
                    $q->whereNull('service_id')->orWhere('service_id', $service->id);
                })
                ->first();
        } else {
            $template = CertificateTemplate::where('service_id', $service->id)->first();
        }

        $fields = $request->input('fields', []);
        $uid = (string) Str::uuid();
        $number = 'IPAMS-COC-'.date('Y').'-'.substr($project->id, 0, 8).'-'.$service->id.'-'.substr($uid, 0, 8);
        $hash = hash('sha256', implode('|', [
            $uid,
            $number,
            $project->id,
            (string) $service->id,
            (string) $request->issued_at,
            (string) $request->certificate_title,
            (string) $standardId,
        ]));

        $certificate = Certificate::create([
            'receiver_type' => \App\Models\Project::class,
            'receiver_id' => $project->id,
            'service_id' => $service->id,
            'certificate_number' => $number,
            'certificate_uid' => $uid,
            'issued_at' => $request->issued_at,
            'issued_by' => Auth::id(),
            'certificate_hash' => $hash,
            'metadata' => [
                'title' => $request->certificate_title,
                'template_slug' => $template?->template_slug,
                'fields' => array_merge($fields, ['standardized_id' => $standardId]),
                'branding' => $template?->branding ?? ['theme' => 'default'],
                'format_options' => $template?->format_options ?? ['pdf' => true, 'png' => true],
            ],
        ]);

        $html = ($template?->html_template) ?: '<!-- Production-ready Certificate Template: Default Fallback --><div style="font-family:Arial,sans-serif;color:#222"><style>@page{size:A4;margin:20mm} .wrap{border:6px solid #0a5ad1;padding:24px} .header{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px} .logo{width:64px;height:64px;object-fit:contain} .svc{font-size:12pt;color:#555} .title{font-size:28pt;font-weight:700;text-align:center;color:#0a5ad1;margin:12px 0 4px} .subtitle{text-align:center;font-size:11pt;color:#666;margin-bottom:16px} .recipient{font-size:18pt;font-weight:600;text-align:center;margin:18px 0 8px} .achievement{text-align:center;font-size:12pt;color:#333;margin-bottom:16px} .description{font-size:12pt;line-height:1.6;margin:12px 0 18px} .details{margin-top:8px} .signatures{display:flex;gap:24px;justify-content:space-between;margin-top:24px} .sig{flex:1;text-align:center} .sig .line{border-top:1px solid #999;margin-top:40px;padding-top:8px} .footer{display:flex;justify-content:space-between;align-items:center;margin-top:18px;font-size:10pt;color:#555} .badge{border:1px solid #0a5ad1;padding:4px 8px;border-radius:4px} </style><!-- Header: service and logo --><div class="wrap" style="border-color: {{brand_primary}}"><div class="header"><div class="svc">{{service}}</div><img class="logo" src="{{logo_url}}" alt="Logo"/></div><!-- Title and organization --><div class="title" style="color: {{brand_primary}}">{{title}}</div><div class="subtitle">{{organization_name}}</div><!-- Recipient section --><div class="recipient">{{recipient_name}}</div><div class="achievement">{{recipient_achievement}}</div><!-- Accomplishment description --><div class="description">{{accomplishment_description}}</div><!-- Optional classification and detailed table from existing logic --><div class="details"><div style="font-weight:600">{{entity_classification}}</div><div>{{entity_details_html}}</div></div><!-- Authorized signatures --><div class="signatures"><div class="sig"><div class="line">{{signature_1_name}}</div><div>{{signature_1_title}}</div></div><div class="sig"><div class="line">{{signature_2_name}}</div><div>{{signature_2_title}}</div></div></div><!-- Footer: issuance date and UID --><div class="footer"><div>Issued {{date}}</div><div class="badge">UID {{uid}}</div></div><!-- Optional standardized ID and verification --><div style="margin-top:8px;font-size:10pt">ID: {{standardized_id}}</div><div style="display:flex;align-items:center;gap:8px;margin-top:8px"><div>{{qr_svg}}</div><div>Verify: {{verification_link}}</div></div><!-- Officer signature (legacy field) --><div style="margin-top:12px;font-size:10pt">Authorizing Officer: {{officer_signature_name}}</div></div></div>';
        $replacements = [
            '{{title}}' => (string) $request->certificate_title,
            '{{service}}' => (string) $service->name,
            '{{project}}' => (string) $project->id,
            '{{date}}' => (string) $request->issued_at,
            '{{uid}}' => (string) $uid,
            '{{standardized_id}}' => (string) $standardId,
        ];
        $rendered = strtr($html, $replacements);
        foreach ($fields as $k => $v) {
            $rendered = str_replace('{{'.$k.'}}', (string) $v, $rendered);
        }
        $pdfData = Pdf::loadHTML($rendered)->setPaper('a4')->output();
        $dir = 'certificates';
        $filename = $number.'.pdf';
        $path = $dir.'/'.$filename;
        Storage::disk('local')->put($path, $pdfData);
        $certificate->metadata = array_merge($certificate->metadata ?? [], ['archived_pdf_path' => $path]);
        $certificate->save();

        ManualOperationLog::create([
            'user_id' => Auth::id(),
            'action' => 'create_certificate_manual',
            'target_type' => 'Project',
            'target_id' => (string) $project->id,
            'details' => ['certificate_id' => $certificate->id, 'service_id' => $service->id],
        ]);

        return redirect()->route('admin.certificates.show', $certificate)->with('status', 'Certificate generated');
    }

    public function generateFromPhone(Request $request)
    {
        $request->validate([
            'user_phone' => ['required', 'string', 'max:50'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'service_name' => ['nullable', 'string', 'max:100'],
        ]);
        $phone = $request->string('user_phone')->toString();
        $service = null;
        if ($request->filled('service_id')) {
            $service = Service::findOrFail($request->service_id);
        } elseif ($request->filled('service_name')) {
            $service = Service::where('name', $request->string('service_name')->toString())->first();
            if (! $service) {
                return back()->withErrors(['service_name' => 'Unknown service'])->withInput();
            }
        } else {
            return back()->withErrors(['service_id' => 'Service is required'])->withInput();
        }

        $projects = Project::where('registrant_phone', $phone)->orderBy('created_at', 'desc')->get();
        $licenses = BusinessLicense::where('registrant_phone', $phone)->orderBy('created_at', 'desc')->get();
        $apartments = Apartment::where('contact_phone', $phone)->orderBy('created_at', 'desc')->get();
        $organizations = Organization::where('contact_phone', $phone)->orderBy('created_at', 'desc')->get();
        $requests = ServiceRequest::where('user_phone', $phone)->with('service')->orderBy('created_at', 'desc')->get();

        $anchorProject = $projects->first();
        if (! $anchorProject && $licenses->first()?->project_id) {
            $anchorProject = Project::find($licenses->first()->project_id);
        }
        if (! $anchorProject) {
            return back()->withErrors(['user_phone' => 'No project found for this phone'])->withInput();
        }

        $classification = match ($service->slug) {
            'business-license' => 'Business License Registration',
            'project-registration' => 'Project Registration',
            'ownership-certificate' => 'Apartment Ownership',
            'property-transfer-services' => 'Apartment Transfer',
            'construction-permit-application', 'construction-permit' => 'Apartment Construction Permit',
            default => 'Other Legal Entity',
        };

        $license = $licenses->first();
        $apartment = $apartments->first();
        $organization = $organizations->first();
        $serviceReq = $requests->first();

        $detailsRows = [];
        $detailsRows[] = ['Registration Number', $anchorProject->id];
        $detailsRows[] = ['Entity Name', $anchorProject->project_name];
        $detailsRows[] = ['Classification', $classification];
        if ($license) {
            $detailsRows[] = ['License ID', $license->id];
            $detailsRows[] = ['License Type', $license->license_type];
            $detailsRows[] = ['License Status', ucfirst($license->status)];
            $detailsRows[] = ['License Expires', $license->expires_at?->toDateString() ?: '—'];
        }
        if ($apartment) {
            $detailsRows[] = ['Apartment Contact', $apartment->contact_name.' ('.$apartment->contact_phone.')'];
            $detailsRows[] = ['Apartment City', $apartment->address_city];
        }
        if ($organization) {
            $detailsRows[] = ['Organization', $organization->name];
            $detailsRows[] = ['Org Registration', $organization->registration_number ?: '—'];
        }
        if ($serviceReq) {
            $detailsRows[] = ['Service Request', ($serviceReq->service?->name ?? 'Service').' • '.$serviceReq->status];
        }
        $entityDetailsHtml = '<table style="width:100%;border-collapse:collapse">';
        foreach ($detailsRows as $row) {
            $entityDetailsHtml .= '<tr><td style="padding:6px;border:1px solid #ddd"><strong>'.e($row[0]).'</strong></td><td style="padding:6px;border:1px solid #ddd">'.e($row[1]).'</td></tr>';
        }
        $entityDetailsHtml .= '</table>';

        $uid = (string) Str::uuid();
        $number = 'IPAMS-COC-'.date('Y').'-'.substr($anchorProject->id, 0, 8).'-'.$service->id.'-'.substr($uid, 0, 8);
        $standardId = StandardIdentifier::normalize('project', $anchorProject->id);
        $issuedAt = now()->toDateString();
        $title = $service->name.' Certificate';
        $hash = hash('sha256', implode('|', [
            $uid,
            $number,
            $anchorProject->id,
            (string) $service->id,
            (string) $issuedAt,
            (string) $title,
            (string) $standardId,
        ]));

        $certificate = Certificate::create([
            'receiver_type' => \App\Models\Project::class,
            'receiver_id' => $anchorProject->id,
            'service_id' => $service->id,
            'certificate_number' => $number,
            'certificate_uid' => $uid,
            'issued_at' => $issuedAt,
            'issued_by' => Auth::id(),
            'certificate_hash' => $hash,
            'metadata' => [
                'title' => $title,
                'fields' => [
                    'standardized_id' => $standardId,
                    'entity_classification' => $classification,
                    'entity_details_html' => $entityDetailsHtml,
                    'officer_signature_name' => optional(Auth::user())->first_name.' '.optional(Auth::user())->last_name,
                ],
                'format_options' => ['pdf' => true],
            ],
        ]);

        $verificationLink = URL::signedRoute('certificate.public', ['certificate' => $certificate]);
        $qrSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="120" height="120"><rect width="120" height="120" fill="#fff" stroke="#000"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-size="8">SCAN</text></svg>';

        $fields = $certificate->metadata['fields'] ?? [];
        $fields['verification_link'] = $verificationLink;
        $fields['qr_svg'] = $qrSvg;
        $certificate->metadata = array_merge($certificate->metadata ?? [], ['fields' => $fields]);
        $certificate->save();

        $html = '<div class="p-4" style="font-family:Arial,sans-serif"><div class="d-flex align-items-center mb-3"><h3 class="mb-0" style="margin:0;padding:0">'.e($title).'</h3></div><div class="mb-2">Service: '.e($service->name).'</div><div class="mb-2">Date: '.e($issuedAt).'</div><div class="mb-2">UID: '.e($uid).'</div><div class="mb-2">Standardized ID: '.e($standardId).'</div><hr><div class="mb-2"><strong>Entity Classification</strong>: '.e($classification).'</div><div class="mb-3"><strong>Details</strong></div>'.$entityDetailsHtml.'<hr><div class="d-flex align-items-center gap-3"><div>'.$qrSvg.'</div><div style="font-size:12px">Verify: '.e($verificationLink).'</div></div><div class="mt-3" style="font-size:12px">Authorizing Officer: '.e($fields['officer_signature_name'] ?? 'Officer').'</div></div>';
        $pdfData = Pdf::loadHTML($html)->setPaper('a4')->output();
        $dir = 'certificates';
        $filename = $number.'.pdf';
        $path = $dir.'/'.$filename;

        Storage::disk('local')->put($path, $pdfData);
        $certificate->metadata = array_merge($certificate->metadata ?? [], ['archived_pdf_path' => $path, 'verification_link' => $verificationLink]);
        $certificate->save();

        ManualOperationLog::create([
            'user_id' => Auth::id(),
            'action' => 'generate_certificate_lookup',
            'target_type' => 'Project',
            'target_id' => (string) $anchorProject->id,
            'details' => ['certificate_id' => $certificate->id, 'service_id' => $service->id, 'user_phone' => $phone],
        ]);
        ManualOperationLog::create([
            'user_id' => Auth::id(),
            'action' => 'notify_sms',
            'target_type' => 'Certificate',
            'target_id' => (string) $certificate->id,
            'details' => ['to' => $phone, 'message' => 'Certificate issued. Link: '.$verificationLink],
        ]);

        return redirect()->route('admin.certificates.show', $certificate)->with('status', 'Certificate generated from phone lookup');
    }

    public function download(Request $request, Certificate $certificate)
    {
        $service = optional($certificate->service);
        $meta = $certificate->metadata ?? [];

        // --- Design Parameters ---
        $issuedDate = $certificate->issued_at?->format('d/m/Y') ?? now()->format('d/m/Y');
        $brandPrimary = '#1a4a8e';
        $brandGold = '#d4af37';
        $brandRed = '#ce1126';
        $brandLightBlue = '#4189dd';

        // Remove question marks and clean text
        $recipientName = mb_strtoupper((string) ($certificate->issued_to ?? $meta['fields']['recipient_name'] ?? ''));
        $recipientName = $recipientName ?: '__________________________';
        $recipientName = str_replace('?', '', $recipientName);

        $officerName = (string) ($meta['fields']['officer_signature_name'] ?? $meta['officer_signature_name'] ?? 'DR. YUSUF HUSSEIN JIMALE');
        $officerName = str_replace('?', '', $officerName);

        $officerTitle = (string) ($meta['fields']['officer_title'] ?? $meta['officer_title'] ?? 'GUDOOMIYE KU-XIGEENKA');
        $officerTitle = str_replace('?', '', $officerTitle);

        $uid = (string) $certificate->certificate_uid;
        $serviceName = (string) ($service?->name ?? 'DIIWAANGELINTA GURIGA DABAQA');
        $serviceName = str_replace('?', '', $serviceName);

        $certNumber = 'BRA/CS/'.date('Y').'/'.str_pad($uid, 5, '0', STR_PAD_LEFT);
        $verifyCode = 'BRA-CS-'.substr(strtoupper(hash('crc32', $uid.'BANAADIR2024')), 0, 8);

        $sig = hash_hmac('sha256', $uid, config('app.key'));
        $verificationUrl = route('certificates.verify', ['uid' => $uid, 'sig' => $sig]);

        // Generate QR code as SVG string
        $qrSvg = QrCode::format('svg')->size(200)->margin(1)->generate($verificationUrl);

        // Encode the SVG for embedding in HTML
        $qrEncoded = rawurlencode($qrSvg);

        $logoCandidates = [
            public_path('assets/images/logo/somali-government-logo.png'),
            public_path('assets/images/logo/OIP.jfif'),
        ];
        $logoDataUri = null;
        foreach ($logoCandidates as $p) {
            if (is_file($p)) {
                $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
                $mime = ($ext === 'png') ? 'image/png' : (($ext === 'jpg' || $ext === 'jpeg') ? 'image/jpeg' : 'image/jfif');
                $logoDataUri = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($p));
                break;
            }
        }
        if (! $logoDataUri) {
            $somaliFlagSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 200"><rect width="300" height="200" fill="#4189DD"/><polygon points="150,50 162.5,87.5 202.5,87.5 172.5,112.5 185,150 150,125 115,150 127.5,112.5 97.5,87.5 137.5,87.5" fill="white"/></svg>';
            $logoDataUri = 'data:image/svg+xml;base64,'.base64_encode($somaliFlagSvg);
        }
        $logoHtml = '<img src="'.$logoDataUri.'" class="government-logo" alt="Government Logo">';

        $html = '
<!DOCTYPE html>
<html lang="so">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page { size: A4 landscape; margin: 0; }
        body { margin: 0; padding: 0; font-family: "Times New Roman", "Arial", serif; color: #000; background: #ffffff; }
        .certificate { width: 297mm; height: 210mm; position: relative; background: #ffffff; overflow: hidden; }

        .official-border { position: absolute; top: 8mm; left: 8mm; right: 8mm; bottom: 8mm; border: 1.5mm double '.$brandPrimary.'; background: #ffffff; }
        .inner-border { position: absolute; top: 4mm; left: 4mm; right: 4mm; bottom: 4mm; border: 1px solid '.$brandGold.'; }

        /* Government Logo and Header */
        .government-header {
            position: absolute;
            top: 5mm;
            left: 0;
            right: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 5;
        }

        .government-logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 6mm;
            width: 100%;
            padding: 0 20mm;
        }

        .government-logo {
            height: 25mm;
            width: auto;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        .government-text {
            text-align: center;
            flex-grow: 1;
            margin-top: 2mm;
        }

        .national-emblem {
            font-size: 28pt;
            color: '.$brandPrimary.';
            margin-bottom: 1mm;
            text-align: center;
        }
        .republic-title {
            font-size: 14pt;
            font-weight: bold;
            color: '.$brandPrimary.';
            margin-bottom: 1mm;
            text-align: center;
            line-height: 1.2;
        }
        .region-title {
            font-size: 18pt;
            font-weight: bold;
            color: '.$brandPrimary.';
            text-transform: uppercase;
            text-align: center;
            line-height: 1.2;
        }

        .certificate-header {
            position: absolute;
            top: 40mm;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            width: 80%;
        }
        .certificate-title {
            font-size: 28pt;
            font-weight: bold;
            color: '.$brandPrimary.';
            margin: 5mm 0 3mm;
            padding-bottom: 3mm;
            border-bottom: 2px solid '.$brandGold.';
            text-align: center;
        }
        .certificate-subtitle {
            font-size: 14pt;
            color: #333;
            font-style: italic;
            text-align: center;
        }

        .content-area {
            position: absolute;
            top: 90mm;
            left: 25mm;
            right: 25mm;
            text-align: center;
        }
        .official-statement {
            font-size: 12pt;
            line-height: 1.6;
            margin-bottom: 10mm;
            color: #222;
            text-align: center;
        }
        .recipient-name {
            font-family: "Georgia", serif;
            font-size: 32pt;
            font-weight: bold;
            color: #000;
            text-decoration: underline;
            text-decoration-color: '.$brandGold.';
            text-align: center;
            margin-bottom: 10mm;
        }

        .service-name {
            font-size: 18pt;
            font-weight: bold;
            color: '.$brandPrimary.';
            background: #f8f9fa;
            padding: 4mm 10mm;
            border-left: 5mm solid '.$brandRed.';
            border-right: 5mm solid '.$brandLightBlue.';
            display: inline-block;
        }

        /* FIXED FOOTER ALIGNMENT */
        .official-footer {
            position: absolute;
            bottom: 30mm;
            left: 20mm;
            right: 20mm;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-top: 1px solid #ccc;
            padding-top: 8mm;
        }
        .issuance-info {
            width: 40%;
            text-align: left;
        }
        .signature-area {
            width: 30%;
            text-align: center;
        }

        .info-item {
            margin-bottom: 2mm;
            font-size: 9pt;
        }
        .info-label {
            font-weight: bold;
            color: '.$brandPrimary.';
            display: inline-block;
            width: 30mm;
        }

        .signature-line {
            width: 60mm;
            height: 1.5px;
            background: #000;
            margin: 0 auto 3mm;
            position: relative;
        }
        .official-signature {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 1mm;
        }
        .official-position {
            font-size: 9pt;
            color: #666;
            line-height: 1.2;
        }

        /* QR CODE STAMP */
        .qr-stamp {
            position: absolute;
            bottom: 30mm;
            right: 15mm;
            width: 45mm;
            height: 45mm;
            border: 2mm double '.$brandPrimary.';
            border-radius: 3mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: white;
            z-index: 10;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 2mm;
        }

        .qr-image {
            width: 35mm;
            height: 35mm;
            object-fit: contain;
        }

        .qr-text {
            font-size: 7pt;
            color: '.$brandPrimary.';
            text-align: center;
            margin-top: 1mm;
            font-weight: bold;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 90pt;
            color: rgba(26, 74, 142, 0.04);
            font-weight: bold;
            z-index: 0;
            white-space: nowrap;
        }

        /* Mini seal for the signature area */
        .mini-seal {
            position: absolute;
            bottom: 12mm;
            right: 25mm;
            width: 15mm;
            height: 15mm;
            border: 1mm solid '.$brandPrimary.';
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8pt;
            color: '.$brandPrimary.';
            font-weight: bold;
            background: white;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="watermark">BANAADIR OFFICIAL</div>
        <div class="official-border">
            <div class="inner-border">
                <!-- Government Header with Logo -->
                <div class="government-header">
                    <div class="government-logo-container">
                        '.$logoHtml.'
                        <div class="government-text">
                            <div class="region-title">DOWLADA HOOSE EE GOBOLKA BANAADIR</div>
                        </div>

                    </div>
                </div>

                <div class="certificate-header">
                    <div class="certificate-title">SHAHADADA RASMIGA AH EE ADEEGGA</div>
                    <div class="certificate-subtitle">(Official Certificate of Service)</div>
                </div>

                <div class="content-area">
                    <div class="official-statement">
                        Dowlada Gobolka Banaadir, iyadoo ku salaysan sharciga iyo dastuurka Jamhuuriyadda Federaalka Soomaaliya, ayaa ansixisay in magaca hoos ku qoran uu si buuxda u dhamaystiray adeegga loo dhigay.
                    </div>
                    <div class="recipient-name">'.$recipientName.'</div>
                    <div class="service-name">'.$serviceName.'</div>
                </div>

                <div class="official-footer">
                    <div class="issuance-info">
                        <div class="info-item"><span class="info-label">Generated by:</span> '.$officerName.'</div>
                        <div class="info-item"><span class="info-label">Taariikhda:</span> '.$issuedDate.'</div>
                        <div class="info-item"><span class="info-label">Goobta:</span> Muqdisho, Banaadir</div>
                        <div class="info-item"><span class="info-label">Bixiyay:</span> Waaxda Dhismo Wadaagga</div>
                        <div class="info-item"><span class="info-label">Lambarka:</span> '.$certNumber.'</div>
                        <div class="info-item"><span class="info-label">Koodhka:</span> '.$verifyCode.'</div>
                    </div>

                    <div class="signature-area">
                        <div class="signature-line"></div>
                        <div class="official-signature">'.$officerName.'</div>
                        <div class="official-position">'.$officerTitle.'</div>
                        <div class="mini-seal">GOV<br>SEAL</div>
                    </div>
                </div>

                <!-- QR CODE STAMP -->
                <div class="qr-stamp">
                    <img src="data:image/svg+xml;charset=utf-8,'.$qrEncoded.'" class="qr-image" alt="QR Code for Verification" />
                    <div class="qr-text">SCAN TO VERIFY</div>
                    <div class="qr-text">'.$verifyCode.'</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>';

        $pdf = Pdf::loadHTML($html)
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'dpi' => 150,
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'dejavu sans',
            ]);

        $safeName = preg_replace('/[^\w\-\.]+/', '_', str_replace('?', '', $recipientName));

        return $pdf->download('Certificate_'.$safeName.'.pdf');
    }

    public function template(Service $service)
    {
        $template = CertificateTemplate::where('service_id', $service->id)->first();
        if (! $template) {
            return response()->json([
                'template_slug' => 'default-'.$service->slug,
                'template_name' => $service->name.' Certificate',
                'variables_schema' => [
                    ['key' => 'organization_name', 'type' => 'text', 'label' => 'Organization Name', 'required' => false],
                    ['key' => 'logo_url', 'type' => 'text', 'label' => 'Logo URL', 'required' => false],
                    ['key' => 'recipient_name', 'type' => 'text', 'label' => 'Recipient Name', 'required' => true],
                    ['key' => 'recipient_achievement', 'type' => 'text', 'label' => 'Achievement/Qualification', 'required' => true],
                    ['key' => 'accomplishment_description', 'type' => 'textarea', 'label' => 'Description', 'required' => false],
                    ['key' => 'entity_classification', 'type' => 'text', 'label' => 'Entity Classification', 'required' => false],
                    ['key' => 'entity_details_html', 'type' => 'textarea', 'label' => 'Entity Details HTML', 'required' => false],
                    ['key' => 'signature_1_name', 'type' => 'text', 'label' => 'Signature 1 Name', 'required' => true],
                    ['key' => 'signature_1_title', 'type' => 'text', 'label' => 'Signature 1 Title', 'required' => true],
                    ['key' => 'signature_2_name', 'type' => 'text', 'label' => 'Signature 2 Name', 'required' => false],
                    ['key' => 'signature_2_title', 'type' => 'text', 'label' => 'Signature 2 Title', 'required' => false],
                    ['key' => 'officer_signature_name', 'type' => 'text', 'label' => 'Officer Signature Name', 'required' => false],
                    ['key' => 'brand_primary', 'type' => 'text', 'label' => 'Primary Brand Color', 'required' => false],
                    ['key' => 'standardized_id', 'type' => 'text', 'label' => 'Standardized ID', 'required' => false],
                    ['key' => 'qr_svg', 'type' => 'textarea', 'label' => 'QR SVG', 'required' => false],
                    ['key' => 'verification_link', 'type' => 'text', 'label' => 'Verification Link', 'required' => false],
                ],
                'branding' => ['theme' => 'default'],
                'format_options' => ['pdf' => true, 'png' => true],
                'html_template' => '<!-- Production-ready Certificate Template: Service Default --><div style=\"font-family:Arial,sans-serif;color:#222\"><style>@page{size:A4;margin:20mm} .wrap{border:6px solid #0a5ad1;padding:24px} .header{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px} .logo{width:64px;height:64px;object-fit:contain} .svc{font-size:12pt;color:#555} .title{font-size:28pt;font-weight:700;text-align:center;color:#0a5ad1;margin:12px 0 4px} .subtitle{text-align:center;font-size:11pt;color:#666;margin-bottom:16px} .recipient{font-size:18pt;font-weight:600;text-align:center;margin:18px 0 8px} .achievement{text-align:center;font-size:12pt;color:#333;margin-bottom:16px} .description{font-size:12pt;line-height:1.6;margin:12px 0 18px} .details{margin-top:8px} .signatures{display:flex;gap:24px;justify-content:space-between;margin-top:24px} .sig{flex:1;text-align:center} .sig .line{border-top:1px solid #999;margin-top:40px;padding-top:8px} .footer{display:flex;justify-content:space-between;align-items:center;margin-top:18px;font-size:10pt;color:#555} .badge{border:1px solid #0a5ad1;padding:4px 8px;border-radius:4px} </style><div class=\"wrap\" style=\"border-color: {{brand_primary}}\"><div class=\"header\"><div class=\"svc\">{{service}}</div><img class=\"logo\" src=\"{{logo_url}}\" alt=\"Logo\"/></div><div class=\"title\" style=\"color: {{brand_primary}}\">{{title}}</div><div class=\"subtitle\">{{organization_name}}</div><div class=\"recipient\">{{recipient_name}}</div><div class=\"achievement\">{{recipient_achievement}}</div><div class=\"description\">{{accomplishment_description}}</div><div class=\"details\"><div style=\"font-weight:600\">{{entity_classification}}</div><div>{{entity_details_html}}</div></div><div class=\"signatures\"><div class=\"sig\"><div class=\"line\">{{signature_1_name}}</div><div>{{signature_1_title}}</div></div><div class=\"sig\"><div class=\"line\">{{signature_2_name}}</div><div>{{signature_2_title}}</div></div></div><div class=\"footer\"><div>Issued {{date}}</div><div class=\"badge\">UID {{uid}}</div></div><div style=\"margin-top:8px;font-size:10pt\">ID: {{standardized_id}}</div><div style=\"margin-top:12px;font-size:10pt\"><div>{{qr_svg}}</div><div>Verify: {{verification_link}}</div></div><div style=\"margin-top:12px;font-size:10pt\">Authorizing Officer: {{officer_signature_name}}</div></div></div>',
            ]);
        }

        return response()->json($template);
    }

    public function show(Certificate $certificate)
    {
        $service = optional($certificate->service);
        ManualOperationLog::create([
            'user_id' => Auth::id(),
            'action' => 'view_certificate',
            'target_type' => 'Certificate',
            'target_id' => (string) $certificate->id,
            'details' => ['certificate_id' => $certificate->id],
        ]);

        return view('admin.certificates.show', compact('certificate', 'service'));
    }

    public function publicShow(Request $request, Certificate $certificate)
    {
        ManualOperationLog::create([
            'user_id' => Auth::id(),
            'action' => 'view_certificate_public',
            'target_type' => 'Certificate',
            'target_id' => (string) $certificate->id,
            'details' => ['certificate_id' => $certificate->id],
        ]);

        return view('admin.certificates.show', [
            'certificate' => $certificate,
            'service' => optional($certificate->service),
        ]);
    }
}
