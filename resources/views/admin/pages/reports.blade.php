@extends('layouts.mazer')
@section('title', 'Services & payments overview')
@section('page-heading', 'Reports')

@section('content')
<div class="page-heading" id="overview">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-8 order-md-1 order-last">
                <h3>Services &amp; payments overview</h3>
                <p class="text-subtitle text-muted mb-0">Consolidated view of manual service requests, verified service payments, and online registration payments for the selected period.</p>
            </div>
            <div class="col-12 col-md-4 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Reports</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Report period</h4>
                <p class="text-muted small mb-0">Online payments use the <strong>recorded date</strong>. Service payments use the <strong>payment date</strong>. New requests use the <strong>request created date</strong>.</p>
            </div>
            <div class="card-body">
                <form method="get" action="{{ route('admin.reports') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">From</label>
                        <input type="date" name="from" class="form-control" value="{{ $fromStr }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">To</label>
                        <input type="date" name="to" class="form-control" value="{{ $toStr }}" required>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary">Apply</button>
                        <a href="{{ route('admin.reports') }}" class="btn btn-light-secondary">Last 30 days</a>
                        <a href="{{ route('admin.payments.index', ['from' => $fromStr, 'to' => $toStr]) }}" class="btn btn-outline-primary">Open payment list</a>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- KPI row -->
    <section class="row">
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <h6 class="text-muted font-semibold">Total revenue (period)</h6>
                    <h4 class="font-extrabold mb-0 text-primary">${{ number_format($totalRevenue, 2) }}</h4>
                    <p class="small text-muted mb-0 mt-1">Online succeeded + manual verified</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <h6 class="text-muted font-semibold">Online registration</h6>
                    <h4 class="font-extrabold mb-0">${{ number_format($onlineRevenue, 2) }}</h4>
                    <p class="small text-muted mb-0 mt-1">{{ $onlineSucceededCount }} succeeded</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <h6 class="text-muted font-semibold">Manual service payments</h6>
                    <h4 class="font-extrabold mb-0">${{ number_format($manualRevenue, 2) }}</h4>
                    <p class="small text-muted mb-0 mt-1">{{ $manualVerifiedCount }} verified @if($manualDiscrepancyCount > 0) · <span class="text-warning">{{ $manualDiscrepancyCount }} discrepancy</span>@endif</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <h6 class="text-muted font-semibold">New service requests</h6>
                    <h4 class="font-extrabold mb-0">{{ $newRequestsInPeriod }}</h4>
                    <p class="small text-muted mb-0 mt-1">Created in this period</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="services">
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Service request pipeline</h4>
                        <p class="text-muted small mb-0">All requests (current status counts)</p>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr><th>Status</th><th class="text-end">Count</th></tr>
                                </thead>
                                <tbody>
                                    @php $pipeline = ['pending','verified','discrepancy','rejected']; @endphp
                                    @foreach($pipeline as $st)
                                        <tr>
                                            <td><span class="badge bg-light-secondary text-uppercase">{{ $st }}</span></td>
                                            <td class="text-end fw-bold">{{ $requestStatusCounts[$st] ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p class="small text-muted mb-0 mt-2">New in period by status:</p>
                        <ul class="small mb-0">
                            @foreach($pipeline as $st)
                                <li>{{ ucfirst($st) }}: {{ $newRequestsByStatus[$st] ?? 0 }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Payment status (period)</h4>
                    </div>
                    <div class="card-body">
                        <h6 class="text-muted">Online registration</h6>
                        <ul class="small">
                            @foreach(['initiated','succeeded','failed'] as $st)
                                @php $row = $onlineByStatus[$st] ?? null; @endphp
                                <li>{{ ucfirst($st) }}: {{ $row->cnt ?? 0 }} · ${{ number_format($row->total ?? 0, 2) }}</li>
                            @endforeach
                        </ul>
                        <h6 class="text-muted mt-3">Manual service payments</h6>
                        <ul class="small mb-0">
                            @foreach(['verified','discrepancy','rejected'] as $st)
                                @php $row = $pvByStatus[$st] ?? null; @endphp
                                <li>{{ ucfirst($st) }}: {{ $row->cnt ?? 0 }} · ${{ number_format($row->total ?? 0, 2) }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="payments">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Revenue by service</h4>
                <p class="text-muted small mb-0">Online payments (by registration date) and manual verified payments (by payment date) in range, grouped by service.</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th class="text-end">Online</th>
                                <th class="text-end">#</th>
                                <th class="text-end">Manual verified</th>
                                <th class="text-end">#</th>
                                <th class="text-end">Combined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($byService as $row)
                                <tr>
                                    <td class="fw-bold">{{ $row['name'] }}</td>
                                    <td class="text-end">${{ number_format($row['online_total'], 2) }}</td>
                                    <td class="text-end text-muted">{{ $row['online_cnt'] }}</td>
                                    <td class="text-end">${{ number_format($row['manual_total'], 2) }}</td>
                                    <td class="text-end text-muted">{{ $row['manual_cnt'] }}</td>
                                    <td class="text-end fw-bold">${{ number_format($row['combined'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No payment activity in this period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
