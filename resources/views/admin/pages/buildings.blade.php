@extends('layouts.mazer')
@section('title','Buildings & Apartments')
@section('page-heading','Buildings & Apartments')
@section('content')
<div class="card">
  <div class="card-body">
    <h5 class="mb-3">Buildings – Add / Edit</h5>
    <p class="text-muted">Manage building records.</p>
    <div class="d-flex gap-2 mb-3">
      <a href="{{ route('admin.apartments.index') }}" class="btn btn-primary">View Apartments</a>
      <a href="{{ route('admin.apartments.create') }}" class="btn btn-outline-primary">Add Apartment</a>
    </div>
    <hr>
    <h5 id="units" class="mb-3">Units – Assign / Transfer / Status</h5>
    <p class="text-muted">Manage units within buildings.</p>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.apartment-transfers.index') }}" class="btn btn-secondary">Manage Transfers</a>
      <a href="{{ route('admin.apartment-transfers.create') }}" class="btn btn-outline-secondary">Start Transfer</a>
    </div>
  </div>
</div>
@endsection
