<?php

namespace App\Http\Controllers;

use App\Models\BusinessLicense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

class BusinessLicenseController extends Controller
{
    public function show()
    {
        return view('services.business-license-enhanced');
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
            'documents.*' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $key = 'license-register:'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 15)) {
            return back()->withErrors(['company_name' => 'Too many attempts. Please try again later.'])->withInput();
        }

        $license = BusinessLicense::create($request->only([
            'company_name',
            'project_id',
            'license_type',
            'registrant_name',
            'registrant_email',
            'registrant_phone',
        ]));

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

        RateLimiter::hit($key, 60);

        return redirect()->route('services.business-license')->with('status', 'License submission received. Status: pending review.');
    }
}
