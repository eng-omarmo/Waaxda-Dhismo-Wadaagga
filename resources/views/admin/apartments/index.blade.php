@extends('layouts.mazer')

@section('title', 'Apartment Management')
@section('page-heading', 'Apartments')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Apartment List</h4>
                <a href="{{ route('admin.apartments.create') }}" class="btn btn-primary float-end">Add New Apartment</a>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.apartments.index') }}" class="mb-3">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search by apartment name..." value="{{ request('search') }}">
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
                                <th>Name</th>
                                <th>Address</th>
                                <th>Contact Person</th>
                                <th>Units</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($apartments as $apartment)
                                <tr>
                                    <td>{{ $apartment->name }}</td>
                                    <td>{{ $apartment->address_street }}, {{ $apartment->address_city }}</td>
                                    <td>{{ $apartment->contact_name }} ({{ $apartment->contact_email }})</td>
                                    <td>{{ $apartment->units_count }}</td>
                                    <td>
                                        <a href="{{ route('admin.apartments.show', $apartment) }}" class="btn btn-sm btn-info">View</a>
                                        <a href="{{ route('admin.apartments.edit', $apartment) }}" class="btn btn-sm btn-primary">Edit</a>
                                        <form action="{{ route('admin.apartments.destroy', $apartment) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this apartment?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No apartments found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $apartments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
