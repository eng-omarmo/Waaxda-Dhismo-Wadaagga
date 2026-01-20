<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\OrganizationChange;
use App\Models\OrganizationDocument;
use App\Models\Service;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminOrganizationController extends Controller
{
    public function index(Request $request)
    {
        $query = Organization::query();
        if ($q = $request->string('q')->toString()) {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%$q%")
                    ->orWhere('registration_number', 'like', "%$q%")
                    ->orWhere('contact_email', 'like', "%$q%");
            });
        }
        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }
        $sort = $request->string('sort')->toString() ?: 'date';
        $direction = $request->string('direction')->toString() ?: 'desc';
        if ($sort === 'alpha') {
            $query->orderBy('name', $direction === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('created_at', $direction === 'asc' ? 'asc' : 'desc');
        }
        $perPage = min(max((int) $request->query('per_page', 10), 1), 100);
        $organizations = $query->paginate($perPage)->withQueryString();
        $statuses = ['pending', 'approved', 'rejected'];

        return view('admin.pages.organizations', compact('organizations', 'statuses', 'sort', 'direction'));
    }

    public function show(Organization $organization)
    {
        $documents = OrganizationDocument::where('organization_id', $organization->id)->latest()->get();

        return view('admin.pages.organization-show', compact('organization', 'documents'));
    }

    public function downloadDoc(Organization $organization, OrganizationDocument $document)
    {
        if ($document->organization_id !== $organization->id) {
            abort(404);
        }
        if (! Storage::disk('public')->exists($document->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:Developer,Contractor,Consultant,Other'],
            'contact_full_name' => ['required', 'string', 'max:255'],
            'contact_role' => ['required', 'string', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:50'],
            'contact_email' => ['required', 'email', 'max:255'],
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
        ]);
        $org = Organization::create($request->only([
            'name', 'registration_number', 'address', 'type',
            'contact_full_name', 'contact_role', 'contact_phone', 'contact_email', 'status',
        ]));

        $service = Service::whereIn('slug', ['developer-registration', 'organization-registration'])->first();
        if ($service) {
            $schema = [
                'title' => 'Organization Registration – Data Collection',
                'instructions' => 'Complete all required fields.',
                'fields' => [
                    ['name' => 'organization_name', 'label' => 'Organization Name', 'type' => 'text', 'required' => true],
                    ['name' => 'registration_number', 'label' => 'Registration Number', 'type' => 'text', 'required' => false],
                    ['name' => 'contact_email', 'label' => 'Contact Email', 'type' => 'email', 'required' => true],
                    ['name' => 'contact_phone', 'label' => 'Contact Phone', 'type' => 'text', 'required' => true],
                ],
            ];
            $values = [
                'organization_name' => $org->name,
                'registration_number' => $org->registration_number,
                'contact_email' => $org->contact_email,
                'contact_phone' => $org->contact_phone,
            ];
            ServiceRequest::create([
                'service_id' => $service->id,
                'user_id' => Auth::id(),
                'user_full_name' => $org->contact_full_name,
                'user_email' => $org->contact_email,
                'user_phone' => $org->contact_phone,
                'user_national_id' => null,
                'request_details' => [
                    'form_schema' => $schema,
                    'form_values' => $values,
                    'form_status' => 'closed',
                    'form_audit' => [[
                        'submitted_by' => Auth::id(),
                        'submitted_at' => now()->toDateTimeString(),
                        'changes' => [],
                        'values' => $values,
                    ]],
                ],
                'status' => 'verified',
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);
        }

        return redirect()->route('admin.organizations.index')->with('status', 'Organization created');
    }

    public function update(Request $request, Organization $organization)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:Developer,Contractor,Consultant,Other'],
            'contact_full_name' => ['required', 'string', 'max:255'],
            'contact_role' => ['required', 'string', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:50'],
            'contact_email' => ['required', 'email', 'max:255'],
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
        ]);
        $original = $organization->getOriginal();
        $organization->fill($request->only([
            'name', 'registration_number', 'address', 'type',
            'contact_full_name', 'contact_role', 'contact_phone', 'contact_email', 'status',
        ]));
        $organization->save();
        $changes = [];
        foreach ($organization->getChanges() as $key => $value) {
            if ($key === 'updated_at') {
                continue;
            }
            $changes[$key] = ['from' => $original[$key] ?? null, 'to' => $value];
        }
        if (! empty($changes)) {
            OrganizationChange::create([
                'organization_id' => $organization->id,
                'changed_by' => Auth::id(),
                'changes' => $changes,
            ]);
        }

        return redirect()->route('admin.organizations.index')->with('status', 'Organization updated');
    }

    public function approve(Organization $organization)
    {
        $original = $organization->getOriginal();
        $organization->status = 'approved';
        $organization->save();
        OrganizationChange::create([
            'organization_id' => $organization->id,
            'changed_by' => Auth::id(),
            'changes' => ['status' => ['from' => $original['status'] ?? null, 'to' => 'approved']],
        ]);

        return redirect()->route('admin.organizations.index')->with('status', 'Organization approved');
    }

    public function reject(Request $request, Organization $organization)
    {
        $original = $organization->getOriginal();
        $organization->status = 'rejected';
        $organization->admin_notes = $request->string('admin_notes')->toString();
        $organization->save();
        OrganizationChange::create([
            'organization_id' => $organization->id,
            'changed_by' => Auth::id(),
            'changes' => [
                'status' => ['from' => $original['status'] ?? null, 'to' => 'rejected'],
                'admin_notes' => ['from' => $original['admin_notes'] ?? null, 'to' => $organization->admin_notes],
            ],
        ]);

        return redirect()->route('admin.organizations.index')->with('status', 'Organization rejected');
    }
}
