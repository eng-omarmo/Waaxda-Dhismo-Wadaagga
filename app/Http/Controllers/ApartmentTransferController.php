<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\ApartmentTransfer;
use App\Models\OwnerProfile;
use App\Models\OwnershipHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
        $apartments = Apartment::orderBy('name')->get(['id', 'name', 'address_city']);

        return view('admin.apartments.Transfer.create', compact('apartments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'transfer_date' => 'required|date',
            'apartment_id' => 'required|string|exists:apartments,id',
            'apartment_number' => 'nullable|string|max:255',
            'unit_number' => 'nullable|string|max:255',
            'previous_owner_name' => 'required|string|max:255',
            'previous_owner_id' => 'required|string|max:64',
            'new_owner_name' => 'required|string|max:255',
            'new_owner_id' => 'required|string|max:64',
            'transfer_reason' => 'required|in:Sale,Inheritance,Gift',
            'supporting_documents' => 'nullable|file',
        ]);

        $apartment = Apartment::find($request->apartment_id);
        if (! $apartment && $request->filled('apartment_number')) {
            $apartment = Apartment::query()
                ->where('id', $request->apartment_number)
                ->orWhere('name', $request->apartment_number)
                ->first();
        }
        if (! $apartment) {
            return back()->withErrors(['apartment_id' => 'Apartment not found'])->withInput();
        }

        $dup = ApartmentTransfer::where('apartment_number', $apartment->id)
            ->where('previous_owner_id', $request->previous_owner_id)
            ->where('new_owner_id', $request->new_owner_id)
            ->whereDate('transfer_date', $request->transfer_date)
            ->where('approval_status', 'Pending')
            ->first();
        if ($dup) {
            return back()->withErrors(['transfer_date' => 'Duplicate pending transfer request detected'])->withInput();
        }

        $ref = null;
        do {
            $ref = 'IPAMS-TRF-'.date('Y').'-'.substr($apartment->id, 0, 8).'-'.substr((string) Str::uuid(), 0, 8);
        } while (ApartmentTransfer::where('transfer_reference_number', $ref)->exists());

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
            'transfer_reference_number' => $ref,
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
        $propCity = $apartment?->address_city ?: '';
        $propName = $apartment?->name ?: (string) $transfer->apartment_number;
        $unit = (string) ($transfer->unit_number ?: '');
        $dateStr = $transfer->transfer_date?->toDateString() ?: '';
        $approvedStr = $transfer->approved_at?->toDateTimeString() ?: 'Pending';
        $signatureImg = '';
        if ($transfer->digital_signature_svg && str_starts_with($transfer->digital_signature_svg, 'data:')) {
            $signatureImg = '<img src="'.e($transfer->digital_signature_svg).'" alt="Digital Signature" style="max-width:180px;max-height:80px">';
        }
        $legalDescription = 'Apartment: '.e($propName).($unit ? ' • Unit: '.e($unit) : '').($propCity ? ' • City: '.e($propCity) : '');
        $html = '
