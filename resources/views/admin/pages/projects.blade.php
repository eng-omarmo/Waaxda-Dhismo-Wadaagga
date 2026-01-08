@extends('layouts.mazer')
@section('title','Projects')
@section('page-heading','Project Management')
@section('content')
@if (session('status'))
  <div class="alert alert-success" role="alert">{{ session('status') }}</div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0">All Projects</h5>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registerModal" aria-controls="registerModal" aria-expanded="false">
    <i class="bi bi-plus-circle me-1"></i> Register New Project
  </button>
</div>

<div class="card mb-3">
  <div class="card-body">
    <form class="row g-3" method="GET" action="{{ route('admin.projects') }}" aria-label="Filter and search projects">
      <div class="col-md-4">
        <label class="form-label">Search by name</label>
        <input name="q" class="form-control" value="{{ request('q') }}" placeholder="e.g., Daru Salaam">
      </div>
      <div class="col-md-3">
        <label class="form-label">Filter by status</label>
        <select name="status" class="form-select">
          <option value="">All</option>
          @foreach ($statuses as $s)
            <option value="{{ $s }}" @selected(request('status')===$s)>{{ $s }}</option>
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
        <a href="{{ route('admin.projects') }}" class="btn btn-outline-secondary">Reset</a>
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
            <th>Project</th>
            <th>Status</th>
            <th>Registered</th>
            <th>Last Updated</th>
            <th>Owners / Team</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($projects as $p)
            <tr>
              <td>
                <div class="fw-bold">{{ $p->project_name }}</div>
                <div class="text-muted small">{{ $p->location_text }}</div>
              </td>
              <td>
                @php
                  $badge = match ($p->status) {
                    'Draft' => 'bg-secondary',
                    'Submitted' => 'bg-warning',
                    'Approved' => 'bg-success',
                    default => 'bg-light text-dark',
                  };
                  $icon = match ($p->status) {
                    'Draft' => 'bi-pencil',
                    'Submitted' => 'bi-hourglass-split',
                    'Approved' => 'bi-check-circle',
                    default => 'bi-dot',
                  };
                @endphp
                <span class="badge {{ $badge }}"><i class="bi {{ $icon }} me-1"></i>{{ $p->status }}</span>
              </td>
              <td>{{ $p->created_at?->toDateTimeString() }}</td>
              <td>{{ $p->updated_at?->toDateTimeString() }}</td>
              <td>
                <div class="small">
                  <div>Registrant: {{ $p->registrant_name }} · {{ $p->registrant_email }}</div>
                  <div>Developer: {{ $p->developer?->first_name ? ($p->developer->first_name.' '.$p->developer->last_name) : 'Unassigned' }}</div>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center text-muted">No projects found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-3">
      {{ $projects->links() }}
    </div>
  </div>
</div>

<div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Register New Project</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('admin.projects.store') }}" novalidate>
        @csrf
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Project name</label>
              <input name="project_name" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Location</label>
              <input name="location_text" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Assign developer (optional)</label>
              <select name="developer_id" class="form-select">
                <option value="">Unassigned</option>
                @foreach ($developers as $d)
                  <option value="{{ $d->id }}">{{ $d->first_name }} {{ $d->last_name }} ({{ $d->email }})</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Status</label>
              <select name="status" class="form-select" required>
                @foreach ($statuses as $s)
                  <option value="{{ $s }}">{{ $s }}</option>
                @endforeach
              </select>
            </div>
          </div>
          @if ($errors->any())
            <div class="alert alert-danger mt-3" role="alert">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Register</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
