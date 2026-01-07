@extends('layouts.mazer')

@section('title', 'Landing - Mazer')
@section('page-heading', 'Welcome to Mazer')

@section('content')
<section class="section">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-md-6">
                <h1 class="display-5 fw-bold">Modern Admin Dashboard Template</h1>
                <p class="mt-3 text-muted">Build fast, responsive, and beautiful interfaces with Mazer. Designed for developers and teams who value clean UI and great UX.</p>
                <div class="mt-4">
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-lg me-2">View Dashboard</a>
                    <a href="{{ url('/login') }}" class="btn btn-outline-primary btn-lg">Get Started</a>
                </div>
            </div>
            <div class="col-md-6 text-center">
                <img src="{{ asset('assets/images/samples/bg-mountain.jpg') }}" alt="Hero" class="img-fluid rounded">
            </div>
        </div>

        <div class="row text-center mb-5">
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="stats-icon blue mb-3 mx-auto">
                            <i class="bi bi-speedometer2"></i>
                        </div>
                        <h4>Fast Performance</h4>
                        <p class="text-muted">Optimized assets and components for smooth user experience on all devices.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="stats-icon green mb-3 mx-auto">
                            <i class="bi bi-phone"></i>
                        </div>
                        <h4>Mobile Responsive</h4>
                        <p class="text-muted">Fully responsive layout built with Bootstrap and modern CSS.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="stats-icon purple mb-3 mx-auto">
                            <i class="bi bi-ui-checks-grid"></i>
                        </div>
                        <h4>Rich Components</h4>
                        <p class="text-muted">Widgets, charts, forms, and more to kickstart your admin panels.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <h4 class="text-center mb-4">What Users Say</h4>
                        <div class="row">
                            <div class="col-md-4 text-center mb-4">
                                <img src="{{ asset('assets/images/faces/1.jpg') }}" class="rounded-circle mb-2" width="80" height="80" alt="User">
                                <p class="mb-0">“Mazer helps us deliver features faster with a delightful UI.”</p>
                                <small class="text-muted">Product Manager</small>
                            </div>
                            <div class="col-md-4 text-center mb-4">
                                <img src="{{ asset('assets/images/faces/2.jpg') }}" class="rounded-circle mb-2" width="80" height="80" alt="User">
                                <p class="mb-0">“Clean components and responsive design out of the box.”</p>
                                <small class="text-muted">Frontend Engineer</small>
                            </div>
                            <div class="col-md-4 text-center mb-4">
                                <img src="{{ asset('assets/images/faces/3.jpg') }}" class="rounded-circle mb-2" width="80" height="80" alt="User">
                                <p class="mb-0">“Great starting point for admin interfaces.”</p>
                                <small class="text-muted">CTO</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
