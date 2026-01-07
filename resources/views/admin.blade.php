@extends('layouts.mazer')

@section('title', 'Admin - Mazer')
@section('page-heading', 'Admin Panel')

@section('content')
<section class="section">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Overview</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">This is a static admin interface. Use the sidebar to navigate between pages. Authentication and permissions are not implemented at this stage.</p>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Users</h5>
                                    <p class="card-text">Manage user lists, profiles, and roles.</p>
                                    <a href="{{ url('/dashboard') }}" class="btn btn-outline-primary">Open</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Content</h5>
                                    <p class="card-text">Manage pages, posts, and media.</p>
                                    <a href="{{ url('/') }}" class="btn btn-outline-primary">Open</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Settings</h5>
                                    <p class="card-text">Update system configurations and preferences.</p>
                                    <a href="{{ url('/admin') }}" class="btn btn-outline-primary">Open</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
