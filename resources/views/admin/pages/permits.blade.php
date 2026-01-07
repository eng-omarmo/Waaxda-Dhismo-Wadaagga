@extends('layouts.mazer')
@section('title','Permits')
@section('page-heading','Construction Permits')
@section('content')
<div class="card">
  <div class="card-body">
    <h5 class="mb-3">Permit Requests – Pending / Approved / Rejected</h5>
    <p class="text-muted">Review and manage permit requests.</p>
    <hr>
    <h5 id="approve" class="mb-3">Approve Permits</h5>
    <p class="text-muted">Approval actions placeholder.</p>
    <hr>
    <h5 id="history" class="mb-3">Permit History</h5>
    <p class="text-muted">Historical records of permits.</p>
  </div>
</div>
@endsection
