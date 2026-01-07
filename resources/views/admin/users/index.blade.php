@extends('layouts.mazer')
@section('title','User Management')
@section('page-heading','Users')
@section('content')
<div class="card">
    <div class="card-body">
        <form class="row g-3 mb-3" method="GET">
            <div class="col-md-4">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search name or email">
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select">
                    <option value="">All roles</option>
                    <option value="user" @selected(request('role')==='user')>User</option>
                    <option value="admin" @selected(request('role')==='admin')>Admin</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All status</option>
                    <option value="active" @selected(request('status')==='active')>Active</option>
                    <option value="inactive" @selected(request('status')==='inactive')>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-primary w-100" type="submit">Filter</button>
            </div>
        </form>
        <a href="{{ route('admin.users.create') }}" class="btn btn-success mb-3">Create User</a>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td>{{ $user->active ? 'Active' : 'Inactive' }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.users.show',$user) }}" class="btn btn-sm btn-primary">View</a>
                            <a href="{{ route('admin.users.edit',$user) }}" class="btn btn-sm btn-secondary">Edit</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        {{ $users->links() }}
    </div>
 </div>
@endsection
