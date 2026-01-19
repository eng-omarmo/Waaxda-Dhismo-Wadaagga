@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- Transfer Reference --}}
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">
                Reference Number (auto-generated)
            </label>
            <input type="text"
                   class="form-control"
                   id="transfer_reference_number_preview"
                   value="Will be generated on save"
                   disabled>
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="transfer_date" class="form-label">Transfer Date</label>
            <input type="date"
                   class="form-control"
                   id="transfer_date"
                   name="transfer_date"
                   value="{{ old('transfer_date', $transfer->transfer_date ?? '') }}"
                   required>
        </div>
    </div>
</div>

<hr>

{{-- Apartment & Unit --}}
<h5>Apartment & Unit</h5>
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="apartment_id" class="form-label">
                Apartment
            </label>
            <select class="form-select" id="apartment_id" name="apartment_id" required aria-required="true">
                <option value="">Select apartment</option>
                @foreach(($apartments ?? []) as $apt)
                    <option value="{{ $apt->id }}" @selected(old('apartment_id', $transfer->apartment_number ?? '') == $apt->id)>{{ $apt->name }} — {{ $apt->address_city }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="unit_number" class="form-label">
                Unit Number (optional)
            </label>
            <input type="text"
                   class="form-control"
                   id="unit_number"
                   name="unit_number"
                   value="{{ old('unit_number', $transfer->unit_number ?? '') }}">
        </div>
    </div>
</div>

<hr>

{{-- Previous Owner --}}
<h5>Previous Owner</h5>
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="previous_owner_name" class="form-label">Name</label>
            <input type="text"
                   class="form-control"
                   id="previous_owner_name"
                   name="previous_owner_name"
                   value="{{ old('previous_owner_name', $transfer->previous_owner_name ?? '') }}"
                   required>
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="previous_owner_id" class="form-label">National ID</label>
            <input type="text"
                   class="form-control"
                   id="previous_owner_id"
                   name="previous_owner_id"
                   value="{{ old('previous_owner_id', $transfer->previous_owner_id ?? '') }}"
                   required>
        </div>
    </div>
</div>

<hr>

{{-- New Owner --}}
<h5>New Owner</h5>
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="new_owner_name" class="form-label">Name</label>
            <input type="text"
                   class="form-control"
                   id="new_owner_name"
                   name="new_owner_name"
                   value="{{ old('new_owner_name', $transfer->new_owner_name ?? '') }}"
                   required>
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="new_owner_id" class="form-label">National ID</label>
            <input type="text"
                   class="form-control"
                   id="new_owner_id"
                   name="new_owner_id"
                   value="{{ old('new_owner_id', $transfer->new_owner_id ?? '') }}"
                   required>
        </div>
    </div>
</div>

<hr>

{{-- Transfer Details --}}
<h5>Transfer Details</h5>
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="transfer_reason" class="form-label">
                Reason for Transfer
            </label>
            <select class="form-select"
                    id="transfer_reason"
                    name="transfer_reason"
                    required>
                <option value="">Select reason</option>
                <option value="Sale" {{ old('transfer_reason', $transfer->transfer_reason ?? '') == 'Sale' ? 'selected' : '' }}>Sale</option>
                <option value="Inheritance" {{ old('transfer_reason', $transfer->transfer_reason ?? '') == 'Inheritance' ? 'selected' : '' }}>Inheritance</option>
                <option value="Gift" {{ old('transfer_reason', $transfer->transfer_reason ?? '') == 'Gift' ? 'selected' : '' }}>Gift</option>
            </select>
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="supporting_documents" class="form-label">
                Supporting Documents
            </label>
            <input type="file"
                   class="form-control"
                   id="supporting_documents"
                   name="supporting_documents">
        </div>
    </div>
</div>

<hr>

{{-- Approval Status (Admin / Officer) --}}
@if(isset($isAdmin) && $isAdmin)
<h5>Approval</h5>
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="approval_status" class="form-label">
                Approval Status
            </label>
            <select class="form-select"
                    id="approval_status"
                    name="approval_status">
                <option value="Pending" {{ old('approval_status', $transfer->approval_status ?? '') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Approved" {{ old('approval_status', $transfer->approval_status ?? '') == 'Approved' ? 'selected' : '' }}>Approved</option>
                <option value="Rejected" {{ old('approval_status', $transfer->approval_status ?? '') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
    </div>
</div>
@endif
