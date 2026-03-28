@extends('layouts.mazer')
@section('title', 'Payment Records')

@section('content')
<div class="page-heading">
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Payment Records</h3>
                <p class="text-subtitle text-muted">Includes online registration payments and verified manual service payments (two separate ledgers).</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Payments</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <section class="row">
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-4 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon purple mb-2">
                                <i class="bi bi-wallet2"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-8 col-xxl-7">
                            <h6 class="text-muted font-semibold">Total Collected</h6>
                            <h6 class="font-extrabold mb-0">${{ number_format($totalCollected, 2) }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-4 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon blue mb-2">
                                <i class="bi bi-calculator"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-8 col-xxl-7">
                            <h6 class="text-muted font-semibold">Avg. Payment</h6>
                            <h6 class="font-extrabold mb-0">${{ number_format($avgPayment, 2) }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-4 col-xxl-5 d-flex justify-content-start">
                            <div class="stats-icon green mb-2">
                                <i class="bi bi-receipt-cutoff"></i>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-8 col-xxl-7">
                            <h6 class="text-muted font-semibold">Services (verified)</h6>
                            <h6 class="font-extrabold mb-0">${{ number_format($servicePaymentsVerifiedTotal, 2) }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3 col-md-12">
            <div class="card">
                <div class="card-body px-4 py-4-5">
                    <h6 class="text-muted font-semibold mb-3">Method Breakdown (Successful)</h6>
                    <div class="d-flex flex-wrap gap-3">
                        @forelse($methodBreakdown as $method)
                        <div class="d-flex align-items-center">
                            <span class="badge bg-light-primary me-2">{{ $method->payment_method ?: 'Unknown' }}</span>
                            <span class="fw-bold">{{ $method->count }}</span>
                        </div>
                        @empty
                        <span class="text-muted">No successful payments recorded.</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Filters & List -->
    <section class="section">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h4 class="card-title mb-0">Online registration payments</h4>
                        <p class="text-muted small mb-0">Self-registration / portal card payments tied to a pending registration.</p>
                    </div>
                    <div class="btn-group">
                        <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-file-earmark-spreadsheet"></i> CSV
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-file-earmark-pdf"></i> PDF
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Search and Filters Form -->
                <form method="GET" action="{{ route('admin.payments.index') }}" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" name="q" class="form-control" placeholder="Customer, email, or reference..." value="{{ request('q') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">— All Statuses —</option>
                                @foreach($statuses as $s)
                                <option value="{{ $s }}" @selected(request('status') == $s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="method" class="form-select">
                                <option value="">— All Methods —</option>
                                @foreach($methods as $m)
                                <option value="{{ $m }}" @selected(request('method') == $m)>{{ ucfirst($m) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="from" class="form-control" placeholder="From Date" value="{{ request('from') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="to" class="form-control" placeholder="To Date" value="{{ request('to') }}">
                        </div>
                        <div class="col-12 text-end">
                            <a href="{{ route('admin.payments.index') }}" class="btn btn-light-secondary me-1">Reset</a>
                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th><a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'direction' => $direction === 'asc' ? 'desc' : 'asc']) }}">ID <i class="bi bi-arrow-down-up small"></i></a></th>
                                <th>Customer Details</th>
                                <th><a href="{{ request()->fullUrlWithQuery(['sort' => 'amount', 'direction' => $direction === 'asc' ? 'desc' : 'asc']) }}">Amount <i class="bi bi-arrow-down-up small"></i></a></th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Transaction / Reference</th>
                                <th><a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => $direction === 'asc' ? 'desc' : 'asc']) }}">Date <i class="bi bi-arrow-down-up small"></i></a></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $p)
                            <tr>
                                <td>{{ $p->id }}</td>
                                <td>
                                    <div class="fw-bold">{{ $p->registration?->full_name ?: 'N/A' }}</div>
                                    <div class="small text-muted">{{ $p->registration?->email ?: '—' }}</div>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">{{ number_format($p->amount, 2) }} {{ $p->currency }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-light-secondary text-uppercase">{{ $p->payment_method ?: 'Other' }}</span>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($p->status) {
                                            'succeeded' => 'bg-success',
                                            'failed' => 'bg-danger',
                                            'initiated' => 'bg-warning',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ ucfirst($p->status) }}</span>
                                </td>
                                <td>
                                    <div class="small"><span class="text-muted">TX:</span> {{ $p->transaction_id ?: '—' }}</div>
                                    <div class="small"><span class="text-muted">REF:</span> {{ $p->reference ?: '—' }}</div>
                                </td>
                                <td>{{ $p->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No payment records found matching your criteria.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $payments->links('components.pagination') }}
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Manual service payments</h4>
                <p class="text-muted small mb-0 mt-1">Recorded when you verify payment on a <a href="{{ route('admin.manual-requests.index') }}">manual service request</a>. These are stored separately from online registration payments above.</p>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-3">The search and date filters at the top apply to both tables (service rows match customer name, email, or phone; dates use payment date).</p>
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Service &amp; customer</th>
                                <th>Amount</th>
                                <th>Payment date</th>
                                <th>Status</th>
                                <th>Recorded</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($servicePayments as $sp)
                                @php
                                    $sr = $sp->request;
                                    $badgeClass = match($sp->status) {
                                        'verified' => 'bg-success',
                                        'discrepancy' => 'bg-warning',
                                        'rejected' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <tr>
                                    <td>{{ $sp->id }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $sr?->service?->name ?? '—' }}</div>
                                        <div class="small text-muted">{{ $sr?->user_full_name }} · {{ $sr?->user_email }}</div>
                                        <div class="small"><span class="text-muted">Request #{{ $sr?->id }}</span></div>
                                    </td>
                                    <td>${{ number_format($sp->amount, 2) }}</td>
                                    <td>{{ $sp->payment_date?->format('Y-m-d') }}</td>
                                    <td><span class="badge {{ $badgeClass }}">{{ ucfirst($sp->status) }}</span></td>
                                    <td>{{ $sp->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="text-nowrap">
                                        @if($sr && $sp->status === 'verified')
                                            <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.manual-requests.receipt', [$sr, $sp]) }}" target="_blank">Receipt</a>
                                        @endif
                                        @if($sr)
                                            <a class="btn btn-outline-primary btn-sm" href="{{ route('admin.manual-requests.show', $sr) }}">Open request</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No service payments yet. Verify a payment on a manual request to see it here.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 d-flex justify-content-center">
                    {{ $servicePayments->links('components.pagination') }}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
