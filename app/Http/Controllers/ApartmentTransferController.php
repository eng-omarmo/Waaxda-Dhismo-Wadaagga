<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\ApartmentTransfer;
use App\Models\OwnerProfile;
use App\Models\OwnershipHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApartmentTransferController extends Controller
{
    public function index()
    {
        $query = ApartmentTransfer::query()->orderBy('transfer_date', 'desc');
        if (request()->filled('search')) {
            $term = request()->string('search')->toString();
            $query->where('transfer_reference_number', 'like', '%'.$term.'%')
                ->orWhere('apartment_number', 'like', '%'.$term.'%')
                ->orWhere('previous_owner_id', 'like', '%'.$term.'%')
                ->orWhere('new_owner_id', 'like', '%'.$term.'%');
        }
        if (request()->filled('status')) {
            $query->where('approval_status', request()->string('status')->toString());
        }
        if (request()->filled('from')) {
            $query->whereDate('transfer_date', '>=', request()->string('from')->toString());
        }
        if (request()->filled('to')) {
            $query->whereDate('transfer_date', '<=', request()->string('to')->toString());
        }
        $perPage = min(max((int) request()->query('per_page', 10), 1), 100);
        $ApartmentTransfer = $query->paginate($perPage)->withQueryString();

        return view('admin.apartments.Transfer.index', [
            'transfers' => $ApartmentTransfer,
        ]);
    }

    public function create()
    {
        return view('admin.apartments.Transfer.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'transfer_reference_number' => 'required|string|max:64',
            'transfer_date' => 'required|date',
            'apartment_number' => 'required|string|max:255',
            'unit_number' => 'nullable|string|max:255',
            'previous_owner_name' => 'required|string|max:255',
            'previous_owner_id' => 'required|string|max:64',
            'new_owner_name' => 'required|string|max:255',
            'new_owner_id' => 'required|string|max:64',
            'transfer_reason' => 'required|in:Sale,Inheritance,Gift',
            'supporting_documents' => 'nullable|file',
        ]);

        $dup = ApartmentTransfer::where('apartment_number', $request->apartment_number)
            ->where('previous_owner_id', $request->previous_owner_id)
            ->where('new_owner_id', $request->new_owner_id)
            ->whereDate('transfer_date', $request->transfer_date)
            ->where('approval_status', 'Pending')
            ->first();
        if ($dup) {
            return back()->withErrors(['transfer_reference_number' => 'Duplicate pending transfer request detected'])->withInput();
        }

        $apartment = Apartment::query()
            ->where('id', $request->apartment_number)
            ->orWhere('name', $request->apartment_number)
            ->first();
        if (! $apartment) {
            return back()->withErrors(['apartment_number' => 'Apartment not found'])->withInput();
        }

        $previousOwner = OwnerProfile::where('national_id', $request->previous_owner_id)->first();
        if (! $previousOwner) {
            $previousOwner = OwnerProfile::create([
                'full_name' => $request->previous_owner_name,
                'national_id' => $request->previous_owner_id,
            ]);
        }
        if (! $apartment->owner_profile_id) {
            $apartment->owner_profile_id = $previousOwner->id;
            $apartment->save();
            OwnershipHistory::create([
                'apartment_id' => $apartment->id,
                'owner_profile_id' => $previousOwner->id,
                'started_at' => $request->transfer_date,
                'recorded_by_admin_id' => Auth::id(),
            ]);
        }

        $newOwner = OwnerProfile::where('national_id', $request->new_owner_id)->first();
        if (! $newOwner) {
            $newOwner = OwnerProfile::create([
                'full_name' => $request->new_owner_name,
                'national_id' => $request->new_owner_id,
            ]);
        }

        $transfer = ApartmentTransfer::create([
            'transfer_reference_number' => $request->transfer_reference_number,
            'apartment_number' => $apartment->id,
            'unit_number' => $request->unit_number,
            'previous_owner_name' => $previousOwner->full_name,
            'previous_owner_id' => $previousOwner->national_id,
            'new_owner_name' => $newOwner->full_name,
            'new_owner_id' => $newOwner->national_id,
            'transfer_reason' => $request->transfer_reason,
            'transfer_date' => $request->transfer_date,
            'supporting_documents_path' => null,
            'approval_status' => 'Pending',
            'owner_profile_previous_id' => $previousOwner->id,
            'owner_profile_new_id' => $newOwner->id,
        ]);

        OwnershipHistory::where('apartment_id', $apartment->id)
            ->whereNull('ended_at')
            ->latest()
            ->first()?->update(['ended_at' => $request->transfer_date, 'transfer_reference_number' => $transfer->transfer_reference_number]);

        $apartment->owner_profile_id = $newOwner->id;
        $apartment->save();
        OwnershipHistory::create([
            'apartment_id' => $apartment->id,
            'owner_profile_id' => $newOwner->id,
            'started_at' => $request->transfer_date,
            'transfer_reference_number' => $transfer->transfer_reference_number,
            'recorded_by_admin_id' => Auth::id(),
        ]);

        \App\Models\ManualOperationLog::create([
            'user_id' => Auth::id(),
            'action' => 'transfer_request_created',
            'target_type' => 'ApartmentTransfer',
            'target_id' => (string) $transfer->id,
            'details' => [
                'apartment_id' => $apartment->id,
                'previous_owner' => $previousOwner->national_id,
                'new_owner' => $newOwner->national_id,
                'transfer_date' => $request->transfer_date,
            ],
        ]);

        return redirect()->route('admin.apartment-transfers.index')
            ->with('success', 'Apartment transfer created successfully.');
    }

    public function approve(Request $request, ApartmentTransfer $transfer)
    {
        $request->validate([
            'approval_reason' => 'nullable|string|max:1000',
            'digital_signature_svg' => 'nullable|string',
        ]);
        $transfer->approval_status = 'Approved';
        $transfer->approved_by_admin_id = Auth::id();
        $transfer->approved_at = now();
        $transfer->approval_reason = $request->approval_reason;
        $transfer->digital_signature_svg = $request->digital_signature_svg;
        $transfer->save();
        \App\Models\ManualOperationLog::create([
            'user_id' => Auth::id(),
            'action' => 'transfer_request_approved',
            'target_type' => 'ApartmentTransfer',
            'target_id' => (string) $transfer->id,
            'details' => [
                'reason' => $request->approval_reason,
            ],
        ]);

        return back()->with('success', 'Transfer approved');
    }

    public function reject(Request $request, ApartmentTransfer $transfer)
    {
        $request->validate([
            'approval_reason' => 'required|string|max:1000',
        ]);
        $transfer->approval_status = 'Rejected';
        $transfer->approved_by_admin_id = Auth::id();
        $transfer->approved_at = now();
        $transfer->approval_reason = $request->approval_reason;
        $transfer->save();
        \App\Models\ManualOperationLog::create([
            'user_id' => Auth::id(),
            'action' => 'transfer_request_rejected',
            'target_type' => 'ApartmentTransfer',
            'target_id' => (string) $transfer->id,
            'details' => [
                'reason' => $request->approval_reason,
            ],
        ]);

        return back()->with('success', 'Transfer rejected');
    }

    public function deed(ApartmentTransfer $transfer)
    {
        $apartment = \App\Models\Apartment::find($transfer->apartment_number);
        $html = '<div style="font-family:Arial,sans-serif;padding:24px"><h2 style="margin:0">Property Transfer Deed</h2><div style="margin-top:8px">Reference: '.e($transfer->transfer_reference_number).'</div><hr><div>Apartment: '.e($apartment?->name ?? $transfer->apartment_number).' • Unit: '.e($transfer->unit_number).'</div><div>From: '.e($transfer->previous_owner_name).' ('.e($transfer->previous_owner_id).')</div><div>To: '.e($transfer->new_owner_name).' ('.e($transfer->new_owner_id).')</div><div>Reason: '.e($transfer->transfer_reason).'</div><div>Date: '.e($transfer->transfer_date?->toDateString()).'</div><hr><div>Approved: '.e($transfer->approved_at?->toDateTimeString() ?: 'Pending').'</div></div>';
        \Barryvdh\DomPDF\Facade\Pdf::setOptions(['dpi' => 300]);
        $pdfData = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4')->output();
        $fileName = 'TransferDeed_'.$transfer->transfer_reference_number.'.pdf';

        return response($pdfData, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
