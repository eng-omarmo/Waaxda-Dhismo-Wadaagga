@extends('layouts.mazer')
@section('title','Ownership Verification')
@section('page-heading','Land Ownership Verification')
@section('content')
<div class="row">
    <div class="col-12">
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Ownership Claims</h4>
                <form method="GET" action="{{ route('admin.ownership.index') }}" class="row g-2 mt-2" aria-label="Filter ownership claims">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search by ID, name, phone" value="{{ request('search') }}" aria-label="Search claims">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select" aria-label="Filter by status">
                            <option value="">Any Status</option>
                            <option value="Pending" @selected(request('status')==='Pending' )>Pending</option>
                            <option value="Verified" @selected(request('status')==='Verified' )>Verified</option>
                            <option value="Rejected" @selected(request('status')==='Rejected' )>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-outline-primary w-100">Apply</button>
                    </div>
                </form>
            </div>
            <div class="card-body">
                <table class="table table-hover table-responsive" aria-label="Ownership claims list">
                    <thead>
                        <tr>
                            <th>Claim ID</th>
                            <th>Apartment</th>
                            <th>Status</th>
                            <th>Last Modified</th>
                            <th>Modified By</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($claims ?? [] as $c)
                        <tr>
                            <td>{{ $c->id }}</td>
                            <td>
                                @if($c->apartment)
                                <a href="{{ route('admin.apartments.show', $c->apartment) }}">{{ $c->apartment->name }}</a>
                                @else
                                {{ $c->apartment_id }}
                                @endif
                            </td>
                            <td><span class="badge bg-{{ $c->status==='Verified' ? 'success' : ($c->status==='Pending' ? 'warning' : 'danger') }}">{{ $c->status }}</span></td>
                            <td>{{ $c->updated_at?->toDateTimeString() }}</td>
                            <td>{{ optional($c->reviewer)->first_name }} {{ optional($c->reviewer)->last_name }}</td>
                            <td>
                                <a href="{{ route('admin.ownership.index', ['claim' => $c->id]) }}" class="btn btn-sm btn-outline-primary">Manage</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No claims found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                @if(isset($claims)) <x-pagination :paginator="$claims" /> @endif
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">New Ownership Claim</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.ownership.store') }}" aria-label="Create ownership claim" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Apartment</label>
                        <select name="apartment_id" class="form-select" required aria-required="true">
                            <option value="">Select apartment</option>
                            @foreach($apartments as $apt)
                            <option value="{{ $apt->id }}" @selected(request('apartment_id')===$apt->id)>{{ $apt->name }} — {{ $apt->address_city }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Claimant Full Name</label>
                        <input type="text" name="claimant_name" class="form-control" required aria-required="true">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">National ID</label>
                            <input type="text" name="claimant_national_id" class="form-control" required aria-required="true">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" name="claimant_phone" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="claimant_email" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Supporting Documents (PDF, JPG, PNG - max 10MB each)</label>
                        <input type="file" name="evidence_documents[]" class="form-control" multiple required aria-required="true" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Create Claim</button>
                </form>
            </div>
        </div>
    </div>
</div>

@if($claim)
<div class="row mt-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Manage Claim: {{ $claim->id }}</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.ownership.update', $claim) }}" aria-label="Edit ownership claim">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Claimant Full Name</label>
                        <input type="text" name="claimant_name" class="form-control" value="{{ old('claimant_name', $claim->claimant_name) }}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" name="claimant_phone" class="form-control" value="{{ old('claimant_phone', $claim->claimant_phone) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="claimant_email" class="form-control" value="{{ old('claimant_email', $claim->claimant_email) }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reviewer Comments</label>
                        <textarea name="reviewer_comments" class="form-control" rows="3">{{ old('reviewer_comments', $claim->reviewer_comments) }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-outline-primary">Save Changes</button>
                </form>
                <hr>
                <h5 class="mb-3">Supporting Documents</h5>
                @php($docs = (array) $claim->evidence_documents)
                @if(!empty($docs))
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($docs as $i => $d)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ \Illuminate\Support\Arr::get($d, 'file_name') }}</td>
                            <td>{{ \Illuminate\Support\Arr::get($d, 'mime') }}</td>
                            <td>
                                <a class="btn btn-sm btn-outline-secondary" target="_blank" href="{{ route('admin.ownership.documents.view', [$claim, $i]) }}">View</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="text-muted">No supporting documents uploaded.</div>
                @endif
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <form method="POST" action="{{ route('admin.ownership.approve', $claim) }}" aria-label="Approve claim">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Approval Comment</label>
                                <textarea name="approval_comment" class="form-control" rows="3" placeholder="Notes"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Electronic Signature</label>
                                <div class="border rounded p-2">
                                    <canvas id="signaturePad" width="600" height="180" style="width:100%" aria-label="Signature pad"></canvas>
                                </div>
                                <input type="hidden" name="digital_signature_svg" id="signatureData" required>
                                <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="clearSignature">Clear</button>
                            </div>
                            <button type="submit" class="btn btn-success">Approve</button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form method="POST" action="{{ route('admin.ownership.reject', $claim) }}" aria-label="Reject claim">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Rejection Reason</label>
                                <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger">Reject</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Change History</h4>
            </div>
            <div class="card-body">
                @php($history = \App\Models\OwnershipClaimChange::where('claim_id', $claim->id)->latest()->get())
                <table class="table">
                    <thead>
                        <tr>
                            <th>When</th>
                            <th>By</th>
                            <th>Changes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $h)
                        <tr>
                            <td>{{ $h->created_at?->toDateTimeString() }}</td>
                            <td>{{ optional(\App\Models\User::find($h->changed_by))->first_name }} {{ optional(\App\Models\User::find($h->changed_by))->last_name }}</td>
                            <td>
                                @php($chg = (array) $h->changes)
                                @foreach($chg as $k => $v)
                                <div><strong>{{ $k }}:</strong> {{ \Illuminate\Support\Arr::get($v,'from') }} → {{ \Illuminate\Support\Arr::get($v,'to') }}</div>
                                @endforeach
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center">No changes recorded.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('page-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var canvas = document.getElementById('signaturePad');
        var clearBtn = document.getElementById('clearSignature');
        var hidden = document.getElementById('signatureData');
        if (!canvas) return;
        var ctx = canvas.getContext('2d');
        var drawing = false;
        var lastPos = {
            x: 0,
            y: 0
        };

        function getPos(e) {
            var rect = canvas.getBoundingClientRect();
            var x = (e.touches ? e.touches[0].clientX : e.clientX) - rect.left;
            var y = (e.touches ? e.touches[0].clientY : e.clientY) - rect.top;
            return {
                x: x,
                y: y
            };
        }

        function updateHidden() {
            var dataUrl = canvas.toDataURL('image/png');
            hidden.value = dataUrl;
        }
        canvas.addEventListener('mousedown', function(e) {
            drawing = true;
            lastPos = getPos(e);
        });
        canvas.addEventListener('mousemove', function(e) {
            if (!drawing) return;
            var pos = getPos(e);
            ctx.beginPath();
            ctx.moveTo(lastPos.x, lastPos.y);
            ctx.lineTo(pos.x, pos.y);
            ctx.strokeStyle = '#111';
            ctx.lineWidth = 2;
            ctx.stroke();
            lastPos = pos;
            updateHidden();
        });
        canvas.addEventListener('mouseup', function() {
            drawing = false;
            updateHidden();
        });
        canvas.addEventListener('mouseleave', function() {
            drawing = false;
        });
        canvas.addEventListener('touchstart', function(e) {
            drawing = true;
            lastPos = getPos(e);
        }, {
            passive: true
        });
        canvas.addEventListener('touchmove', function(e) {
            if (!drawing) return;
            var pos = getPos(e);
            ctx.beginPath();
            ctx.moveTo(lastPos.x, lastPos.y);
            ctx.lineTo(pos.x, pos.y);
            ctx.strokeStyle = '#111';
            ctx.lineWidth = 2;
            ctx.stroke();
            lastPos = pos;
            updateHidden();
        }, {
            passive: true
        });
        canvas.addEventListener('touchend', function() {
            drawing = false;
            updateHidden();
        });
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                hidden.value = '';
            });
        }
    });
</script>
@endpush
