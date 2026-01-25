<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Certificate;
use App\Models\OwnershipClaim;
use App\Models\OwnershipClaimChange;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminOwnershipController extends Controller
{
    public function index(Request $request)
    {
        $query = OwnershipClaim::with(['apartment', 'reviewer'])->latest();
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('id', 'like', '%'.$s.'%')
                    ->orWhere('claimant_name', 'like', '%'.$s.'%')
                    ->orWhere('claimant_email', 'like', '%'.$s.'%')
                    ->orWhere('claimant_phone', 'like', '%'.$s.'%');
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $claims = $query->paginate(10)->withQueryString();
        $claim = null;
        if ($request->filled('claim')) {
            $claim = OwnershipClaim::find($request->claim);
        }
        $apartments = Apartment::orderBy('name')->get(['id', 'name', 'address_city']);

        return view('admin.pages.ownership', compact('claims', 'claim', 'apartments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'apartment_id' => ['required', 'string', 'exists:apartments,id'],
            'claimant_name' => ['required', 'string', 'max:255'],
            'claimant_national_id' => ['required', 'string', 'max:100'],
            'claimant_phone' => ['nullable', 'string', 'max:50'],
            'claimant_email' => ['nullable', 'email', 'max:255'],
            'evidence_documents.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);
        $docs = [];
        if ($request->hasFile('evidence_documents')) {
            foreach ($request->file('evidence_documents') as $file) {
                $path = $file->store('ownership_claim_docs', 'public');
                $docs[] = [
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime' => $file->getClientMimeType(),
                ];
            }
        }
        $claim = OwnershipClaim::create(array_merge($validated, [
            'status' => 'Pending',
            'last_modified_by_admin_id' => Auth::id(),
            'evidence_documents' => $docs,
        ]));

        return redirect()->route('admin.ownership.index', ['claim' => $claim->id])->with('success', 'Claim created');
    }

    public function update(Request $request, OwnershipClaim $claim)
    {
        $validated = $request->validate([
            'claimant_name' => ['required', 'string', 'max:255'],
            'claimant_phone' => ['nullable', 'string', 'max:50'],
            'claimant_email' => ['nullable', 'email', 'max:255'],
            'reviewer_comments' => ['nullable', 'string', 'max:2000'],
            'evidence_documents.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);
        $before = $claim->getAttributes();
        $docs = (array) $claim->evidence_documents;
        if ($request->hasFile('evidence_documents')) {
            foreach ($request->file('evidence_documents') as $file) {
                $path = $file->store('ownership_claim_docs', 'public');
                $docs[] = [
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime' => $file->getClientMimeType(),
                ];
            }
        }
        $claim->fill(array_merge($validated, [
            'last_modified_by_admin_id' => Auth::id(),
            'evidence_documents' => $docs,
        ]));
        $claim->save();
        $changes = [];
        foreach ($validated as $k => $v) {
            $prev = $before[$k] ?? null;
            $curr = $claim->{$k};
            if ((string) $prev !== (string) $curr) {
                $changes[$k] = ['from' => $prev, 'to' => $curr];
            }
        }
        if (! empty($changes)) {
            OwnershipClaimChange::create([
                'claim_id' => $claim->id,
                'changed_by' => Auth::id(),
                'changes' => $changes,
            ]);
        }

        return back()->with('success', 'Claim updated');
    }

    public function approve(Request $request, OwnershipClaim $claim)
    {
        $validated = $request->validate([
            'approval_comment' => ['nullable', 'string', 'max:2000'],
            'digital_signature_svg' => ['required', 'string'],
        ]);
        $docs = (array) $claim->evidence_documents;
        if (empty($docs)) {
            return back()->withErrors([
                'support_document' => 'Supporting document required to verify claim profile.',
            ])->withInput();
        }
        $claim->status = 'Verified';
        $claim->reviewer_comments = $validated['approval_comment'] ?? null;
        $claim->reviewed_by_admin_id = Auth::id();
        $claim->reviewed_at = now();
        $claim->last_modified_by_admin_id = Auth::id();
        $claim->save();
        $service = Service::where('slug', 'ownership-certificate')->first();
        if ($service) {
            Certificate::create([
                'receiver_type' => OwnershipClaim::class,
                'receiver_id' => $claim->id,
                'service_id' => $service->id,
                'certificate_number' => 'IPAMS-OWN-'.date('Y').'-'.substr($claim->id, 0, 8).'-'.$service->id,
                'certificate_uid' => (string) \Illuminate\Support\Str::uuid(),
                'issued_at' => now(),
                'issued_by' => Auth::id(),
                'issued_to' => $claim->claimant_name,
                'certificate_hash' => hash('sha256', $claim->id.'|'.$service->id),
                'status' => 'valid',
                'metadata' => [
                    'fields' => [
                        'apartment_id' => $claim->apartment_id,
                        'claimant_name' => $claim->claimant_name,
                    ],
                ],
            ]);
        }

        return back()->with('success', 'Claim verified');
    }

    public function reject(Request $request, OwnershipClaim $claim)
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:2000'],
        ]);
        $claim->status = 'Rejected';
        $claim->reviewer_comments = $validated['rejection_reason'];
        $claim->reviewed_by_admin_id = Auth::id();
        $claim->reviewed_at = now();
        $claim->last_modified_by_admin_id = Auth::id();
        $claim->save();

        return back()->with('success', 'Claim rejected');
    }

    public function viewDoc(OwnershipClaim $claim, int $index)
    {
        $docs = (array) $claim->evidence_documents;
        if (! array_key_exists($index, $docs)) {
            abort(404);
        }
        $doc = $docs[$index] ?? [];
        $path = $doc['file_path'] ?? null;
        $name = $doc['file_name'] ?? 'document';
        if (! $path || ! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->response($path, $name);
    }
}
