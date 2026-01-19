@extends('layouts.mazer')

@section('title', 'Construction Permits')
@section('page-heading', 'Construction Permits')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Permit List</h4>
                <a href="{{ route('admin.permits.create') }}" class="btn btn-primary float-end">Request New Permit</a>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.permits.index') }}" class="mb-3">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search by applicant or plot number..." value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">Search</button>
                    </div>
                </form>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Applicant</th>
                                <th>Plot Number</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Issue Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($permits as $permit)
                                <tr>
                                    <td>{{ $permit->applicant_name }}</td>
                                    <td>{{ $permit->land_plot_number }}</td>
                                    <td>{{ $permit->location }}</td>
                                    <td>
                                        <span class="badge bg-{{ $permit->permit_status == 'Approved' ? 'success' : ($permit->permit_status == 'Pending' ? 'warning' : 'danger') }}">
                                            {{ $permit->permit_status }}
                                        </span>
                                    </td>
                                    <td>{{ $permit->permit_issue_date ? $permit->permit_issue_date->format('Y-m-d') : 'N/A' }}</td>
                                    <td>
                                        <a href="{{ route('admin.permits.show', $permit) }}" class="btn btn-sm btn-info">View</a>
                                        <a href="{{ route('admin.permits.edit', $permit) }}" class="btn btn-sm btn-primary">Edit</a>
                                        <form action="{{ route('admin.permits.destroy', $permit) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this permit?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No construction permits found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <x-pagination :paginator="$permits" />
            </div>
        </div>
    </div>
</div>
@endsection
