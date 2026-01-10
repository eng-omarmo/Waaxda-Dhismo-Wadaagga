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

                {{-- Search --}}
                <form method="GET" action="{{ route('admin.apartment-transfers.index') }}" class="mb-3">
                    <div class="input-group">
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search by transfer reference, owner name, unit..."
                            value="{{ request('search') }}"
                        >
                        <button class="btn btn-outline-secondary" type="submit">Search</button>
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
                                    <td>
                                        <a href="{{ route('admin.apartment-transfers.show', $transfer) }}"
                                           class="btn btn-sm btn-info">
                                            View
                                        </a>

                                        <a href="{{ route('admin.apartment-transfers.edit', $transfer) }}"
                                           class="btn btn-sm btn-primary">
                                            Edit
                                        </a>

                                        <form action="{{ route('admin.apartment-transfers.destroy', $transfer) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this transfer?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                Delete
                                            </button>
                                        </form>
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
