<?php

namespace App\Http\Controllers;

use App\Models\LandOwnershipVerification;
use App\Models\LandParcel;
use App\Models\ManualOperationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LandOwnershipVerificationController extends Controller
{
    public function index(Request $request)
    {
        $query = LandOwnershipVerification::query()->with(['landParcel', 'verifiedBy'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('applicant_name', 'like', '%'.$search.'%')
                    ->orWhereHas('landParcel', function ($landQ) use ($search) {
                        $landQ->where('plot_number', 'like', '%'.$search.'%');
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('verification_request_type')) {
            $query->where('verification_request_type', $request->verification_request_type);
        }

        $perPage = min(max((int) $request->query('per_page', 10), 1), 100);
        $verifications = $query->paginate($perPage)->withQueryString();
        $statuses = ['Pending', 'InProgress', 'Verified', 'Rejected'];
        $requestTypes = ['PrePermit', 'PreConstruction', 'Transfer'];

        return view('admin.land-verifications.index', compact('verifications', 'statuses', 'requestTypes'));
    }

    public function show(LandOwnershipVerification $verification)
    {
        $verification->load(['landParcel', 'requestedBy', 'verifiedBy']);

        return view('admin.land-verifications.show', compact('verification'));
    }

    public function create()
    {
        $landParcels = LandParcel::orderBy('plot_number')->get();

        return view('admin.land-verifications.create', compact('landParcels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'land_parcel_id' => ['required', 'uuid', 'exists:land_parcels,id'],
            'verification_request_type' => ['required', Rule::in(['PrePermit', 'PreConstruction', 'Transfer'])],
            'applicant_national_id' => ['required', 'string', 'max:100'],
            'applicant_name' => ['required', 'string', 'max:255'],
        ]);

        $landParcel = LandParcel::findOrFail($validated['land_parcel_id']);

        $verification = LandOwnershipVerification::create([
            'land_parcel_id' => $validated['land_parcel_id'],
            'verification_request_type' => $validated['verification_request_type'],
            'requested_by_admin_id' => Auth::id(),
            'applicant_national_id' => $validated['applicant_national_id'],
            'applicant_name' => $validated['applicant_name'],
            'status' => 'Pending',
            'verification_method' => 'Manual',
        ]);

        ManualOperationLog::create([
            'user_id' => Auth::id(),
            'action' => 'land_verification_request_created',
            'target_type' => 'LandOwnershipVerification',
            'target_id' => (string) $verification->id,
            'details' => [
                'plot_number' => $landParcel->plot_number,
                'applicant_name' => $validated['applicant_name'],
            ],
        ]);

        return redirect()->route('admin.land-verifications.show', $verification)->with('success', 'Verification request created successfully.');
    }

    public function process(Request $request, LandOwnershipVerification $verification)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['InProgress', 'Verified', 'Rejected'])],
            'verification_result' => ['nullable', 'array'],
            'rejection_reason' => ['required_if:status,Rejected', 'nullable', 'string', 'max:2000'],
            'verification_method' => ['required', Rule::in(['Database', 'Manual', 'ExternalAPI'])],
        ]);

        $verification->update([
            'status' => $validated['status'],
            'verification_method' => $validated['verification_method'],
            'verification_result' => $validated['verification_result'] ?? null,
            'verified_by_admin_id' => in_array($validated['status'], ['Verified', 'Rejected']) ? Auth::id() : null,
            'verified_at' => in_array($validated['status'], ['Verified', 'Rejected']) ? now() : null,
            'rejection_reason' => $validated['rejection_reason'] ?? null,
        ]);

        // If verified, update the land parcel status
        if ($validated['status'] === 'Verified') {
            $verification->landParcel->update([
                'verification_status' => 'Verified',
                'verified_by_admin_id' => Auth::id(),
                'verified_at' => now(),
                'last_verification_date' => now(),
            ]);

            // Create ownership history entry if new owner
            $parcel = $verification->landParcel;
            if ($parcel->current_owner_national_id !== $verification->applicant_national_id) {
                // End current ownership
                $parcel->ownershipHistories()
                    ->whereNull('ownership_end_date')
                    ->latest()
                    ->first()?->update(['ownership_end_date' => now()]);

                // Create new ownership entry
                $parcel->ownershipHistories()->create([
                    'owner_name' => $verification->applicant_name,
                    'owner_national_id' => $verification->applicant_national_id,
                    'ownership_start_date' => now(),
                    'recorded_by_admin_id' => Auth::id(),
                ]);

                // Update parcel owner
                $parcel->update([
                    'current_owner_name' => $verification->applicant_name,
                    'current_owner_national_id' => $verification->applicant_national_id,
                ]);
            }
        }

        ManualOperationLog::create([
            'user_id' => Auth::id(),
            'action' => 'land_verification_processed',
            'target_type' => 'LandOwnershipVerification',
            'target_id' => (string) $verification->id,
            'details' => [
                'status' => $validated['status'],
                'verification_method' => $validated['verification_method'],
            ],
        ]);

        return back()->with('success', 'Verification request processed successfully.');
    }
}
