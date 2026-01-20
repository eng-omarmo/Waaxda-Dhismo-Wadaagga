<?php

namespace App\Http\Controllers;

use App\Models\LandParcel;
use App\Models\ManualOperationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LandParcelController extends Controller
{
    public function index(Request $request)
    {
        $query = LandParcel::query()->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('plot_number', 'like', '%'.$search.'%')
                    ->orWhere('title_number', 'like', '%'.$search.'%')
                    ->orWhere('current_owner_name', 'like', '%'.$search.'%')
                    ->orWhere('location_district', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('verification_status')) {
            $query->where('verification_status', $request->verification_status);
        }

        if ($request->filled('ownership_type')) {
            $query->where('ownership_type', $request->ownership_type);
        }

        $perPage = min(max((int) $request->query('per_page', 10), 1), 100);
        $parcels = $query->with('verifier')->paginate($perPage)->withQueryString();
        $verificationStatuses = ['Unverified', 'PendingVerification', 'Verified', 'Rejected'];
        $ownershipTypes = ['Private', 'Shared', 'Government', 'Leased'];

        return view('admin.land-parcels.index', compact('parcels', 'verificationStatuses', 'ownershipTypes'));
    }

    public function show(LandParcel $landParcel)
    {
        $landParcel->load(['verifier', 'verifications.verifiedBy', 'ownershipHistories.recordedBy', 'permits']);

        return view('admin.land-parcels.show', compact('landParcel'));
    }

    public function create()
    {
        return view('admin.land-parcels.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plot_number' => ['required', 'string', 'max:255', 'unique:land_parcels,plot_number'],
            'title_number' => ['nullable', 'string', 'max:255'],
            'location_district' => ['required', 'string', 'max:255'],
            'location_region' => ['nullable', 'string', 'max:255'],
            'size_sqm' => ['nullable', 'numeric', 'min:0'],
            'current_owner_name' => ['required', 'string', 'max:255'],
            'current_owner_national_id' => ['required', 'string', 'max:100'],
            'ownership_type' => ['required', Rule::in(['Private', 'Shared', 'Government', 'Leased'])],
            'verification_status' => ['required', Rule::in(['Unverified', 'PendingVerification', 'Verified', 'Rejected'])],
            'verification_documents' => ['nullable', 'array'],
            'verification_documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'verification_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $documents = [];
        if ($request->hasFile('verification_documents')) {
            foreach ($request->file('verification_documents') as $file) {
                $path = $file->store('land_verification_docs', 'public');
                $documents[] = [
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime' => $file->getMimeType(),
                ];
            }
        }

        $parcel = LandParcel::create([
            'plot_number' => $validated['plot_number'],
            'title_number' => $validated['title_number'] ?? null,
            'location_district' => $validated['location_district'],
            'location_region' => $validated['location_region'] ?? null,
            'size_sqm' => $validated['size_sqm'] ?? null,
            'current_owner_name' => $validated['current_owner_name'],
            'current_owner_national_id' => $validated['current_owner_national_id'],
            'ownership_type' => $validated['ownership_type'],
            'verification_status' => $validated['verification_status'],
            'verification_documents_path' => ! empty($documents) ? $documents : null,
            'verification_notes' => $validated['verification_notes'] ?? null,
            'verified_by_admin_id' => $validated['verification_status'] === 'Verified' ? Auth::id() : null,
            'verified_at' => $validated['verification_status'] === 'Verified' ? now() : null,
            'last_verification_date' => $validated['verification_status'] === 'Verified' ? now() : null,
        ]);

        ManualOperationLog::create([
            'user_id' => Auth::id(),
            'action' => 'land_parcel_created',
            'target_type' => 'LandParcel',
            'target_id' => (string) $parcel->id,
            'details' => [
                'plot_number' => $parcel->plot_number,
                'verification_status' => $parcel->verification_status,
            ],
        ]);

        return redirect()->route('admin.land-parcels.show', $parcel)->with('success', 'Land parcel created successfully.');
    }

    public function verify(Request $request, LandParcel $landParcel)
    {
        $validated = $request->validate([
            'verification_status' => ['required', Rule::in(['Verified', 'Rejected'])],
            'verification_notes' => ['nullable', 'string', 'max:2000'],
            'verification_documents' => ['nullable', 'array'],
            'verification_documents.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $documents = $landParcel->verification_documents_path ?? [];
        if ($request->hasFile('verification_documents')) {
            foreach ($request->file('verification_documents') as $file) {
                $path = $file->store('land_verification_docs', 'public');
                $documents[] = [
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime' => $file->getMimeType(),
                ];
            }
        }

        $landParcel->update([
            'verification_status' => $validated['verification_status'],
            'verification_notes' => $validated['verification_notes'] ?? null,
            'verification_documents_path' => $documents,
            'verified_by_admin_id' => Auth::id(),
            'verified_at' => now(),
            'last_verification_date' => now(),
        ]);

        ManualOperationLog::create([
            'user_id' => Auth::id(),
            'action' => 'land_parcel_verified',
            'target_type' => 'LandParcel',
            'target_id' => (string) $landParcel->id,
            'details' => [
                'verification_status' => $validated['verification_status'],
                'plot_number' => $landParcel->plot_number,
            ],
        ]);

        return back()->with('success', 'Land parcel verification status updated.');
    }
}
