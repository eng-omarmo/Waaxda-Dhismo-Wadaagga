@extends('layouts.mazer')
@section('title','Create User')
@section('page-heading','Create User')
@section('content')
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">First name</label>
                    <input name="first_name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last name</label>
                    <input name="last_name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input name="email" type="email" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contact phone</label>
                    <input name="contact_phone" class="form-control" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Contact address</label>
                    <input name="contact_address" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input name="password" type="password" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirm password</label>
                    <input name="password_confirmation" type="password" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="active" class="form-select" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
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
                <button class="btn btn-primary" type="submit">Create</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
