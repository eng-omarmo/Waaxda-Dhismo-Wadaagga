@extends('layouts.mazer')
@section('title','Engineer Licenses')
@section('page-heading','Professional Engineer Licenses')
@section('content')
@if (session('success'))
  <div class="alert alert-success" role="alert">{{ session('success') }}</div>
@endif

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Applicant</th>
            <th>Field</th>
            <th>University</th>
            <th>Status</th>
            <th>Submitted At</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($licenses as $l)
            <tr>
              <td>{{ $l->id }}</td>
              <td>
                <div class="fw-bold">{{ $l->applicant_name }}</div>
                <div class="text-muted small">{{ $l->email }}</div>
              </td>
              <td>{{ $l->engineering_field }}</td>
              <td>{{ $l->university }} ({{ $l->graduation_year }})</td>
              <td>
                <span class="badge bg-{{ $l->status === 'Approved' ? 'success' : ($l->status === 'Pending' ? 'warning' : 'danger') }}">
                  {{ $l->status }}
                </span>
              </td>
              <td>{{ $l->created_at->toDateTimeString() }}</td>
              <td class="text-end">
                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approve-{{ $l->id }}" @disabled($l->status==='Approved')>Approve</button>
                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#reject-{{ $l->id }}" @disabled($l->status==='Rejected')>Reject</button>
              </td>
            </tr>

            <!-- Approve Modal -->
            <div class="modal fade" id="approve-{{ $l->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Approve License</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form method="POST" action="{{ route('admin.engineer-licenses.approve', $l) }}">
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

            <!-- Reject Modal -->
            <div class="modal fade" id="reject-{{ $l->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Reject License</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form method="POST" action="{{ route('admin.engineer-licenses.reject', $l) }}">
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
              <td colspan="7" class="text-center text-muted">No applications found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-3">
      {{ $licenses->links() }}
    </div>
  </div>
</div>
@endsection
