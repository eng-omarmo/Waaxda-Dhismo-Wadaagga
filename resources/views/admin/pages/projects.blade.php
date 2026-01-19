@extends('layouts.mazer')
@section('title','Projects')
@section('page-heading','Project Management')
@section('content')
@if (session('status'))
<div class="alert alert-success" role="alert">{{ session('status') }}</div>
@endif

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
                    <option value="date" @selected(($sort ?? 'date' )==='date' )>Registration date</option>
                    <option value="alpha" @selected(($sort ?? '' )==='alpha' )>Alphabetical</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Direction</label>
                <select name="direction" class="form-select">
                    <option value="desc" @selected(($direction ?? 'desc' )==='desc' )>Descending</option>
                    <option value="asc" @selected(($direction ?? '' )==='asc' )>Ascending</option>
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
                            <div class="text-muted small">{{ $p->id }}</div>
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
                                <div class="d-flex align-items-center gap-2">
                                    <span>Developer: {{ $p->developer?->name ?? 'Unassigned' }}</span>
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#assignDev-{{ $p->id }}" aria-controls="assignDev-{{ $p->id }}">Assign</button>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.projects.edit', $p) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="{{ route('admin.projects.destroy', $p) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <div class="modal fade" id="assignDev-{{ $p->id }}" tabindex="-1" aria-labelledby="assignDevLabel-{{ $p->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 id="assignDevLabel-{{ $p->id }}" class="modal-title">Assign Developer</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="POST" action="{{ route('admin.projects.assignDeveloper', $p) }}">
                                    @csrf
                                    <div class="modal-body">
                                        <label class="form-label">Organization</label>
                                        <select name="developer_id" class="form-select">
                                            <option value="">Unassigned</option>
                                            @foreach ($developers as $d)
                                            <option value="{{ $d->id }}" @selected($p->developer_id===$d->id)>{{ $d->name }} ({{ $d->type }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No projects found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            <x-pagination :paginator="$projects" />
        </div>
    </div>
</div>


@endsection
