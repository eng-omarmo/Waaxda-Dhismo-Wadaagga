<?php

namespace App\Http\Controllers;

use App\Models\EngineerLicense;
use App\Models\ManualOperationLog;
use App\Models\Service;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminEngineerLicenseController extends Controller
{
    public function index()
    {
        $licenses = EngineerLicense::latest()->paginate(10);
        return view('admin.pages.engineer-licenses', compact('licenses'));
    }

    public function create()
    {
        return view('admin.pages.engineer-license-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'applicant_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'national_id' => 'required|string|max:20',
            'engineering_field' => 'required|string|max:255',
            'university' => 'nullable|string|max:255',
            'graduation_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'status' => 'required|in:Pending,Approved,Rejected',
            'admin_comments' => 'nullable|string',
        ]);

        $license = EngineerLicense::create([
            'applicant_name' => $validated['applicant_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'national_id' => $validated['national_id'],
            'engineering_field' => $validated['engineering_field'],
            'university' => $validated['university'],
            'graduation_year' => $validated['graduation_year'],
            'status' => $validated['status'],
            'admin_comments' => $validated['admin_comments'],
            'approved_by' => $validated['status'] !== 'Pending' ? Auth::id() : null,
            'approved_at' => $validated['status'] !== 'Pending' ? now() : null,
        ]);

        ManualOperationLog::create([
            'user_id' => Auth::id(),
            'action' => 'admin_issue_engineer_license',
            'target_type' => 'EngineerLicense',
            'target_id' => (string) $license->id,
            'details' => ['applicant_name' => $license->applicant_name, 'status' => $license->status],
        ]);

        $service = Service::where('slug', 'engineer-license')->first();
        if ($service) {
            $statusForRequest = $license->status === 'Approved' ? 'verified' : ($license->status === 'Rejected' ? 'rejected' : 'pending');
            ServiceRequest::updateOrCreate(
                [
                    'service_id' => $service->id,
                    'user_email' => $license->email,
                ],
                [
                    'user_id' => Auth::id(),
                    'user_full_name' => $license->applicant_name,
                    'user_email' => $license->email,
                    'user_phone' => $license->phone,
                    'user_national_id' => $license->national_id,
                    'request_details' => [
                        'engineering_field' => $license->engineering_field,
                        'university' => $license->university,
                        'graduation_year' => $license->graduation_year,
                        'admin_comments' => $license->admin_comments,
                        'engineer_license_id' => $license->id,
                    ],
                    'status' => $statusForRequest,
                    'processed_by' => Auth::id(),
                    'processed_at' => now(),
                ]
            );
        }

        return redirect()->route('admin.engineer-licenses.index')->with('success', 'Engineer license issued successfully.');
    }

    public function show(EngineerLicense $license)
    {
        return view('admin.pages.engineer-license-show', compact('license'));
    }

    public function approve(Request $request, EngineerLicense $license)
    {
        $license->update([
            'status' => 'Approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'admin_comments' => $request->input('admin_comments'),
        ]);

        ManualOperationLog::create([
            'user_id' => Auth::id(),
            'action' => 'approve_engineer_license',
            'target_type' => 'EngineerLicense',
            'target_id' => (string) $license->id,
            'details' => ['applicant_name' => $license->applicant_name],
        ]);

        $service = Service::where('slug', 'engineer-license')->first();
        if ($service) {
            $statusForRequest = 'verified';
            ServiceRequest::updateOrCreate(
                [
                    'service_id' => $service->id,
                    'user_email' => $license->email,
                ],
                [
                    'user_id' => Auth::id(),
                    'user_full_name' => $license->applicant_name,
                    'user_email' => $license->email,
                    'user_phone' => $license->phone,
                    'user_national_id' => $license->national_id,
                    'request_details' => [
                        'engineering_field' => $license->engineering_field,
                        'university' => $license->university,
                        'graduation_year' => $license->graduation_year,
                        'admin_comments' => $license->admin_comments,
                        'engineer_license_id' => $license->id,
                    ],
                    'status' => $statusForRequest,
                    'processed_by' => Auth::id(),
                    'processed_at' => now(),
                ]
            );
        }

        return redirect()->route('admin.engineer-licenses.index')->with('success', 'License approved successfully.');
    }

    public function reject(Request $request, EngineerLicense $license)
    {
        $license->update([
            'status' => 'Rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'admin_comments' => $request->input('admin_comments'),
        ]);

        ManualOperationLog::create([
            'user_id' => Auth::id(),
            'action' => 'reject_engineer_license',
            'target_type' => 'EngineerLicense',
            'target_id' => (string) $license->id,
            'details' => ['applicant_name' => $license->applicant_name],
        ]);

        $service = Service::where('slug', 'engineer-license')->first();
        if ($service) {
            $statusForRequest = 'rejected';
            ServiceRequest::updateOrCreate(
                [
                    'service_id' => $service->id,
                    'user_email' => $license->email,
                ],
                [
                    'user_id' => Auth::id(),
                    'user_full_name' => $license->applicant_name,
                    'user_email' => $license->email,
                    'user_phone' => $license->phone,
                    'user_national_id' => $license->national_id,
                    'request_details' => [
                        'engineering_field' => $license->engineering_field,
                        'university' => $license->university,
                        'graduation_year' => $license->graduation_year,
                        'admin_comments' => $license->admin_comments,
                        'engineer_license_id' => $license->id,
                    ],
                    'status' => $statusForRequest,
                    'processed_by' => Auth::id(),
                    'processed_at' => now(),
                ]
            );
        }

        return redirect()->route('admin.engineer-licenses.index')->with('success', 'License rejected.');
    }
}
