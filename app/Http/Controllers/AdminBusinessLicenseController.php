<?php

namespace App\Http\Controllers;

use App\Models\BusinessLicense;
use Illuminate\Http\Request;
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
        $licenses = $query->paginate(10)->withQueryString();
        $statuses = ['pending','approved','rejected'];
        return view('admin.pages.licensing', compact('licenses','statuses'));
    }

    public function approve(Request $request, BusinessLicense $license)
    {
        $license->status = 'approved';
        $license->admin_comments = $request->string('admin_comments')->toString();
        $license->save();
        return redirect()->route('admin.licensing.index')->with('status', 'License approved');
    }

    public function reject(Request $request, BusinessLicense $license)
    {
        $license->status = 'rejected';
        $license->admin_comments = $request->string('admin_comments')->toString();
        $license->save();
        return redirect()->route('admin.licensing.index')->with('status', 'License rejected');
    }

    public function update(Request $request, BusinessLicense $license)
    {
        $request->validate([
            'verification_status' => ['required', Rule::in(['unverified','verified'])],
            'expires_at' => ['nullable', 'date'],
        ]);
        $license->verification_status = $request->verification_status;
        $license->expires_at = $request->expires_at;
        $license->save();
        return redirect()->route('admin.licensing.index')->with('status', 'License updated');
    }

    public function downloadDoc(BusinessLicense $license, int $docId)
    {
        $doc = DB::table('business_license_documents')->where('id', $docId)->where('license_id', $license->id)->first();
        if (!$doc) {
            abort(404);
        }
        if (!Storage::disk('public')->exists($doc->file_path)) {
            abort(404);
        }
        return Storage::disk('public')->download($doc->file_path, $doc->file_name);
    }
}

