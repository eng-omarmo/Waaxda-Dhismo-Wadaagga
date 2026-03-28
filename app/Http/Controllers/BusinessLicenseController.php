<?php

namespace App\Http\Controllers;

use App\Models\BusinessLicense;
use App\Models\ManualOperationLog;
use App\Models\Project;
use App\Models\Service;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

class BusinessLicenseController extends Controller
{
    public function show()
    {
        $projects = Project::all();
        return view('services.business-license-enhanced', compact('projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'project_id' => ['nullable', 'string', 'max:36'],
            'license_type' => ['required', 'in:Rental,Commercial'],
            'registrant_name' => ['required', 'string', 'max:255'],
            'registrant_email' => ['required', 'email', 'max:255'],
            'registrant_phone' => ['required', 'string', 'max:50'],
            'documents.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $key = 'license-register:'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 15)) {
            return back()->withErrors(['company_name' => 'Too many attempts. Please try again later.'])->withInput();
        }

        try {
            DB::beginTransaction();

            $license = BusinessLicense::create($request->only([
                'company_name',
                'project_id',
                'license_type',
                'registrant_name',
                'registrant_email',
                'registrant_phone',
            ]));

            if (Auth::check()) {
                ManualOperationLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'admin_create_business_license',
                    'target_type' => 'BusinessLicense',
                    'target_id' => (string) $license->id,
                    'details' => ['company_name' => $license->company_name],
                ]);
            }

            $service = Service::where('slug', 'business-license')->first();
            if ($service) {
                ServiceRequest::create([
                    'service_id' => $service->id,
                    'user_id' => Auth::id(),
                    'user_full_name' => $license->registrant_name,
                    'user_email' => $license->registrant_email,
                    'user_phone' => $license->registrant_phone,
                    'user_national_id' => null,
                    'request_details' => [
                        'company_name' => $license->company_name,
                        'license_type' => $license->license_type,
                        'project_id' => $license->project_id,
                        'business_license_id' => $license->id,
                    ],
                    'status' => 'pending',
                ]);
            }

            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $path = $file->store('license_docs', 'public');
                    DB::table('business_license_documents')->insert([
                        'license_id' => $license->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $file->getClientOriginalExtension(),
                        'document_label' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();

            RateLimiter::hit($key, 60);

            return redirect()->route('services.business-license')->with('status', 'License submission received. Status: pending review.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'License submission failed: ' . $e->getMessage()])->withInput();
        }
    }
}
