@extends('layouts.mazer')

@section('title', 'Apartment Ownership Transfers')
@section('page-heading', 'Apartment Ownership Transfers')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Ownership Transfer List</h4>
                <a href="{{ route('admin.apartment-transfers.create') }}"
                   class="btn btn-primary float-end">
                    New Transfer
                </a>
            </div>

            <div class="card-body">

                {{-- Filters --}}
                <form method="GET" action="{{ route('admin.apartment-transfers.index') }}" class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" placeholder="Ref / Apt / Owner ID" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">Any Status</option>
                                <option value="Pending" @selected(request('status')==='Pending')>Pending</option>
                                <option value="Approved" @selected(request('status')==='Approved')>Approved</option>
                                <option value="Rejected" @selected(request('status')==='Rejected')>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="from" class="form-control" value="{{ request('from') }}" placeholder="From">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="to" class="form-control" value="{{ request('to') }}" placeholder="To">
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-outline-secondary" type="submit">Apply Filters</button>
                            <a href="{{ route('admin.apartment-transfers.index') }}" class="btn btn-link">Reset</a>
                        </div>
                    </div>
                </form>

                {{-- Success message --}}
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                {{-- Table --}}
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Reference No</th>
                                <th>Apartment / Unit</th>
                                <th>Previous Owner</th>
                                <th>New Owner</th>
                                <th>Reason</th>
                                <th>Transfer Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($transfers as $transfer)
                                <tr>
                                    <td>{{ $transfer->transfer_reference_number }}</td>
                                    <td>
                                        Apt {{ $transfer->apartment_number }} <br>
                                        Unit {{ $transfer->unit_number }}
                                    </td>
                                    <td>
                                        {{ $transfer->previous_owner_name }} <br>
                                        <small class="text-muted">{{ $transfer->previous_owner_id }}</small>
                                    </td>
                                    <td>
                                        {{ $transfer->new_owner_name }} <br>
                                        <small class="text-muted">{{ $transfer->new_owner_id }}</small>
                                    </td>
                                    <td>{{ $transfer->transfer_reason }}</td>
                                    <td>{{ $transfer->transfer_date }}</td>
                                    <td>
                                        <span class="badge
                                            @if($transfer->approval_status === 'Approved') bg-success
                                            @elseif($transfer->approval_status === 'Rejected') bg-danger
                                            @else bg-warning
                                            @endif
                                        ">
                                            {{ $transfer->approval_status }}
                                        </span>
                                    </td>
                                    <td class="d-flex gap-1">
                                        <a href="{{ route('admin.apartment-transfers.deed', $transfer) }}" class="btn btn-sm btn-outline-primary">Deed</a>
                                        @if($transfer->approval_status === 'Pending')
                                            <form action="{{ route('admin.apartment-transfers.approve', $transfer) }}" method="POST" onsubmit="return confirm('Approve this transfer?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                            </form>
                                            <form action="{{ route('admin.apartment-transfers.reject', $transfer) }}" method="POST" onsubmit="return confirm('Reject this transfer?');">
                                                @csrf
                                                <input type="hidden" name="approval_reason" value="Insufficient documentation">
                                                <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">
                                        No ownership transfers found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                {{ $transfers->links() }}

            </div>
        </div>
    </div>
</div>
@endsection
