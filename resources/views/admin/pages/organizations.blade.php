@extends('layouts.mazer')
@section('title','Organizations')
@section('page-heading','Organization Management')
@section('content')
@if (session('status'))
  <div class="alert alert-success" role="alert">{{ session('status') }}</div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0">All Organizations</h5>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal" aria-controls="createModal" aria-expanded="false" id="create">Create Organization</button>
</div>

<div class="card mb-3">
  <div class="card-body">
    <form class="row g-3" method="GET" action="{{ route('admin.organizations.index') }}" aria-label="Filter and search organizations">
      <div class="col-md-4">
        <label class="form-label">Search</label>
        <input name="q" class="form-control" value="{{ request('q') }}" placeholder="Name, registration, contact email">
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
        <label class="form-label">Sort</label>
        <select name="sort" class="form-select">
          <option value="date" @selected(($sort ?? 'date')==='date')>Registration date</option>
          <option value="alpha" @selected(($sort ?? '')==='alpha')>Alphabetical</option>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">Direction</label>
        <select name="direction" class="form-select">
          <option value="desc" @selected(($direction ?? 'desc')==='desc')>Descending</option>
          <option value="asc" @selected(($direction ?? '')==='asc')>Ascending</option>
        </select>
      </div>
      <div class="col-12 d-flex gap-2">
        <button type="submit" class="btn btn-outline-primary"><i class="bi bi-filter me-1"></i> Apply</button>
        <a href="{{ route('admin.organizations.index') }}" class="btn btn-outline-secondary">Reset</a>
      </div>
    </form>
  </div>
></div>

<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>Name</th>
            <th>Status</th>
            <th>Registered</th>
            <th>Updated</th>
            <th>Contact</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($organizations as $o)
            <tr>
              <td>
                <div class="fw-bold">{{ $o->name }}</div>
                <div class="text-muted small">{{ $o->type }} · {{ $o->registration_number }}</div>
              </td>
              <td>
                @php
                  $badge = match ($o->status) {
                    'pending' => 'bg-warning',
                    'approved' => 'bg-success',
                    'rejected' => 'bg-danger',
                    default => 'bg-light text-dark',
                  };
                @endphp
                <span class="badge {{ $badge }}">{{ ucfirst($o->status) }}</span>
              </td>
              <td>{{ $o->created_at?->toDateTimeString() }}</td>
              <td>{{ $o->updated_at?->toDateTimeString() }}</td>
              <td class="small">
                <div>{{ $o->contact_full_name }} · {{ $o->contact_role }}</div>
                <div>{{ $o->contact_email }} · {{ $o->contact_phone }}</div>
              </td>
              <td class="text-end">
                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#edit-{{ $o->id }}">Edit</button>
                <form method="POST" action="{{ route('admin.organizations.approve', $o) }}" class="d-inline">
                  @csrf
                  <button class="btn btn-sm btn-success" @disabled($o->status==='approved')>Approve</button>
                </form>
                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#reject-{{ $o->id }}" @disabled($o->status==='rejected')>Reject</button>
              </td>
            </tr>
            <div class="modal fade" id="edit-{{ $o->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Edit Organization</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form method="POST" action="{{ route('admin.organizations.update', $o) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                      <div class="row g-3">
                        <div class="col-md-6">
                          <label class="form-label">Name</label>
                          <input name="name" class="form-control" value="{{ $o->name }}" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Registration number</label>
                          <input name="registration_number" class="form-control" value="{{ $o->registration_number }}">
                        </div>
                        <div class="col-md-12">
                          <label class="form-label">Address</label>
                          <input name="address" class="form-control" value="{{ $o->address }}" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Type</label>
                          <select name="type" class="form-select" required>
                            @foreach (['Developer','Contractor','Consultant','Other'] as $t)
                              <option value="{{ $t }}" @selected($o->type===$t)>{{ $t }}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Status</label>
                          <select name="status" class="form-select" required>
                            @foreach ($statuses as $s)
                              <option value="{{ $s }}" @selected($o->status===$s)>{{ ucfirst($s) }}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Contact name</label>
                          <input name="contact_full_name" class="form-control" value="{{ $o->contact_full_name }}" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Contact role</label>
                          <input name="contact_role" class="form-control" value="{{ $o->contact_role }}" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Contact phone</label>
                          <input name="contact_phone" class="form-control" value="{{ $o->contact_phone }}" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Contact email</label>
                          <input name="contact_email" type="email" class="form-control" value="{{ $o->contact_email }}" required>
                        </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <div class="modal fade" id="reject-{{ $o->id }}" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Reject Organization</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <form method="POST" action="{{ route('admin.organizations.reject', $o) }}">
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
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted">No organizations found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-3">
      {{ $organizations->links() }}
    </div>
  </div>
</div>

<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create Organization</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('admin.organizations.store') }}">
        @csrf
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Name</label>
              <input name="name" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Registration number</label>
              <input name="registration_number" class="form-control">
            </div>
            <div class="col-md-12">
              <label class="form-label">Address</label>
              <input name="address" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Type</label>
              <select name="type" class="form-select" required>
                @foreach (['Developer','Contractor','Consultant','Other'] as $t)
                  <option value="{{ $t }}">{{ $t }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Status</label>
              <select name="status" class="form-select" required>
                @foreach ($statuses as $s)
                  <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Contact name</label>
              <input name="contact_full_name" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Contact role</label>
              <input name="contact_role" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Contact phone</label>
              <input name="contact_phone" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Contact email</label>
              <input name="contact_email" type="email" class="form-control" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
