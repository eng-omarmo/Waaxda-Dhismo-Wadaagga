@extends('layouts.mazer')

@section('title', 'Permit Details')
@section('page-heading', 'Permit for ' . $permit->applicant_name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Permit Details</h4>
                <a href="{{ route('admin.permits.index') }}" class="btn btn-secondary float-end">Back to List</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Applicant Information</h5>
                        <p><strong>Applicant:</strong> {{ $permit->applicant_name }}</p>
                        <p><strong>ID/Reg No.:</strong> {{ $permit->national_id_or_company_registration }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Land Information</h5>
                        <p><strong>Plot Number:</strong> {{ $permit->land_plot_number }}</p>
                        <p><strong>Location:</strong> {{ $permit->location }}</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Apartment Details</h5>
                        <p><strong>Floors:</strong> {{ $permit->number_of_floors }}</p>
                        <p><strong>Units:</strong> {{ $permit->number_of_units }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>Technical Approval</h5>
                        <p><strong>Engineer/Architect:</strong> {{ $permit->engineer_or_architect_name }}</p>
                        <p><strong>License:</strong> {{ $permit->engineer_or_architect_license ?? 'N/A' }}</p>
                        <p><strong>Approved Drawings:</strong>
                            @if($permit->approved_drawings_path)
                            <a href="{{ route('admin.permits.download', $permit) }}" class="btn btn-sm btn-info">Download</a>
                            @else
                            Not Uploaded
                            @endif
                        </p>
                    </div>
                </div>
                <hr>
                <h5>Permit Status</h5>
                <p><strong>Status:</strong> <span class="badge bg-{{ $permit->permit_status == 'Approved' ? 'success' : ($permit->permit_status == 'Pending' ? 'warning' : 'danger') }}">{{ $permit->permit_status }}</span></p>
                <p><strong>Issue Date:</strong> {{ $permit->permit_issue_date ? $permit->permit_issue_date->format('Y-m-d') : 'N/A' }}</p>
                <p><strong>Expiry Date:</strong> {{ $permit->permit_expiry_date ? $permit->permit_expiry_date->format('Y-m-d') : 'N/A' }}</p>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <form method="POST" action="{{ route('admin.permits.approve', $permit) }}">
                            @csrf
                            <div class="mb-3">
                                <label for="approval_notes" class="form-label">Approval Notes</label>
                                <textarea id="approval_notes" name="approval_notes" class="form-control" rows="3">{{ old('approval_notes', $permit->approval_notes ?? '') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Approve Permit</button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form method="POST" action="{{ route('admin.permits.reject', $permit) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Rejection Reason</label>
                                <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger">Reject Permit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
@endpush
