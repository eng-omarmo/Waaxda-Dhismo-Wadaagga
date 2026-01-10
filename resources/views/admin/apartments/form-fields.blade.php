@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="name" class="form-label">Apartment Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $apartment->name ?? '') }}" required>
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="address_city" class="form-label">Location </label>
            <input type="text" class="form-control" id="address_street" name="address_city" value="{{ old('address_city', $apartment->address_city ?? '') }}" required>
        </div>
    </div>

</div>



<hr>
<h5>Contact Person</h5>
<div class="row">
    <div class="col-md-4">
        <div class="mb-3">
            <label for="contact_name" class="form-label">Name</label>
            <input type="text" class="form-control" id="contact_name" name="contact_name" value="{{ old('contact_name', $apartment->contact_name ?? '') }}" required>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label for="contact_phone" class="form-label">Phone</label>
            <input type="text" class="form-control" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $apartment->contact_phone ?? '') }}" required>
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label for="contact_email" class="form-label">Email</label>
            <input type="email" class="form-control" id="contact_email" name="contact_email" value="{{ old('contact_email', $apartment->contact_email ?? '') }}" required>
        </div>
    </div>
</div>

<div class="mb-3">
    <label for="notes" class="form-label">Notes</label>
    <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $apartment->notes ?? '') }}</textarea>
</div>

<hr>
<h5>Units</h5>
<div id="units-container">
    @if(isset($apartment) && $apartment->units->count() > 0)
    @foreach($apartment->units as $index => $unit)
    @include('admin.apartments.unit-form-fields', ['index' => $index, 'unit' => $unit])
    @endforeach
    @else
    @include('admin.apartments.unit-form-fields', ['index' => 0])
    @endif
</div>
<button type="button" id="add-unit-btn" class="btn btn-secondary">Add Another Unit</button>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let unitIndex = {
            {
                isset($apartment) ? $apartment - > units - > count() : 1
            }
        };
        document.getElementById('add-unit-btn').addEventListener('click', function() {
            const unitContainer = document.getElementById('units-container');
            const newUnitForm = `@include('admin.apartments.unit-form-fields', ['index' => 'REPLACE_INDEX'])`.replace(/REPLACE_INDEX/g, unitIndex);
            unitContainer.insertAdjacentHTML('beforeend', newUnitForm);
            unitIndex++;
        });

        document.getElementById('units-container').addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('remove-unit-btn')) {
                e.target.closest('.unit-form').remove();
            }
        });
    });
</script>
@endpush
