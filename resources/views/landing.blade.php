@extends('layouts.mazer')

@section('title', 'Department of Property & Apartment Management')
@section('page-heading', 'Integrated Property & Apartment Management System (IPAMS)')

@section('content')
<section class="section">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-md-7">
                <h1 class="display-6 fw-bold">Department of Property & Apartment Management</h1>
                <p class="mt-3 text-muted">IPAMS provides transparent, citizen-friendly services for project registration, construction permits, apartment and unit registration, licensing, ownership verification, property transfer, and inspections.</p>
                <div class="mt-4">
                    <a href="{{ url('/login') }}" class="btn btn-primary btn-lg me-2">Register Project</a>
                    <a href="{{ url('/login') }}" class="btn btn-outline-primary btn-lg me-2">Verify Ownership</a>
                    <a href="{{ url('/dashboard') }}" class="btn btn-outline-secondary btn-lg">Track Application</a>
                </div>
            </div>
            <div class="col-md-5 text-center">
                <img src="{{ asset('assets/images/samples/building.jpg') }}" alt="IPAMS" class="img-fluid rounded">
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12">
                <h3 class="mb-3">Current Leadership</h3>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <img src="{{ asset('assets/images/faces/1.jpg') }}" class="rounded-circle mb-3" width="96" height="96" alt="Leader">
                        <h5 class="mb-0">Amina Farah</h5>
                        <small class="text-muted">Director General</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <img src="{{ asset('assets/images/faces/2.jpg') }}" class="rounded-circle mb-3" width="96" height="96" alt="Leader">
                        <h5 class="mb-0">Mohamed Isse</h5>
                        <small class="text-muted">Deputy Director</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <img src="{{ asset('assets/images/faces/3.jpg') }}" class="rounded-circle mb-3" width="96" height="96" alt="Leader">
                        <h5 class="mb-0">Safiya Ali</h5>
                        <small class="text-muted">Chief Registrar</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12">
                <h3 class="mb-3">Core Public Services</h3>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5>Project Registration</h5>
                        <p class="text-muted mb-0">Register new development projects and obtain a unique project ID.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5>Construction Permits</h5>
                        <p class="text-muted mb-0">Apply for and manage construction permits and approvals.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5>Apartment & Unit Registration</h5>
                        <p class="text-muted mb-0">Register buildings and individual units for official records.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5>Licensing</h5>
                        <p class="text-muted mb-0">Obtain and renew licenses for property-related activities.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5>Ownership Verification</h5>
                        <p class="text-muted mb-0">Verify custodial records of unit ownership and assignments.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5>Property Transfer</h5>
                        <p class="text-muted mb-0">Record transfers of ownership between parties.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5>Inspections</h5>
                        <p class="text-muted mb-0">Schedule and track site inspections and compliance checks.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-12">
                <h3 class="mb-3">How the System Works</h3>
            </div>
            <div class="col-lg-10 mx-auto">
                <div class="row g-4">
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <div class="stats-icon blue mx-auto mb-3"><i class="bi bi-briefcase"></i></div>
                                <h6 class="mb-1">Step 1</h6>
                                <h5>Register Project</h5>
                                <p class="text-muted mb-0">Create project record and submit initial details.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <div class="stats-icon green mx-auto mb-3"><i class="bi bi-clipboard-check"></i></div>
                                <h6 class="mb-1">Step 2</h6>
                                <h5>Permits & Approvals</h5>
                                <p class="text-muted mb-0">Obtain construction permits and compliance approvals.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <div class="stats-icon purple mx-auto mb-3"><i class="bi bi-building"></i></div>
                                <h6 class="mb-1">Step 3</h6>
                                <h5>Building Certification</h5>
                                <p class="text-muted mb-0">Register building completion and certification.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <div class="stats-icon red mx-auto mb-3"><i class="bi bi-house-door"></i></div>
                                <h6 class="mb-1">Step 4</h6>
                                <h5>Unit Registration</h5>
                                <p class="text-muted mb-0">Register individual units and custodial ownership.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <a href="{{ url('/login') }}" class="btn btn-primary btn-lg me-2">Register Project</a>
                <a href="{{ url('/login') }}" class="btn btn-outline-primary btn-lg me-2">Verify Ownership</a>
                <a href="{{ url('/dashboard') }}" class="btn btn-outline-secondary btn-lg">Track Application</a>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-lg-10 mx-auto">
                <div class="alert alert-secondary" role="alert">
                    Legal disclaimer: Ownership verification in IPAMS is custodial and does not constitute a land title.
                </div>
            </div>
        </div>
    </div>
    @endsection