<html>
<head>
<meta charset="utf-8">
<style>
@page { size: A4; margin: 20mm; }
body { font-family: Arial, sans-serif; font-size: 12pt; line-height: 1.6; color: #222; }
.wrap { position: relative; }
.title { font-size: 20pt; font-weight: 700; text-align: center; margin-bottom: 6mm; }
.meta { text-align: center; font-size: 11pt; color: #555; margin-bottom: 6mm; }
.section { margin-bottom: 6mm; }
.section h3 { font-size: 14pt; margin: 0 0 2mm 0; padding: 0; }
.box { border: 1px solid #999; padding: 4mm; border-radius: 2px; }
.grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4mm; }
.small { font-size: 10pt; color: #555; }
.footer { position: fixed; bottom: 10mm; left: 0; right: 0; text-align: center; font-size: 10pt; color: #555; }
.pagenum:before { content: counter(page); }
.pagetotal:before { content: counter(pages); }
.watermark { position: fixed; top: 40%; left: 50%; transform: translate(-50%, -50%) rotate(-30deg); font-size: 60pt; color: rgba(0,0,0,0.08); z-index: 0; }
.content { position: relative; z-index: 1; }
.signature-line { border-top: 1px solid #000; width: 60mm; margin-top: 8mm; }
.signature-block { display: flex; gap: 10mm; align-items: flex-end; }
.notary { border: 1px dashed #999; padding: 4mm; }
.crop { position: fixed; width: 100%; height: 100%; top: 0; left: 0; pointer-events: none; }
.crop .tl, .crop .tr, .crop .bl, .crop .br { position: absolute; width: 20mm; height: 20mm; }
.crop .tl { top: 0; left: 0; border-left: 0.5pt solid #999; border-top: 0.5pt solid #999; }
.crop .tr { top: 0; right: 0; border-right: 0.5pt solid #999; border-top: 0.5pt solid #999; }
.crop .bl { bottom: 0; left: 0; border-left: 0.5pt solid #999; border-bottom: 0.5pt solid #999; }
.crop .br { bottom: 0; right: 0; border-right: 0.5pt solid #999; border-bottom: 0.5pt solid #999; }
</style>
</head>
<body>
<div class="wrap">
  <div class="watermark">IPAMS</div>
  <div class="content">
    <div class="title">Property Transfer Deed</div>
    <div class="meta">Reference: '.e($transfer->transfer_reference_number).' • Date: '.e($dateStr).'</div>

    <div class="section box">
      <h3>Parties</h3>
      <div class="grid">
        <div>
          <div><strong>Grantor (Previous Owner)</strong></div>
          <div>Full Legal Name: '.e($transfer->previous_owner_name).'</div>
          <div>National ID: '.e($transfer->previous_owner_id).'</div>
          <div>Address: '.e('').' </div>
        </div>
        <div>
          <div><strong>Grantee (New Owner)</strong></div>
          <div>Full Legal Name: '.e($transfer->new_owner_name).'</div>
          <div>National ID: '.e($transfer->new_owner_id).'</div>
          <div>Address: '.e('').' </div>
        </div>
      </div>
    </div>

    <div class="section box">
      <h3>Property Description</h3>
      <div>'.($legalDescription).'</div>
      <div class="small">Complete legal description to be appended if required by jurisdiction.</div>
    </div>

    <div class="section box">
      <h3>Consideration</h3>
      <div>For good and valuable consideration, the receipt and sufficiency of which are hereby acknowledged, the Grantor agrees to convey the Property to the Grantee.</div>
    </div>

    <div class="section box">
      <h3>Granting Clause</h3>
      <div>The Grantor hereby conveys, grants, bargains, sells, and transfers to the Grantee all right, title, and interest in and to the Property, together with all improvements and appurtenances thereto, subject to recorded easements, covenants, and restrictions of record.</div>
    </div>

    <div class="section box">
      <h3>Habendum Clause</h3>
      <div>To have and to hold the Property unto the Grantee, the Grantee\'s heirs, successors, and assigns forever, free and clear of all liens and encumbrances except as expressly stated herein.</div>
    </div>

    <div class="section box">
      <h3>Jurisdictional Clause</h3>
      <div>This conveyance complies with applicable municipal and national laws. Any state or locality-specific clauses required by law shall be attached hereto and made part of this Deed.</div>
    </div>

    <div class="section box">
      <h3>Execution</h3>
      <div class="signature-block">
        <div>
          <div class="signature-line"></div>
          <div>Grantor Signature</div>
          <div>Name: '.e($transfer->previous_owner_name).'</div>
        </div>
        <div>
          <div class="signature-line"></div>
          <div>Grantee Signature</div>
          <div>Name: '.e($transfer->new_owner_name).'</div>
        </div>
        <div>
          '.($signatureImg ?: '<div class="signature-line" style="width:40mm"></div>').'
          <div>Authorized Officer</div>
          <div class="small">Approval: '.e($approvedStr).'</div>
        </div>
      </div>
    </div>

    <div class="section notary">
      <h3>Notary Acknowledgment</h3>
      <div>State/Province: ____________________</div>
      <div>County/District: ____________________</div>
      <div>On this ____ day of __________, 20____, before me, the undersigned Notary Public, personally appeared ____________________, known to me or satisfactorily proven to be the person(s) whose name(s) is/are subscribed to the foregoing instrument, and acknowledged that he/she/they executed the same for the purposes therein contained.</div>
      <div>Notary Public Signature: ____________________</div>
      <div>Commission Expires: ____________________</div>
      <div>Seal:</div>
      <div style="border:1px solid #000; width:30mm; height:30mm;"></div>
    </div>

    <div class="section box">
      <h3>Security Notice</h3>
      <div>This document contains a security watermark and page numbering. Unauthorized reproduction may be subject to applicable laws.</div>
    </div>

  </div>
  <div class="footer">Page <span class="pagenum"></span> of <span class="pagetotal"></span></div>
  <div class="crop"><div class="tl"></div><div class="tr"></div><div class="bl"></div><div class="br"></div></div>
</div>
</body>
</html>
';
        \Barryvdh\DomPDF\Facade\Pdf::setOptions(['dpi' => 300]);
        $pdfData = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)->setPaper('a4')->output();
        $fileName = 'TransferDeed_'.$transfer->transfer_reference_number.'.pdf';

        return response($pdfData, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function ownerProfile(Apartment $apartment)
    {
        $owner = null;
        if ($apartment->owner_profile_id) {
            $owner = OwnerProfile::find($apartment->owner_profile_id);
        }

        return response()->json([
            'apartment' => [
                'id' => $apartment->id,
                'name' => $apartment->name,
                'city' => $apartment->address_city,
            ],
            'owner_name' => $owner?->full_name,
            'owner_id' => $owner?->national_id,
        ]);
    }

    public function ownersLookup(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $nid = trim((string) $request->query('national_id', ''));
        $query = OwnerProfile::query();
        if ($nid !== '') {
            $owner = $query->where('national_id', $nid)->first();
            if ($owner) {
                return response()->json([
                    'data' => [[
                        'id' => $owner->id,
                        'full_name' => $owner->full_name,
                        'national_id' => $owner->national_id,
                    ]],
                ]);
            }

            return response()->json(['data' => []]);
        }
        if ($q === '') {
            return response()->json(['data' => []]);
        }
        $owners = $query
            ->where(function ($sub) use ($q) {
                $sub->where('full_name', 'like', '%'.$q.'%')
                    ->orWhere('national_id', 'like', '%'.$q.'%');
            })
            ->orderBy('full_name')
            ->limit(10)
            ->get(['id', 'full_name', 'national_id']);

        return response()->json(['data' => $owners]);
    }
}
