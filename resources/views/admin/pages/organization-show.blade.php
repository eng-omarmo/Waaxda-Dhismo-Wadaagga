@extends('layouts.mazer')
@section('title','Organization Details')
@section('page-heading','Organization Details')
@section('content')
<div class="row">
  <div class="col-lg-8">
    <div class="card mb-3">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="mb-0">{{ $organization->name }}</h5>
          <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.organizations.index') }}">Back</a>
        </div>
        <hr>
        <div class="row g-3">
          <div class="col-md-6">
            <div class="small text-muted">Registration number</div>
            <div>{{ $organization->registration_number ?: '—' }}</div>
          </div>
          <div class="col-md-6">
            <div class="small text-muted">Type</div>
            <div>{{ $organization->type }}</div>
          </div>
          <div class="col-md-12">
            <div class="small text-muted">Address</div>
            <div>{{ $organization->address }}</div>
          </div>
          <div class="col-md-6">
            <div class="small text-muted">Status</div>
            @php
              $badge = match ($organization->status) {
                'pending' => 'bg-warning',
                'approved' => 'bg-success',
                'rejected' => 'bg-danger',
                default => 'bg-light text-dark',
              };
            @endphp
            <span class="badge {{ $badge }}">{{ ucfirst($organization->status) }}</span>
          </div>
          <div class="col-md-6">
            <div class="small text-muted">Updated</div>
            <div>{{ $organization->updated_at?->toDateTimeString() }}</div>
          </div>
        </div>
        @if ($organization->admin_notes)
          <hr>
          <div>
            <div class="small text-muted">Admin notes</div>
            <div>{{ $organization->admin_notes }}</div>
          </div>
        @endif
        <hr>
        <div class="row g-3">
          <div class="col-md-6">
            <div class="small text-muted">Contact name</div>
            <div>{{ $organization->contact_full_name }}</div>
          </div>
          <div class="col-md-6">
            <div class="small text-muted">Contact role</div>
            <div>{{ $organization->contact_role }}</div>
          </div>
          <div class="col-md-6">
            <div class="small text-muted">Contact phone</div>
            <div>{{ $organization->contact_phone }}</div>
          </div>
          <div class="col-md-6">
            <div class="small text-muted">Contact email</div>
            <div>{{ $organization->contact_email }}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h6 class="mb-3">Documents</h6>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>File</th>
                <th>Type</th>
                <th>Label</th>
                <th>Uploaded</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($documents as $doc)
                <tr>
                  <td>{{ $doc->file_name }}</td>
                  <td>{{ strtoupper($doc->file_type) }}</td>
                  <td>{{ $doc->document_label ?: '—' }}</td>
                  <td>{{ $doc->created_at?->toDateTimeString() }}</td>
                  <td class="text-end">
                    @php
                      $url = \Illuminate\Support\Facades\Storage::disk('public')->url($doc->file_path);
                    @endphp
                    <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-outline-primary">Preview</a>
                    <a href="{{ route('admin.organizations.documents.download', [$organization, $doc]) }}" class="btn btn-sm btn-outline-secondary">Download</a>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted">No documents uploaded.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h6 class="mb-3">Actions</h6>
        <form method="POST" action="{{ route('admin.organizations.approve', $organization) }}" class="d-inline">
          @csrf
          <button class="btn btn-success btn-sm" @disabled($organization->status==='approved')>Approve</button>
        </form>
        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal" @disabled($organization->status==='rejected')>Reject</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Reject Organization</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('admin.organizations.reject', $organization) }}">
        @csrf
        <div class="modal-body">
          <label class="form-label">Reason</label>
          <textarea name="admin_notes" class="form-control" rows="3" placeholder="Reason for rejection"></textarea>
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

