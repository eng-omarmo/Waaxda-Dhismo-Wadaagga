<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\ManualOperationLog;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Support\StandardIdentifier;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $certs = Certificate::with(['project', 'service'])->latest()->paginate(10)->withQueryString();
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

        if ($request->filled('identifier_type') && !StandardIdentifier::validateType($request->identifier_type)) {
            return back()->withErrors(['identifier_type' => 'Unsupported identifier type'])->withInput();
        }
        if ($request->filled('identifier_type') && !$request->filled('identifier_value')) {
            return back()->withErrors(['identifier_value' => 'Identifier value is required'])->withInput();
        }
        if ($request->filled('identifier_type') && !StandardIdentifier::validate($request->identifier_type, $request->identifier_value)) {
            return back()->withErrors(['identifier_value' => 'Invalid identifier format'])->withInput();
        }
        if (StandardIdentifier::conflict($project->id, $request->identifier_type, $request->identifier_value)) {
            return back()->withErrors(['identifier_value' => 'Conflicting identifier: project ID mismatch'])->withInput();
        }
        $standardId = StandardIdentifier::compute($project->id, $request->identifier_type, $request->identifier_value);
        if (!$standardId) {
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
        $number = 'IPAMS-COC-' . date('Y') . '-' . substr($project->id, 0, 8) . '-' . $service->id . '-' . substr($uid, 0, 8);
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
            'project_id' => $project->id,
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

        $html = ($template?->html_template) ?: '<div class="p-4"><div class="d-flex align-items-center mb-3"><h4 class="mb-0">{{title}}</h4></div><div>Service: {{service}}</div><div>Project: {{project}}</div><div>Date: {{date}}</div><div>UID: {{uid}}</div><div>Standardized ID: {{standardized_id}}</div><hr><div><strong>Details</strong></div></div>';
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
            $rendered = str_replace('{{' . $k . '}}', (string) $v, $rendered);
        }
        $pdfData = Pdf::loadHTML($rendered)->setPaper('a4')->output();
        $dir = 'certificates';
        $filename = $number . '.pdf';
        $path = $dir . '/' . $filename;
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

    public function download(Certificate $certificate)
    {
        $project = Project::findOrFail($certificate->project_id);
        $service = optional($certificate->service);
        $meta = $certificate->metadata ?? [];
        $path = $meta['archived_pdf_path'] ?? null;
        $exists = $path ? Storage::disk('local')->exists($path) : false;
        if (!$exists) {
            $template = null;
            $slug = $meta['template_slug'] ?? null;
            if ($slug) {
                $template = CertificateTemplate::where('template_slug', $slug)->first();
            }
            if (!$template && $service?->id) {
                $template = CertificateTemplate::where('service_id', $service->id)->first();
            }
            $html = ($template?->html_template) ?: '<div class="p-4"><div class="d-flex align-items-center mb-3"><h4 class="mb-0">{{title}}</h4></div><div>Service: {{service}}</div><div>Project: {{project}}</div><div>Date: {{date}}</div><div>UID: {{uid}}</div><div>Standardized ID: {{standardized_id}}</div><hr><div><strong>Details</strong></div></div>';
            $replacements = [
                '{{title}}' => (string) ($meta['title'] ?? 'Certificate'),
                '{{service}}' => (string) ($service?->name ?? 'Project Registration'),
                '{{project}}' => (string) $project->id,
                '{{date}}' => (string) $certificate->issued_at?->toDateString(),
                '{{uid}}' => (string) $certificate->certificate_uid,
                '{{standardized_id}}' => (string) ($meta['fields']['standardized_id'] ?? $meta['standardized_id'] ?? ''),
            ];
            $rendered = strtr($html, $replacements);
            if (is_array($meta['fields'] ?? null)) {
                foreach ($meta['fields'] as $k => $v) {
                    $rendered = str_replace('{{' . $k . '}}', (string) $v, $rendered);
                }
            }
            $pdfData = Pdf::loadHTML($rendered)->setPaper('a4')->output();
            $dir = 'certificates';
            $filename = $certificate->certificate_number . '.pdf';
            $path = $dir . '/' . $filename;
            Storage::disk('local')->put($path, $pdfData);
            $certificate->metadata = array_merge($meta, ['archived_pdf_path' => $path]);
            $certificate->save();
        }
        return Storage::disk('local')->download($path, $certificate->certificate_number . '.pdf');
    }

    public function template(Service $service)
    {
        $template = CertificateTemplate::where('service_id', $service->id)->first();
        if (!$template) {
            return response()->json([
                'template_slug' => 'default-' . $service->slug,
                'template_name' => $service->name . ' Certificate',
                'variables_schema' => [
                    ['key' => 'parameters', 'type' => 'textarea', 'label' => 'Parameters', 'required' => false],
                    ['key' => 'configurations', 'type' => 'textarea', 'label' => 'Configurations', 'required' => false],
                    ['key' => 'compliance', 'type' => 'textarea', 'label' => 'Compliance Standards', 'required' => false],
                ],
                'branding' => ['theme' => 'default'],
                'format_options' => ['pdf' => true, 'png' => true],
                'html_template' => '<div class=\"p-4\"><h3>{{title}}</h3><div>Service: {{service}}</div><div>Project: {{project}}</div><div>Date: {{date}}</div><div>UID: {{uid}}</div><hr><div><strong>Parameters</strong><div>{{parameters}}</div></div><div><strong>Configurations</strong><div>{{configurations}}</div></div><div><strong>Compliance</strong><div>{{compliance}}</div></div></div>',
            ]);
        }
        return response()->json($template);
    }

    public function show(Certificate $certificate)
    {
        $project = Project::findOrFail($certificate->project_id);
        $service = optional($certificate->service);
        ManualOperationLog::create([
            'user_id' => Auth::id(),
            'action' => 'view_certificate',
            'target_type' => 'Project',
            'target_id' => (string) $project->id,
            'details' => ['certificate_id' => $certificate->id],
        ]);
        return view('admin.certificates.show', compact('certificate', 'project', 'service'));
    }

    public function publicShow(Request $request, Certificate $certificate)
    {
        $project = Project::findOrFail($certificate->project_id);
        ManualOperationLog::create([
            'user_id' => Auth::id(),
            'action' => 'view_certificate_public',
            'target_type' => 'Project',
            'target_id' => (string) $project->id,
            'details' => ['certificate_id' => $certificate->id],
        ]);
        return view('admin.certificates.show', [
            'certificate' => $certificate,
            'project' => $project,
            'service' => optional($certificate->service),
        ]);
    }
}
