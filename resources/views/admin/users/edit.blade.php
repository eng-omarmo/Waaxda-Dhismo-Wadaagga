@extends('layouts.mazer')
@section('title','Edit User')
@section('page-heading','Edit User')
@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.update',$user) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">First name</label>
                    <input name="first_name" class="form-control" value="{{ old('first_name',$user->first_name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last name</label>
                    <input name="last_name" class="form-control" value="{{ old('last_name',$user->last_name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input name="email" type="email" class="form-control" value="{{ old('email',$user->email) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contact phone</label>
                    <input name="contact_phone" class="form-control" value="{{ old('contact_phone',$user->contact_phone) }}" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Contact address</label>
                    <input name="contact_address" class="form-control" value="{{ old('contact_address',$user->contact_address) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">New password</label>
                    <input name="password" type="password" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirm password</label>
                    <input name="password_confirmation" type="password" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="user" @selected(old('role',$user->role)==='user')>User</option>
                        <option value="admin" @selected(old('role',$user->role)==='admin')>Admin</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="active" class="form-select" required>
                        <option value="1" @selected(old('active',$user->active))>Active</option>
                        <option value="0" @selected(!old('active',$user->active))>Inactive</option>
                    </select>
                </div>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger mt-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="mt-3">
                <button class="btn btn-primary" type="submit">Save</button>
                <a href="{{ route('admin.users.show',$user) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
