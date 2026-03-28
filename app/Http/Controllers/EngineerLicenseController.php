<?php

namespace App\Http\Controllers;

use App\Models\EngineerLicense;
use App\Models\ManualOperationLog;
use App\Models\Service;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EngineerLicenseController extends Controller
{
    public function show()
    {
        return view('services.engineer-license');
    }

    public function store(Request $request)
    {
        $request->validate([
            'applicant_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'national_id' => 'required|string|max:20',
            'engineering_field' => 'required|string|max:255',
            'university' => 'nullable|string|max:255',
            'graduation_year' => 'nullable|integer|min:1900|max:'.(date('Y') + 1),
        ]);

        $data = $request->only([
            'applicant_name',
            'email',
            'phone',
            'national_id',
            'engineering_field',
            'university',
            'graduation_year',
        ]);

        $license = EngineerLicense::create($data);

        if (Auth::check()) {
            ManualOperationLog::create([
                'user_id' => Auth::id(),
                'action' => 'admin_create_engineer_license',
                'target_type' => 'EngineerLicense',
                'target_id' => (string) $license->id,
                'details' => ['applicant_name' => $license->applicant_name],
            ]);
        }

        $service = Service::where('slug', 'engineer-license')->first();
        if ($service) {
            ServiceRequest::create([
                'service_id' => $service->id,
                'user_id' => null,
                'user_full_name' => $license->applicant_name,
                'user_email' => $license->email,
                'user_phone' => $license->phone,
                'user_national_id' => $license->national_id,
                'request_details' => [
                    'engineering_field' => $license->engineering_field,
                    'university' => $license->university,
                    'graduation_year' => $license->graduation_year,
                    'engineer_license_id' => $license->id,
                ],
                'status' => 'pending',
            ]);
        }

        return redirect()->route('services.engineer-license')->with('status', 'Application submitted successfully. Pending review.');
    }
}
