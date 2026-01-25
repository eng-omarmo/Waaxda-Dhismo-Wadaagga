<?php

namespace App\Http\Controllers;

use App\Models\ApartmentConstructionPermit;
use App\Models\OnlinePayment;
use App\Models\Service;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ApartmentConstructionPermitController extends Controller
{
    public function publicShow(Request $request)
    {
        $service = Service::where('slug', 'construction-permit-application')->firstOrFail();

        $payment = null;
        $paymentId = (int) $request->query('payment', 0);
        if ($paymentId > 0) {
            $payment = OnlinePayment::find($paymentId);
            if ($payment) {
                $request->session()->put('construction_permit_payment_id', $payment->id);
            }
        } else {
            $sessionPaymentId = $request->session()->get('construction_permit_payment_id');
            if ($sessionPaymentId) {
                $payment = OnlinePayment::find($sessionPaymentId);
            }
        }

        return view('services.construction-permit-enhanced', [
            'service' => $service,
            'payment' => $payment,
        ]);
    }

    public function publicThankyou(string $id)
    {
        return view('services.construction-permit-thankyou', ['id' => $id]);
    }

    public function publicStore(Request $request)
    {
        $validated = $request->validate([
            'applicant_full_name' => ['required', 'string', 'max:255'],
            'applicant_national_id' => ['required', 'string', 'max:100'],
            'applicant_role' => ['required', 'in:Owner,Legal Representative,Developer'],
            'applicant_phone' => ['required', 'string', 'max:50'],
            'applicant_email' => ['nullable', 'email', 'max:255'],
            'applicant_address' => ['required', 'string', 'max:255'],
            'plot_number' => ['required', 'string', 'max:255'],
            'land_title_number' => ['required', 'string', 'max:255'],
            'land_size_sqm' => ['required', 'integer', 'min:1'],
            'land_location_district' => ['required', 'string', 'max:255'],
            'land_use_zoning' => ['required', 'in:Residential,Commercial,Mixed'],
            'land_ownership_type' => ['required', 'in:Private,Shared,Government'],
            'documents.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'payment_id' => ['nullable', 'integer', 'exists:online_payments,id'],
        ]);

        $service = Service::where('slug', 'construction-permit-application')->firstOrFail();

        $docs = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('permit_docs', 'public');
                $docs[] = [
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime' => $file->getClientMimeType(),
                ];
            }
        }

        $payment = null;
        if (! empty($validated['payment_id'])) {
            $payment = OnlinePayment::find($validated['payment_id']);
        }

        $requestDetails = [
            'applicant_role' => $validated['applicant_role'],
            'applicant_address' => $validated['applicant_address'],
            'plot_number' => $validated['plot_number'],
            'land_title_number' => $validated['land_title_number'],
            'land_size_sqm' => (int) $validated['land_size_sqm'],
            'land_location_district' => $validated['land_location_district'],
            'land_use_zoning' => $validated['land_use_zoning'],
            'land_ownership_type' => $validated['land_ownership_type'],
            'documents' => $docs,
        ];

        if ($payment) {
            $requestDetails['payment'] = [
                'online_payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'reference' => $payment->reference,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
            ];
        }

        $userEmail = auth()->check() ? auth()->user()->email : ($validated['applicant_email'] ?? '');

        $req = ServiceRequest::firstOrCreate(
            [
                'service_id' => $service->id,
                'user_email' => $userEmail,
                'status' => 'pending',
            ],
            [
                'user_id' => auth()->id(),
                'user_full_name' => $validated['applicant_full_name'],
                'user_email' => $userEmail,
                'user_phone' => $validated['applicant_phone'],
                'user_national_id' => $validated['applicant_national_id'],
                'request_details' => $requestDetails,
            ]
        );

        if (! $req->wasRecentlyCreated) {
            $existingDetails = (array) $req->request_details;
            $req->request_details = array_merge($existingDetails, $requestDetails);
            if (! $req->user_full_name) {
                $req->user_full_name = $validated['applicant_full_name'];
            }
            if (! $req->user_phone) {
                $req->user_phone = $validated['applicant_phone'];
            }
            if (! $req->user_national_id) {
                $req->user_national_id = $validated['applicant_national_id'];
            }
            $req->save();
        }

        ApartmentConstructionPermit::create([
            'applicant_name' => $validated['applicant_full_name'],
            'national_id_or_company_registration' => $validated['applicant_national_id'],
            'land_plot_number' => $validated['plot_number'],
            'location' => $validated['land_location_district'],
            'number_of_floors' => 1,
            'number_of_units' => 1,
            'approved_drawings_path' => null,
            'engineer_or_architect_name' => 'Pending',
            'engineer_or_architect_license' => null,
            'permit_issue_date' => null,
            'permit_expiry_date' => null,
            'permit_status' => 'Pending',
        ]);

        return redirect()->route('services.construction-permit.thankyou', ['id' => $req->id]);
    }

    public function index(Request $request)
    {
        $query = ApartmentConstructionPermit::latest();

        if ($request->has('search')) {
            $query->where('applicant_name', 'like', '%'.$request->search.'%')
                ->orWhere('land_plot_number', 'like', '%'.$request->search.'%');
        }

        $permits = $query->paginate(10)->withQueryString();

        return view('admin.permits.index', compact('permits'));
    }

    public function create()
    {
        return view('admin.permits.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'applicant_name' => 'required|string|max:255',
            'national_id_or_company_registration' => 'required|string|max:255',
            'land_plot_number' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'number_of_floors' => 'required|integer|min:1',
            'number_of_units' => 'required|integer|min:1',
            'approved_drawings' => 'nullable|file|mimes:pdf,dwg,zip|max:10240', // 10MB Max
            'engineer_or_architect_name' => 'required|string|max:255',
            'engineer_or_architect_license' => 'nullable|string|max:255',
            'permit_issue_date' => 'nullable|date',
            'permit_expiry_date' => 'nullable|date|after_or_equal:permit_issue_date',
            'permit_status' => 'required|in:Pending,Approved,Rejected',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->except('approved_drawings');

        if ($request->hasFile('approved_drawings')) {
            $data['approved_drawings_path'] = $request->file('approved_drawings')->store('permit_drawings', 'public');
        }

        $permit = ApartmentConstructionPermit::create($data);

        $service = Service::whereIn('slug', ['construction-permit', 'construction-permit-application'])->first();
        if ($service) {
            $schema = [
                'title' => 'Construction Permit – Data Collection',
                'instructions' => 'Complete all required fields.',
                'fields' => [
                    ['name' => 'applicant_full_name', 'label' => 'Applicant Full Name', 'type' => 'text', 'required' => true],
                    ['name' => 'applicant_role', 'label' => 'Applicant Role', 'type' => 'select', 'options' => ['Owner', 'Legal Representative', 'Developer'], 'required' => true],
                    ['name' => 'plot_number', 'label' => 'Plot Number', 'type' => 'text', 'required' => true],
                    ['name' => 'land_title_number', 'label' => 'Land Title Number', 'type' => 'text', 'required' => true],
                    ['name' => 'land_size_sqm', 'label' => 'Land Size (sqm)', 'type' => 'number', 'required' => true],
                    ['name' => 'land_location_district', 'label' => 'Location District', 'type' => 'text', 'required' => true],
                ],
            ];
            $values = [
                'applicant_full_name' => $permit->applicant_name,
                'applicant_role' => 'Owner',
                'plot_number' => $permit->land_plot_number,
                'land_title_number' => $permit->land_title_number ?? '',
                'land_size_sqm' => 1,
                'land_location_district' => $permit->location,
            ];
            ServiceRequest::create([
                'service_id' => $service->id,
                'user_id' => auth()->id(),
                'user_full_name' => $permit->applicant_name,
                'user_email' => '',
                'user_phone' => '',
                'user_national_id' => $permit->national_id_or_company_registration,
                'request_details' => [
                    'form_schema' => $schema,
                    'form_values' => $values,
                    'form_status' => 'closed',
                    'form_audit' => [[
                        'submitted_by' => auth()->id(),
                        'submitted_at' => now()->toDateTimeString(),
                        'changes' => [],
                        'values' => $values,
                    ]],
                ],
                'status' => $permit->permit_status === 'Approved' ? 'verified' : 'pending',
                'processed_by' => $permit->permit_status === 'Approved' ? auth()->id() : null,
                'processed_at' => $permit->permit_status === 'Approved' ? now() : null,
            ]);
        }

        return redirect()->route('admin.permits.index')->with('success', 'Construction permit created successfully.');
    }

    public function show(ApartmentConstructionPermit $permit)
    {
        return view('admin.permits.show', compact('permit'));
    }

    public function edit(ApartmentConstructionPermit $permit)
    {
        return view('admin.permits.edit', compact('permit'));
    }

    public function update(Request $request, ApartmentConstructionPermit $permit)
    {
        $validator = Validator::make($request->all(), [
            'applicant_name' => 'required|string|max:255',
            'national_id_or_company_registration' => 'required|string|max:255',
            'land_plot_number' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'number_of_floors' => 'required|integer|min:1',
            'number_of_units' => 'required|integer|min:1',
            'approved_drawings' => 'nullable|file|mimes:pdf,dwg,zip|max:10240',
            'engineer_or_architect_name' => 'required|string|max:255',
            'engineer_or_architect_license' => 'nullable|string|max:255',
            'permit_issue_date' => 'nullable|date',
            'permit_expiry_date' => 'nullable|date|after_or_equal:permit_issue_date',
            'permit_status' => 'required|in:Pending,Approved,Rejected',
            'approval_notes' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->except('approved_drawings');

        if ($request->hasFile('approved_drawings')) {
            // Delete old file
            if ($permit->approved_drawings_path) {
                Storage::disk('public')->delete($permit->approved_drawings_path);
            }
            $data['approved_drawings_path'] = $request->file('approved_drawings')->store('permit_drawings', 'public');
        }

        $permit->update($data);

        return redirect()->route('admin.permits.index')->with('success', 'Construction permit updated successfully.');
    }

    public function destroy(ApartmentConstructionPermit $permit)
    {
        if ($permit->approved_drawings_path) {
            Storage::disk('public')->delete($permit->approved_drawings_path);
        }
        $permit->delete();

        return redirect()->route('admin.permits.index')->with('success', 'Construction permit deleted successfully.');
    }

    public function downloadDrawing(ApartmentConstructionPermit $permit)
    {
        if ($permit->approved_drawings_path) {
            return Storage::disk('public')->download($permit->approved_drawings_path);
        }

        return redirect()->back()->with('error', 'No drawing file available.');
    }

    public function approve(Request $request, ApartmentConstructionPermit $permit)
    {
        $validated = $request->validate([
            'approval_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        // Land Ownership Verification Check
        $plotNumber = $permit->land_plot_number;
        $landParcel = \App\Models\LandParcel::where('plot_number', $plotNumber)->first();

        if (! $landParcel) {
            return back()->withErrors([
                'land_verification' => 'Land parcel with plot number "'.$plotNumber.'" not found. Please register the land parcel first.',
            ])->withInput();
        }

        if ($landParcel->verification_status !== 'Verified') {
            return back()->withErrors([
                'land_verification' => 'Land ownership must be verified before permit approval. Current status: '.$landParcel->verification_status.'. <a href="'.route('admin.land-parcels.show', $landParcel).'">Verify land parcel</a>',
            ])->withInput();
        }

        // Check applicant matches verified owner
        if ($landParcel->current_owner_national_id !== $permit->national_id_or_company_registration) {
            return back()->withErrors([
                'ownership_mismatch' => 'Applicant national ID ('.$permit->national_id_or_company_registration.') does not match verified land owner ('.$landParcel->current_owner_national_id.').',
            ])->withInput();
        }

        $permit->permit_status = 'Approved';
        $permit->approval_notes = $validated['approval_notes'] ?? null;
        $permit->approved_by_admin_id = auth()->id();
        $permit->approved_at = now();
        $permit->save();

        return back()->with('success', 'Permit approved');
    }

    public function reject(Request $request, ApartmentConstructionPermit $permit)
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:2000'],
        ]);
        $permit->permit_status = 'Rejected';
        $permit->approval_notes = $validated['rejection_reason'];
        $permit->approved_by_admin_id = auth()->id();
        $permit->approved_at = now();
        $permit->save();

        return back()->with('success', 'Permit rejected');
    }
}
