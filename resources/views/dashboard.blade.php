@extends('layouts.mazer')

@section('title', 'Admin Dashboard')
@section('page-heading', 'Dashboard')

@section('content')
<section class="row">
    <div class="col-12 col-lg-9">
        @php
            $projectsCount = \App\Models\Project::count();
            $permitsCount = \App\Models\ApartmentConstructionPermit::count();
            $apartmentsCount = \App\Models\Apartment::count();
            $unitsCount = \App\Models\Unit::count();
            $licensesCount = \App\Models\BusinessLicense::count();
            $usersCount = \App\Models\User::count();
        @endphp
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
                                <h6 class="font-extrabold mb-0">{{ number_format($projectsCount) }}</h6>
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
                                <h6 class="font-extrabold mb-0">{{ number_format($permitsCount) }}</h6>
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
                                <h6 class="font-extrabold mb-0">{{ number_format($apartmentsCount) }}</h6>
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
                                <h6 class="font-extrabold mb-0">{{ number_format($unitsCount) }}</h6>
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
                                <h6 class="font-extrabold mb-0">{{ number_format($licensesCount) }}</h6>
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
                                <h6 class="font-extrabold mb-0">{{ number_format($usersCount) }}</h6>
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
                            @php
                                $pending = \App\Models\ServiceRequest::with('service')->where('status','pending')->latest()->limit(10)->get();
                            @endphp
                            @forelse($pending as $p)
                                @php
                                    $type = $p->service?->name ?? 'Service';
                                    $ref = (string) data_get($p->request_details,'transaction_id') ?: (string) data_get($p->request_details,'online_payment_id') ?: '—';
                                    $badge = 'bg-warning';
                                @endphp
                                <tr>
                                    <td>{{ $type }}</td>
                                    <td>{{ $ref }}</td>
                                    <td><span class="badge {{ $badge }}">Pending</span></td>
                                    <td>{{ $p->created_at?->format('Y-m-d') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center">No pending approvals</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4>Payments – Status Summary</h4>
            </div>
            <div class="card-body">
                @php
                    $pvSummary = \App\Models\PaymentVerification::selectRaw('status, COUNT(*) as cnt, SUM(amount) as total')
                        ->groupBy('status')->get()->keyBy('status');
                    $pvStatuses = ['pending','verified','rejected','discrepancy'];
                @endphp
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th class="text-end">Count</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pvStatuses as $s)
                                @php
                                    $row = $pvSummary[$s] ?? null;
                                    $count = $row?->cnt ?? 0;
                                    $total = $row?->total ?? 0;
                                    $badge = $s==='verified' ? 'bg-success' : ($s==='pending' ? 'bg-warning' : ($s==='rejected' ? 'bg-danger' : 'bg-secondary'));
                                @endphp
                                <tr>
                                    <td><span class="badge {{ $badge }}">{{ ucfirst($s) }}</span></td>
                                    <td class="text-end">{{ number_format($count) }}</td>
                                    <td class="text-end">${{ number_format($total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @php
            $recentPayments = \App\Models\PaymentVerification::with(['request.service','verifier'])->latest()->limit(10)->get();
        @endphp
        <div class="card">
            <div class="card-header">
                <h4>Recent Payments</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>Service</th>
                                <th class="text-end">Amount</th>
                                <th>Status</th>
                                <th>Verified</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentPayments as $pv)
                                @php
                                    $svc = $pv->request?->service?->name ?? '—';
                                    $badge = $pv->status==='verified' ? 'bg-success' : ($pv->status==='pending' ? 'bg-warning' : ($pv->status==='rejected' ? 'bg-danger' : 'bg-secondary'));
                                @endphp
                                <tr>
                                    <td class="text-muted">{{ $pv->reference_number }}</td>
                                    <td>{{ $svc }}</td>
                                    <td class="text-end">${{ number_format($pv->amount,2) }}</td>
                                    <td><span class="badge {{ $badge }}">{{ ucfirst($pv->status) }}</span></td>
                                    <td>{{ $pv->verified_at?->format('Y-m-d H:i') ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">No payments found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-3">
        @php
            $todayCount = \App\Models\OnlinePayment::where('status','completed')->whereDate('verified_at', now()->toDateString())->count();
            $todayTotal = \App\Models\OnlinePayment::where('status','completed')->whereDate('verified_at', now()->toDateString())->sum('amount');
            $weekCount = \App\Models\OnlinePayment::where('status','completed')->whereBetween('verified_at', [now()->startOfWeek(), now()])->count();
            $weekTotal = \App\Models\OnlinePayment::where('status','completed')->whereBetween('verified_at', [now()->startOfWeek(), now()])->sum('amount');
            $monthCount = \App\Models\OnlinePayment::where('status','completed')->whereYear('verified_at', now()->year)->whereMonth('verified_at', now()->month)->count();
            $monthTotal = \App\Models\OnlinePayment::where('status','completed')->whereYear('verified_at', now()->year)->whereMonth('verified_at', now()->month)->sum('amount');
            $initiatedCount = \App\Models\OnlinePayment::where('status','initiated')->count();
            $initiatedTotal = \App\Models\OnlinePayment::where('status','initiated')->sum('amount');
        @endphp
        <div class="card">
            <div class="card-header">
                <h4>Payments Summary</h4>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <div class="text-muted">Today Completed</div>
                    <div><span class="badge bg-success">{{ $todayCount }}</span> ${{ number_format($todayTotal,2) }}</div>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <div class="text-muted">Week Completed</div>
                    <div><span class="badge bg-success">{{ $weekCount }}</span> ${{ number_format($weekTotal,2) }}</div>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <div class="text-muted">Month Completed</div>
                    <div><span class="badge bg-success">{{ $monthCount }}</span> ${{ number_format($monthTotal,2) }}</div>
                </div>
                <div class="d-flex justify-content-between">
                    <div class="text-muted">Initiated</div>
                    <div><span class="badge bg-warning">{{ $initiatedCount }}</span> ${{ number_format($initiatedTotal,2) }}</div>
                </div>
            </div>
        </div>
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
