@extends('layouts.mazer')
@section('title','Contacts')
@section('page-heading','Landing Page Contacts')
@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form class="row g-3" method="GET" action="{{ route('admin.contacts.index') }}">
            <div class="col-md-6">
                <label class="form-label">Search</label>
                <input name="q" class="form-control" value="{{ request('q') }}" placeholder="Name, email, phone, service type">
            </div>
            <div class="col-md-2">
                <label class="form-label">Per page</label>
                <select name="per_page" class="form-select">
                    @foreach ([10,25,50,100] as $n)
                    <option value="{{ $n }}" @selected(request('per_page',10)==$n)>{{ $n }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-filter me-1"></i> Apply</button>
                <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
    </div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Service Type</th>
                        <th>Message</th>
                        <th>Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($messages as $m)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $m->full_name }}</div>
                        </td>
                        <td>
                            <div class="small">{{ $m->email }}</div>
                            <div class="small">{{ $m->phone }}</div>
                        </td>
                        <td>{{ $m->service_type ?? '—' }}</td>
                        <td>
                            <div class="text-truncate" style="max-width: 400px">{{ $m->message }}</div>
                        </td>
                        <td>{{ $m->created_at?->toDateTimeString() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No contacts found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            <x-pagination :paginator="$messages" />
        </div>
    </div>
</div>
@endsection

