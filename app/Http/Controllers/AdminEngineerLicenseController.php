<?php

namespace App\Http\Controllers;

use App\Models\EngineerLicense;
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
