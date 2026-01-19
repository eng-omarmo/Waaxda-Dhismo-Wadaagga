<?php

namespace App\Http\Controllers;

use App\Models\BusinessLicense;
use App\Models\BusinessLicenseChange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminBusinessLicenseController extends Controller
{
    public function index(Request $request)
    {
        $query = BusinessLicense::query();
        if ($q = $request->string('q')->toString()) {
            $query->where('company_name', 'like', "%$q%");
        }
        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }
        if ($date = $request->string('date')->toString()) {
            $query->whereDate('created_at', $date);
        }
        $query->orderBy('created_at', 'desc');
        $perPage = min(max((int) $request->query('per_page', 10), 1), 100);
        $licenses = $query->paginate($perPage)->withQueryString();
        $statuses = ['pending', 'approved', 'rejected'];

        return view('admin.pages.licensing', compact('licenses', 'statuses'));
    }

    public function edit(Request $request, BusinessLicense $license)
    {
        $query = BusinessLicense::query();
        if ($q = $request->string('q')->toString()) {
            $query->where('company_name', 'like', "%$q%");
        }
        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }
        if ($date = $request->string('date')->toString()) {
            $query->whereDate('created_at', $date);
        }
        $query->orderBy('created_at', 'desc');
        $perPage = min(max((int) $request->query('per_page', 10), 1), 100);
        $licenses = $query->paginate($perPage)->withQueryString();
        $statuses = ['pending', 'approved', 'rejected'];
        $verificationStatuses = ['unverified', 'verified'];
        $previousChange = BusinessLicenseChange::where('license_id', $license->id)->latest()->first();
        $history = BusinessLicenseChange::where('license_id', $license->id)->orderByDesc('created_at')->get();

        return view('admin.pages.license-edit', compact('license', 'licenses', 'statuses', 'verificationStatuses', 'previousChange', 'history'));
    }

    public function save(Request $request, BusinessLicense $license)
    {
        $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'project_id' => ['nullable', 'string', 'max:36'],
            'license_type' => ['required', Rule::in(['Rental', 'Commercial'])],
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
            'verification_status' => ['required', Rule::in(['unverified', 'verified'])],
            'expires_at' => ['nullable', 'date'],
            'admin_comments' => ['nullable', 'string'],
            'registrant_name' => ['nullable', 'string', 'max:255'],
            'registrant_email' => ['nullable', 'email', 'max:255'],
            'registrant_phone' => ['nullable', 'string', 'max:50'],
        ]);

        $before = $license->only([
            'company_name', 'project_id', 'license_type', 'status', 'verification_status', 'expires_at', 'admin_comments',
            'registrant_name', 'registrant_email', 'registrant_phone',
        ]);

        $license->company_name = $request->company_name;
        $license->project_id = $request->project_id;
        $license->license_type = $request->license_type;
        $license->status = $request->status;
        $license->verification_status = $request->verification_status;
        $license->expires_at = $request->expires_at;
        $license->admin_comments = $request->admin_comments;
        $license->registrant_name = $request->registrant_name;
        $license->registrant_email = $request->registrant_email;
        $license->registrant_phone = $request->registrant_phone;
        $license->save();

        $after = $license->only(array_keys($before));
        $changes = [];
        foreach ($before as $k => $v) {
            $nv = $after[$k] ?? null;
            if ($v instanceof \DateTimeInterface) {
                $v = $v->format('Y-m-d H:i:s');
            }
            if ($nv instanceof \DateTimeInterface) {
                $nv = $nv->format('Y-m-d H:i:s');
            }
            if ($v !== $nv) {
                $changes[$k] = ['from' => $v, 'to' => $nv];
            }
        }
        if (! empty($changes)) {
            BusinessLicenseChange::create([
                'license_id' => $license->id,
                'changed_by' => Auth::id(),
                'changes' => $changes,
            ]);
        }

        return redirect()->route('admin.licensing.edit', $license)->with('status', 'Changes saved');
    }

    public function approve(Request $request, BusinessLicense $license)
    {
        $request->validate([
            'admin_comments' => ['nullable', 'string'],
        ]);
        $beforeStatus = $license->status;
        $license->status = 'approved';
        $license->admin_comments = $request->string('admin_comments')->toString();
        $license->approved_by = Auth::id();
        $license->approved_at = now();
        $license->save();

        $changes = [];
        if ($beforeStatus !== $license->status) {
            $changes['status'] = ['from' => $beforeStatus, 'to' => $license->status];
        }
        if (! empty($changes)) {
            BusinessLicenseChange::create([
                'license_id' => $license->id,
                'changed_by' => Auth::id(),
                'changes' => $changes,
            ]);
        }

        return redirect()->route('admin.licensing.index')->with('status', 'License approved');
    }

    public function displayIssuePage()
    {

        return view('admin.pages.new-business-licence');
    }

    public function reject(Request $request, BusinessLicense $license)
    {
        $beforeStatus = $license->status;
        $license->status = 'rejected';
        $license->admin_comments = $request->string('admin_comments')->toString();
        $license->save();

        $changes = [];
        if ($beforeStatus !== $license->status) {
            $changes['status'] = ['from' => $beforeStatus, 'to' => $license->status];
        }
        if (! empty($changes)) {
            BusinessLicenseChange::create([
                'license_id' => $license->id,
                'changed_by' => Auth::id(),
                'changes' => $changes,
            ]);
        }

        return redirect()->route('admin.licensing.index')->with('status', 'License rejected');
    }

    public function update(Request $request, BusinessLicense $license)
    {
        $request->validate([
            'verification_status' => ['required', Rule::in(['unverified', 'verified'])],
            'expires_at' => ['nullable', 'date'],
        ]);
        $before = $license->only(['verification_status', 'expires_at']);
        $license->verification_status = $request->verification_status;
        $license->expires_at = $request->expires_at;
        $license->save();

        $after = $license->only(['verification_status', 'expires_at']);
        $changes = [];
        foreach ($before as $k => $v) {
            $nv = $after[$k] ?? null;
            if ($v instanceof \DateTimeInterface) {
                $v = $v->format('Y-m-d H:i:s');
            }
            if ($nv instanceof \DateTimeInterface) {
                $nv = $nv->format('Y-m-d H:i:s');
            }
            if ($v !== $nv) {
                $changes[$k] = ['from' => $v, 'to' => $nv];
            }
        }
        if (! empty($changes)) {
            BusinessLicenseChange::create([
                'license_id' => $license->id,
                'changed_by' => Auth::id(),
                'changes' => $changes,
            ]);
        }

        return redirect()->route('admin.licensing.index')->with('status', 'License updated');
    }

    public function downloadDoc(BusinessLicense $license, int $docId)
    {
        $doc = DB::table('business_license_documents')->where('id', $docId)->where('license_id', $license->id)->first();
        if (! $doc) {
            abort(404);
        }
        if (! Storage::disk('public')->exists($doc->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($doc->file_path, $doc->file_name);
    }
}
