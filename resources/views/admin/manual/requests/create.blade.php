@extends('layouts.mazer')
@section('title','New Manual Request')
@section('page-heading','Create Manual Service Request')
@section('content')
<div class="card">
  <div class="card-body">
    <form method="post" action="{{ route('admin.manual-requests.store') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label">Service</label>
        <select name="service_id" class="form-select" required>
          @foreach($services as $s)
            <option value="{{ $s->id }}">{{ $s->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">User Full Name</label>
        <input type="text" name="user_full_name" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">User Email</label>
        <input type="email" name="user_email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">User Phone</label>
        <input type="text" name="user_phone" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">National ID</label>
        <input type="text" name="user_national_id" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Request Details</label>
        <textarea name="request_details[notes]" class="form-control" rows="3"></textarea>
      </div>
      <button class="btn btn-primary">Create</button>
      <a href="{{ route('admin.manual-requests.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
  </div>
</div>
@endsection
