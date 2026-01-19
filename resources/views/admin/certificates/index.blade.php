@extends('layouts.mazer')
@section('title','Certificates')
@section('page-heading','Certificates of Clearance')
@section('content')
<div class="card">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div class="text-muted small">Track generated certificates with unique identifiers.</div>
      <div class="d-flex gap-2">
        <a href="{{ route('admin.certificates.create') }}" class="btn btn-primary btn-sm">Generate Certificate</a>
        <a href="{{ route('admin.certificates.index') }}" class="btn btn-outline-secondary btn-sm">Refresh</a>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Certificate #</th>
            <th>Issued</th>
            <th>Service</th>
            <th>Payment Ref</th>
            <th>Verification</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse($certs as $c)
            <tr>
              <td class="text-muted">{{ $c->certificate_number }}</td>
              <td>{{ $c->issued_at->format('Y-m-d H:i') }}</td>
              <td>{{ optional($c->service)->name ?? 'Service' }}</td>
              <td class="small">{{ data_get($c->metadata,'payment_reference','—') }}</td>
              <td class="small text-break">{{ Str::limit($c->certificate_hash, 16) }}</td>
              <td class="text-end">
                <a href="{{ route('admin.certificates.show', $c) }}" class="btn btn-sm btn-outline-primary">View</a>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center">No certificates found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <x-pagination :paginator="$certs" />
  </div>
@endsection
