<?php

namespace App\Http\Controllers;

use App\Models\ApartmentConstructionPermit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ApartmentConstructionPermitController extends Controller
{
    public function index(Request $request)
    {
        $query = ApartmentConstructionPermit::latest();

        if ($request->has('search')) {
            $query->where('applicant_name', 'like', '%' . $request->search . '%')
                  ->orWhere('land_plot_number', 'like', '%' . $request->search . '%');
        }

        $permits = $query->paginate(10);
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

        ApartmentConstructionPermit::create($data);

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
}
