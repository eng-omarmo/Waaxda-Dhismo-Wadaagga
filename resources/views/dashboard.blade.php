@extends('layouts.mazer')

@section('title', 'Admin Dashboard')
@section('page-heading', 'Dashboard')

@section('content')
<section class="row">
    <div class="col-12 col-lg-9">
        <div class="row">
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon purple">
                                    <i class="bi bi-briefcase"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Projects</h6>
                                <h6 class="font-extrabold mb-0">128</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon blue">
                                    <i class="bi bi-clipboard-check"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Permits</h6>
                                <h6 class="font-extrabold mb-0">76</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon green">
                                    <i class="bi bi-building"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Buildings</h6>
                                <h6 class="font-extrabold mb-0">54</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon red">
                                    <i class="bi bi-grid-3x3-gap"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Units</h6>
                                <h6 class="font-extrabold mb-0">1,240</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon purple">
                                    <i class="bi bi-patch-check"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Licenses</h6>
                                <h6 class="font-extrabold mb-0">32</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon blue">
                                    <i class="bi bi-person-badge"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Ownership Claims</h6>
                                <h6 class="font-extrabold mb-0">89</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon green">
                                    <i class="bi bi-arrow-left-right"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Transfers</h6>
                                <h6 class="font-extrabold mb-0">21</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-3 py-4-5">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-icon red">
                                    <i class="bi bi-people"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="text-muted font-semibold">Users</h6>
                                <h6 class="font-extrabold mb-0">64</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4>Pending Approvals</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Reference</th>
                                <th>Status</th>
                                <th>Submitted</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Permit</td>
                                <td>PRM-2026-0012</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                                <td>2026-01-05</td>
                            </tr>
                            <tr>
                                <td>Ownership Claim</td>
                                <td>OWN-UNIT-3A</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                                <td>2026-01-06</td>
                            </tr>
                            <tr>
                                <td>Transfer</td>
                                <td>TRF-UNIT-5C</td>
                                <td><span class="badge bg-warning">Requested</span></td>
                                <td>2026-01-06</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-3">
        <div class="card">
            <div class="card-header">
                <h4>Recent Activity</h4>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Project submitted: Daru Salaam II
                        <span class="badge bg-light text-primary">Today</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Permit approved: PRM-2026-0009
                        <span class="badge bg-light text-primary">Yesterday</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Ownership claim filed: Unit 3A
                        <span class="badge bg-light text-primary">2 days ago</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Transfer requested: Unit 5C
                        <span class="badge bg-light text-primary">2 days ago</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4>Quick Actions</h4>
            </div>
            <div class="card-body d-grid gap-2">
                <a href="{{ url('/admin/projects') }}" class="btn btn-outline-primary">Open Projects</a>
                <a href="{{ url('/admin/permits') }}" class="btn btn-outline-primary">Open Permits</a>
                <a href="{{ url('/admin/buildings') }}" class="btn btn-outline-primary">Open Buildings</a>
                <a href="{{ url('/admin/ownership') }}" class="btn btn-outline-primary">Open Ownership</a>
                <a href="{{ url('/admin/transfers') }}" class="btn btn-outline-primary">Open Transfers</a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary">Open Users</a>
            </div>
        </div>
    </div>
</section>
@endsection

@push('page-scripts')
<script></script>
@endpush
