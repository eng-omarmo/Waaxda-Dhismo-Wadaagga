@extends('layouts.mazer')
@section('title','Project Registration')

@section('content')
<div class="page-heading d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold">Project Registration</h3>
        <p class="text-subtitle text-muted">Submit new business license applications and link them to existing projects.</p>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Project Registration</li>
        </ol>
    </nav>
</div>

<div class="page-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0">Application Form</h5>
                </div>
                <div class="card-body pt-4">
                    @if (session('status'))
                        <div class="alert alert-light-success color-success d-flex align-items-center mb-4" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <div>{{ session('status') }}</div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('services.business-license.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold small text-uppercase"><i class="bi bi-hash me-1"></i>System ID</label>
                                <input type="text" class="form-control bg-light border-0 py-2" value="Auto-generated ID" readonly>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold small text-uppercase"><i class="bi bi-link-45deg me-1"></i>Project UUID Reference</label>
                                <input name="project_id" type="text" class="form-control py-2" placeholder="xxxxxxxx-xxxx-xxxx..." value="{{ old('project_id') }}">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase"><i class="bi bi-building me-1"></i>Company / Organization Name</label>
                            <input name="company_name" type="text" class="form-control py-2 @error('company_name') is-invalid @enderror" placeholder="Enter legal business name" value="{{ old('company_name') }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold small text-uppercase"><i class="bi bi-tags me-1"></i>License Type</label>
                                <select name="license_type" class="form-select py-2" required>
                                    <option value="" disabled selected>Select category</option>
                                    <option value="Rental">Rental Property</option>
                                    <option value="Commercial">Commercial Project</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold small text-uppercase"><i class="bi bi-cloud-arrow-up me-1"></i>Supporting Documents</label>
                                <input name="documents[]" type="file" class="form-control py-2" multiple accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                        </div>

                        <hr class="my-4 opacity-25">

                        <h6 class="fw-bold mb-3 text-primary">Registrant Information</h6>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-bold small text-uppercase">Contact Full Name</label>
                                <input name="registrant_name" type="text" class="form-control py-2" placeholder="Person responsible for application" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-uppercase">Email Address</label>
                                <input name="registrant_email" type="email" class="form-control py-2" placeholder="email@example.com" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-uppercase">Mobile Phone</label>
                                <input name="registrant_phone" type="tel" class="form-control py-2" placeholder="061xxxxxxx" required>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mt-5 pt-3 border-top">
                            <div class="d-flex align-items-center text-warning fw-bold">
                                <span class="badge bg-light-warning me-2"><i class="bi bi-hourglass-split"></i></span>
                                <span class="small">Pending Review</span>
                            </div>
                            <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow">
                                <i class="bi bi-send me-2"></i> Submit Application
                            </button>
                        </div>
                    </form>

                    @if ($errors->any())
                        <div class="alert alert-light-danger mt-4 border-0 shadow-sm">
                            <ul class="mb-0 small fw-bold">
                                @foreach ($errors->all() as $error)
                                    <li><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>


    </div>
</div>
@endsection
