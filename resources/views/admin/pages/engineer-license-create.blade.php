@extends('layouts.mazer')
@section('title', 'Issue Engineer License')
@section('page-heading', 'Issue New Engineer License')

@section('content')
<div class="row">
    <div class="col-12 col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Engineer Details</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.engineer-licenses.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Applicant Full Name</label>
                            <input type="text" name="applicant_name" class="form-control @error('applicant_name') is-invalid @enderror" value="{{ old('applicant_name') }}" required>
                            @error('applicant_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">National ID</label>
                            <input type="text" name="national_id" class="form-control @error('national_id') is-invalid @enderror" value="{{ old('national_id') }}" required>
                            @error('national_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Engineering Field</label>
                            <input type="text" name="engineering_field" class="form-control @error('engineering_field') is-invalid @enderror" value="{{ old('engineering_field') }}" placeholder="e.g. Civil Engineering, Electrical Engineering" required>
                            @error('engineering_field')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">University</label>
                            <input type="text" name="university" class="form-control @error('university') is-invalid @enderror" value="{{ old('university') }}">
                            @error('university')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Graduation Year</label>
                            <input type="number" name="graduation_year" class="form-control @error('graduation_year') is-invalid @enderror" value="{{ old('graduation_year', date('Y')) }}">
                            @error('graduation_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Initial Status</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="Pending" {{ old('status') == 'Pending' ? 'selected' : '' }}>Pending (Needs Review)</option>
                                <option value="Approved" {{ old('status') == 'Approved' ? 'selected' : '' }}>Approved (Active)</option>
                                <option value="Rejected" {{ old('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Admin Comments</label>
                            <textarea name="admin_comments" class="form-control @error('admin_comments') is-invalid @enderror" rows="3">{{ old('admin_comments') }}</textarea>
                            @error('admin_comments')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.engineer-licenses.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Issue License</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
