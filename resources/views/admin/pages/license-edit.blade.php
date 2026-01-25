@extends('layouts.mazer')
@section('title','Manage License')
@section('page-heading','Licensing & Commercial Approvals – Edit')
@section('content')
@if (session('status'))
<div class="alert alert-success" role="alert">{{ session('status') }}</div>
@endif

<div class="card mb-3">
    <div class="card-body">
        <form class="row g-3" method="GET" action="{{ route('admin.licensing.edit', $license) }}" aria-label="Filter approvals">
            <div class="col-md-4">
                <label class="form-label">Search by company</label>
                <input name="q" class="form-control" value="{{ request('q') }}" placeholder="Company name">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All</option>
                    @foreach ($statuses as $s)
                    <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Submission date</label>
                <input name="date" type="date" class="form-control" value="{{ request('date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Actions</label><br>
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-filter me-1"></i> Apply</button>
                <a href="{{ route('admin.licensing.edit', $license) }}" class="btn btn-outline-secondary">Reset</a>
                <a href="{{ route('admin.licensing.index') }}" class="btn btn-outline-dark">Back to list</a>
            </div>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Approval ID</th>
                        <th>Company</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Last Modified</th>
                        <th>Modified By</th>
                        <th class="text-end">Manage</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($licenses as $l)
                    @php($lastChange = \App\Models\BusinessLicenseChange::where('license_id', $l->id)->latest()->first())
                    <tr @class(['table-active'=> $l->id === $license->id])>
                        <td class="text-muted small">{{ $l->id }}</td>
                        <td>
                            <div class="fw-bold">{{ $l->company_name }}</div>
                            <div class="text-muted small">Project: {{ $l->project_id ?: '—' }}</div>
                        </td>
                        <td>{{ $l->license_type }}</td>
                        <td>
                            <span class="badge {{ $l->status_badge_class }}">{{ ucfirst($l->status) }}</span>
                        </td>
                        <td>{{ $lastChange?->created_at?->toDateTimeString() ?? $l->updated_at?->toDateTimeString() ?? '—' }}</td>
                        <td>
                            @php($modUser = $lastChange ? \App\Models\User::find($lastChange->changed_by) : null)
                            {{ $modUser ? ($modUser->first_name.' '.$modUser->last_name) : '—' }}
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.licensing.edit', $l) }}" class="btn btn-sm btn-outline-secondary">Open</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No approvals found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            <x-pagination :paginator="$licenses" />
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit Approval</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.licensing.save', $license) }}">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Company Name</label>
                            <input name="company_name" class="form-control" value="{{ old('company_name',$license->company_name) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Project ID</label>
                            <input name="project_id" class="form-control" value="{{ old('project_id',$license->project_id) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">License Type</label>
                            <select name="license_type" class="form-select" required>
                                @foreach (['Rental','Commercial'] as $t)
                                <option value="{{ $t }}" @selected(old('license_type',$license->license_type)===$t)>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                @foreach ($statuses as $s)
                                <option value="{{ $s }}" @selected(old('status',$license->status)===$s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Verification Status</label>
                            <select name="verification_status" class="form-select" required>
                                @foreach ($verificationStatuses as $v)
                                <option value="{{ $v }}" @selected(old('verification_status',$license->verification_status)===$v)>{{ ucfirst($v) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Expires At</label>
                            <input name="expires_at" type="date" class="form-control" value="{{ old('expires_at', $license->expires_at?->toDateString()) }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Admin Comments</label>
                        <textarea name="admin_comments" class="form-control" rows="3">{{ old('admin_comments',$license->admin_comments) }}</textarea>
                    </div>
                    <h6 class="mt-3 mb-2">Registrant Contact</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Full Name</label>
                            <input name="registrant_name" class="form-control" value="{{ old('registrant_name',$license->registrant_name) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Email</label>
                            <input name="registrant_email" type="email" class="form-control" value="{{ old('registrant_email',$license->registrant_email) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Phone</label>
                            <input name="registrant_phone" class="form-control" value="{{ old('registrant_phone',$license->registrant_phone) }}">
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a href="{{ route('admin.licensing.index') }}" class="btn btn-outline-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Version Comparison</h6>
            </div>
            <div class="card-body">
                @if ($previousChange)
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Previous</th>
                                <th>Current</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($previousChange->changes as $field => $diff)
                            <tr>
                                <td class="text-muted">{{ $field }}</td>
                                <td>{{ is_array($diff['from']) ? json_encode($diff['from']) : $diff['from'] }}</td>
                                <td>{{ data_get($license, $field) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-muted">No previous changes.</div>
                @endif
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Approval Workflow</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.licensing.approve', $license) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Comments/Notes</label>
                        <textarea name="admin_comments" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success" @disabled($license->status==='approved')>Approve</button>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal" @disabled($license->status==='rejected')>Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h6 class="mb-0">Change History</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>When</th>
                        <th>Who</th>
                        <th>Changes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($history as $h)
                    @php($user = \App\Models\User::find($h->changed_by))
                    <tr>
                        <td>{{ $h->created_at?->toDateTimeString() }}</td>
                        <td>{{ $user ? ($user->first_name.' '.$user->last_name) : $h->changed_by }}</td>
                        <td>
                            <ul class="mb-0">
                                @foreach ($h->changes as $field => $diff)
                                <li><strong>{{ $field }}</strong>: {{ is_array($diff['from']) ? json_encode($diff['from']) : $diff['from'] }} → {{ is_array($diff['to']) ? json_encode($diff['to']) : $diff['to'] }}</li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-muted text-center">No changes recorded.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject License</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.licensing.reject', $license) }}">
                @csrf
                <div class="modal-body">
                    <label class="form-label">Comments (optional)</label>
                    <textarea name="admin_comments" class="form-control" rows="3"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
