@extends('layouts.mazer')
@section('title','Edit Project')
@section('page-heading','Edit Project Registration')

@section('content')
<div class="page-content">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">

                    <h5 class="mb-4">Contact Details</h5>

                    <form method="POST" action="{{ route('admin.projects.update', $project->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name</label>
                                <input name="registrant_name" type="text"
                                       class="form-control"
                                       value="{{ old('registrant_name', $project->registrant_name) }}"
                                       required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input name="registrant_phone" type="tel"
                                       class="form-control"
                                       value="{{ old('registrant_phone', $project->registrant_phone) }}"
                                       required>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Email</label>
                                <input name="registrant_email" type="email"
                                       class="form-control"
                                       value="{{ old('registrant_email', $project->registrant_email) }}"
                                       required>
                            </div>
                        </div>

                        <h5 class="mt-4 mb-3">Project Details</h5>

                        <div class="mb-3">
                            <label class="form-label">Project ID</label>
                            <input type="text" class="form-control"
                                   value="{{ $project->id }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Project Name</label>
                            <input name="project_name" type="text"
                                   class="form-control"
                                   value="{{ old('project_name', $project->project_name) }}"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input name="location_text" type="text"
                                   class="form-control"
                                   value="{{ old('location_text', $project->location_text) }}"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Developer</label>
                            <select name="developer_id" class="form-select">
                                <option value="">— Not Assigned —</option>
                                @foreach($developers as $developer)
                                    <option value="{{ $developer->id }}"
                                        @selected(old('developer_id', $project->developer_id) == $developer->id)>
                                        {{ $developer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="Draft" @selected($project->status === 'Draft')>Draft</option>
                                <option value="Submitted" @selected($project->status === 'Submitted')>Submitted</option>
                                <option value="Approved" disabled>Approved (Admin only)</option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                Update Project
                            </button>

                            @if($project->status === 'Draft')
                                <button type="button"
                                        class="btn btn-success"
                                        onclick="submitFinal()">
                                    Submit Registration
                                </button>
                            @endif
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3">Project Status</h6>
                    <p class="text-muted mb-2">
                        Current Status:
                        <span class="badge bg-info">{{ $project->status }}</span>
                    </p>
                    <hr>
                    <p class="small text-muted">
                        Once submitted, the project becomes read-only until reviewed by an administrator.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function submitFinal() {
    if (confirm('Submit this project for official review?')) {
        document.querySelector('select[name="status"]').value = 'Submitted';
        document.querySelector('form').submit();
    }
}
</script>
@endsection
