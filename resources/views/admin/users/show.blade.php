@extends('layouts.mazer')
@section('title','User Detail')
@section('page-heading','User Detail')
@section('content')
<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <h5>{{ $user->first_name }} {{ $user->last_name }}</h5>
                <p class="mb-1">{{ $user->email }}</p>
                <span class="badge bg-primary me-2">{{ ucfirst($user->role) }}</span>
                <span class="badge {{ $user->active ? 'bg-success':'bg-secondary' }}">{{ $user->active ? 'Active' : 'Inactive' }}</span>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.users.edit',$user) }}" class="btn btn-secondary">Edit</a>
                <form method="POST" action="{{ route('admin.users.destroy',$user) }}" class="d-inline" onsubmit="return confirm('Deactivate this user?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger" type="submit">Deactivate</button>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6>Contact</h6>
                        <p class="mb-1">Phone: {{ $user->contact_phone }}</p>
                        <p class="mb-0">Address: {{ $user->contact_address }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
