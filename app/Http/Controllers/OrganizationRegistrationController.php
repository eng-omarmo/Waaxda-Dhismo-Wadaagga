<?php

namespace App\Http\Controllers;

use App\Mail\OrganizationRegistered;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;

class OrganizationRegistrationController extends Controller
{
    public function show()
    {
        return view('services.developer-registration');
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
            'terms' => ['accepted'],
            'documents.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
        ]);

        $key = 'org-register:'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return back()->withErrors(['name' => 'Too many attempts. Please try again later.'])->withInput();
        }

        $org = Organization::create([
            'name' => $request->name,
            'registration_number' => $request->registration_number,
            'address' => $request->address,
            'type' => $request->type,
            'contact_full_name' => $request->contact_full_name,
            'contact_role' => $request->contact_role,
            'contact_phone' => $request->contact_phone,
            'contact_email' => $request->contact_email,
            'status' => 'pending',
        ]);

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('organization_docs', 'public');
                \DB::table('organization_documents')->insert([
                    'organization_id' => $org->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientOriginalExtension(),
                    'document_label' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        try {
            Mail::to($org->contact_email)->send(new OrganizationRegistered($org));
        } catch (\Throwable $e) {
        } finally {
            RateLimiter::hit($key, 60);
        }

        return redirect()->route('services.developer-registration')->with('status', 'Registration received. Please check your email.');
    }
}
