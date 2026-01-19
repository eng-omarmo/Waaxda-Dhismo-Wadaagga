@extends('layouts.mazer')
@section('title','Manual Processing')
@section('page-heading','Manual Service Requests')
@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <form method="get" class="d-flex gap-2">
                <select name="status" class="form-select" style="max-width:200px">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $s)
                    <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                <input type="text" name="q" class="form-control" placeholder="Search..." value="{{ request('q') }}">
                <button class="btn btn-primary">Filter</button>
            </form>
            <a href="{{ route('admin.manual-requests.create') }}" class="btn btn-success">New Manual Request</a>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Service</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $r)
                <tr>
                    <td>{{ $r->id }}</td>
                    <td>{{ $r->service->name }}</td>
                    <td>{{ $r->user_full_name }}</td>
                    <td>{{ $r->user_email }}</td>
                    <td><span class="badge bg-{{ $r->status==='verified' ? 'success' : ($r->status==='discrepancy' ? 'warning' : ($r->status==='rejected' ? 'danger' : 'secondary')) }}">{{ ucfirst($r->status) }}</span></td>
                    <td>{{ $r->created_at->format('Y-m-d H:i') }}</td>
                    <td><a class="btn btn-primary btn-sm" href="{{ route('admin.manual-requests.show',$r) }}">Open</a></td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">No requests found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <x-pagination :paginator="$requests" />
    </div>
</div>
@endsection
