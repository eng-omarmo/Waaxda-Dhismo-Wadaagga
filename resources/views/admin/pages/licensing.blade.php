@extends('layouts.mazer')
@section('title','Licensing')
@section('page-heading','Licensing & Commercial Approvals')
@section('content')
@if (session('status'))
  <div class="alert alert-success" role="alert">{{ session('status') }}</div>
@endif

<div class="card mb-3">
  <div class="card-body">
    <form class="row g-3" method="GET" action="{{ route('admin.licensing.index') }}" aria-label="Filter licenses">
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
        <a href="{{ route('admin.licensing.index') }}" class="btn btn-outline-secondary">Reset</a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>Approval ID</th>
            <th>Company</th>
            <th>Type</th>
            <th>Status</th>
            <th>Verification</th>
            <th>Expires</th>
            <th>Last Modified</th>
            <th>Modified By</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($licenses as $l)
            <tr>
              @php($lastChange = \App\Models\BusinessLicenseChange::where('license_id', $l->id)->latest()->first())
              <td class="text-muted small">{{ $l->id }}</td>
              <td>
                <div class="fw-bold">{{ $l->company_name }}</div>
                <div class="text-muted small">Project: {{ $l->project_id ?: '—' }}</div>
              </td>
              <td>{{ $l->license_type }}</td>
              <td>
                <span class="badge {{ $l->status_badge_class }}">{{ ucfirst($l->status) }}</span>
              </td>
              <td>{{ ucfirst($l->verification_status) }}</td>
              <td>{{ $l->expires_at ? \Illuminate\Support\Carbon::parse($l->expires_at)->toDateString() : '—' }}</td>
              <td>{{ $lastChange?->created_at?->toDateTimeString() ?? $l->updated_at?->toDateTimeString() ?? '—' }}</td>
              <td>
                @php($modUser = $lastChange ? \App\Models\User::find($lastChange->changed_by) : null)
                {{ $modUser ? ($modUser->first_name.' '.$modUser->last_name) : '—' }}
              </td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.licensing.edit', $l) }}">Manage</a>
                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approve-{{ $l->id }}" @disabled($l->status==='approved')>Approve</button>
                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#reject-{{ $l->id }}" @disabled($l->status==='rejected')>Reject</button>
              </td>
            </tr>
            <div class="modal fade" id="approve-{{ $l->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Approve License</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form method="POST" action="{{ route('admin.licensing.approve', $l) }}">
                    @csrf
                    <div class="modal-body">
                      <label class="form-label">Comments (optional)</label>
                      <textarea name="admin_comments" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-success">Approve</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <div class="modal fade" id="reject-{{ $l->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Reject License</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form method="POST" action="{{ route('admin.licensing.reject', $l) }}">
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
          @empty
            <tr>
              <td colspan="7" class="text-center text-muted">No licenses found.</td>
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
@endsection
