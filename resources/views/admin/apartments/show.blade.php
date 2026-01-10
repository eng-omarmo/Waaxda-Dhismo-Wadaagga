@extends('layouts.mazer')

@section('title', 'Apartment Details')
@section('page-heading', 'Apartment: ' . $apartment->name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Apartment Details</h4>
                <a href="{{ route('admin.apartments.index') }}" class="btn btn-secondary float-end">Back to List</a>
            </div>
            <div class="card-body">
                <h5>{{ $apartment->name }}</h5>
                <p><strong>Address:</strong> {{ $apartment->address_street }}, {{ $apartment->address_city }}, {{ $apartment->address_state }} {{ $apartment->address_zip }}</p>
                <p><strong>Contact:</strong> {{ $apartment->contact_name }} ({{ $apartment->contact_phone }}, {{ $apartment->contact_email }})</p>
                <p><strong>Notes:</strong> {{ $apartment->notes ?? 'N/A' }}</p>

                <hr>

                <h5>Units</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Unit Number</th>
                                <th>Type</th>
                                <th>Sq. Footage</th>
                                <th>Monthly Rent</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($apartment->units as $unit)
                                <tr>
                                    <td>{{ $unit->unit_number }}</td>
                                    <td>{{ $unit->unit_type }}</td>
                                    <td>{{ $unit->square_footage }}</td>
                                    <td>${{ number_format($unit->monthly_rent, 2) }}</td>
                                    <td><span class="badge bg-{{ $unit->status == 'available' ? 'success' : ($unit->status == 'occupied' ? 'warning' : 'danger') }}">{{ ucfirst(str_replace('-', ' ', $unit->status)) }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No units found for this apartment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
