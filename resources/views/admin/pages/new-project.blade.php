@extends('layouts.mazer')
@section('title','Project Registration')
@section('page-heading','Project Registration')

@section('content')
<div class="page-content">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-4">Contact Details</h5>
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <div class="fw-bold mb-2">Please fix the following:</div>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                    @endif
                    <form method="POST" action="{{ route('admin.projects.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name</label>
                                <input name="registrant_name" type="text" class="form-control" placeholder="e.g., Mohamed Ali" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input name="registrant_phone" type="tel" class="form-control" placeholder="061XXXXXXX" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Email</label>
                                <input name="registrant_email" type="email" class="form-control" placeholder="you@example.com" required>
                            </div>
                        </div>

                        <h5 class="mt-4 mb-3">Project Details</h5>
                        <div class="mb-3">
                            <label class="form-label">Project ID</label>
                            <input type="text" class="form-control" value="Auto-generated on submission" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Project Name</label>
                            <input name="project_name" type="text" class="form-control" placeholder="e.g., Daru Salaam Apartments Phase II" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input name="location_text" type="text" class="form-control" placeholder="District, neighborhood or map reference" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Developer (optional)</label>
                            <select name="developer_id" class="form-select">
                                <option value="">— None —</option>
                                @foreach ($developers as $dev)
                                <option value="{{ $dev->id }}">{{ $dev->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                @foreach ($statuses as $s)
                                <option value="{{ $s }}" @selected(old('status', 'Draft' )===$s)>{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Created At</label>
                            <input type="text" class="form-control" value="{{ date('Y-m-d') }}" disabled>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Save Draft</button>
                            <button type="button" class="btn btn-success" onclick="document.querySelector('select[name=status]').value='Submitted'; this.closest('form').submit();">Submit Registration</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">About Project Registration</h6>
                    <p class="text-muted">All services attach to projects. Register the project first to proceed with permits, buildings, units, licensing, ownership and transfers.</p>
                    <hr>
                    <div class="list-group small">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Construction Permits
                            <span class="badge bg-light text-primary">After submission</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Buildings & Units
                            <span class="badge bg-light text-primary">After approval</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Licensing
                            <span class="badge bg-light text-primary">After approval</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Ownership & Transfers
                            <span class="badge bg-light text-primary">After unit creation</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
