@extends('layouts.mazer')

@section('title', 'Create Apartment Transfer')
@section('page-heading', 'Create New Apartment Transfer')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Apartment Transfer Information</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.apartment-transfers.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @include('admin.apartments.Transfer.form')
                    <button type="submit" class="btn btn-primary">Create Apartment Transfer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  var aptSelect = document.getElementById('apartment_id');
  var prevName = document.getElementById('previous_owner_name');
  var prevId = document.getElementById('previous_owner_id');
  var prevNameSearch = document.getElementById('previous_owner_name');
  var prevIdInput = document.getElementById('previous_owner_id');
  function fetchOwner(apartmentId) {
    if (!apartmentId) return;
    fetch('{{ url('/admin/transfers/owner') }}/' + apartmentId, { headers: { 'Accept': 'application/json' } })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data && prevName && prevId) {
          prevName.value = data.owner_name || '';
          prevId.value = data.owner_id || '';
        }
      })
      .catch(function() {});
  }
  function lookupPrevOwnerById(nid) {
    if (!nid) return;
    fetch('{{ url('/admin/transfers/owners/lookup') }}?national_id=' + encodeURIComponent(nid), { headers: { 'Accept': 'application/json' } })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        var list = (data && data.data) ? data.data : [];
        if (list.length > 0) {
          prevName.value = list[0].full_name || prevName.value;
        }
      })
      .catch(function() {});
  }
  function lookupPrevOwnerByQuery(q) {
    if (!q) return;
    fetch('{{ url('/admin/transfers/owners/lookup') }}?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' } })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        var list = (data && data.data) ? data.data : [];
        if (list.length > 0) {
          // Use first match to assist filling; admins can edit as needed
          prevName.value = list[0].full_name || prevName.value;
          prevId.value = prevId.value || list[0].national_id || '';
        }
      })
      .catch(function() {});
  }
  if (aptSelect) {
    aptSelect.addEventListener('change', function() { fetchOwner(this.value); });
    if (aptSelect.value) { fetchOwner(aptSelect.value); }
  }
  if (prevIdInput) {
    prevIdInput.addEventListener('blur', function() { lookupPrevOwnerById(this.value); });
  }
  if (prevNameSearch) {
    prevNameSearch.addEventListener('blur', function() { lookupPrevOwnerByQuery(this.value); });
  }
});
</script>
@endpush
