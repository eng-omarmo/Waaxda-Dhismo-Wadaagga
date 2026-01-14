<div class="unit-form border rounded p-3 mb-3">
    @if(isset($unit))
    <input type="hidden" name="units[{{ $index }}][id]" value="{{ $unit->id }}">
    @endif
    <div class="row">
        <div class="col-md-3">
            <div class="mb-3">
                <label for="units_{{ $index }}_unit_number" class="form-label">Unit Number</label>
                <input type="text" class="form-control" id="units_{{ $index }}_unit_number" name="units[{{ $index }}][unit_number]" value="{{ old('units.'.$index.'.unit_number', $unit->unit_number ?? '') }}" required>
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label for="units_{{ $index }}_unit_type" class="form-label">Unit Type</label>
                <select class="form-select" id="units_{{ $index }}_unit_type" name="units[{{ $index }}][unit_type]" required>
                    <option value="">Select Type</option>
                    <option value="studio" {{ old('units.'.$index.'.unit_type', $unit->unit_type ?? '') == 'studio' ? 'selected' : '' }}>Studio</option>
                    <option value="1-bedroom" {{ old('units.'.$index.'.unit_type', $unit->unit_type ?? '') == '1-bedroom' ? 'selected' : '' }}>1-Bedroom</option>
                    <option value="2-bedroom" {{ old('units.'.$index.'.unit_type', $unit->unit_type ?? '') == '2-bedroom' ? 'selected' : '' }}>2-Bedroom</option>
                    <option value="3-bedroom" {{ old('units.'.$index.'.unit_type', $unit->unit_type ?? '') == '3-bedroom' ? 'selected' : '' }}>3-Bedroom</option>
                    <option value="penthouse" {{ old('units.'.$index.'.unit_type', $unit->unit_type ?? '') == 'penthouse' ? 'selected' : '' }}>Penthouse</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="mb-3">
                <label for="units_{{ $index }}_square_footage" class="form-label">Sq. Footage</label>
                <input type="number" class="form-control" id="units_{{ $index }}_square_footage" name="units[{{ $index }}][square_footage]" value="{{ old('units.'.$index.'.square_footage', $unit->square_footage ?? '') }}" required>
            </div>
        </div>
        <div class="col-md-2">
            <div class="mb-3">
                <label for="units_{{ $index }}_monthly_rent" class="form-label">Monthly Rent</label>
                <input type="number" step="0.01" class="form-control" id="units_{{ $index }}_monthly_rent" name="units[{{ $index }}][monthly_rent]" value="{{ old('units.'.$index.'.monthly_rent', $unit->monthly_rent ?? '') }}" required>
            </div>
        </div>
        <div class="col-md-2">
            <div class="mb-3">
                <label for="units_{{ $index }}_status" class="form-label">Status</label>
                <select class="form-select" id="units_{{ $index }}_status" name="units[{{ $index }}][status]" required>
                    <option value="available" {{ old('units.'.$index.'.status', $unit->status ?? '') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="occupied" {{ old('units.'.$index.'.status', $unit->status ?? '') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                    <option value="under-maintenance" {{ old('units.'.$index.'.status', $unit->status ?? '') == 'under-maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                </select>
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-danger btn-sm remove-unit-btn">Remove Unit</button>
</div>
