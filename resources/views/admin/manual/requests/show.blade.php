@extends('layouts.mazer')
@section('title','Manual Request')
@section('page-heading','Manual Request Details')
@section('content')
<div class="row">
  <div class="col-md-7">
    <div class="card mb-3">
      <div class="card-header">
        <h5 class="mb-0">{{ $request->service->name }}</h5>
      </div>
      <div class="card-body">
        <div class="mb-2">User: {{ $request->user_full_name }}</div>
        <div class="mb-2">Email: {{ $request->user_email }}</div>
        <div class="mb-2">Phone: {{ $request->user_phone }}</div>
        <div class="mb-2">Status: <span class="badge bg-{{ $request->status==='verified' ? 'success' : ($request->status==='discrepancy' ? 'warning' : ($request->status==='rejected' ? 'danger' : 'secondary')) }}">{{ ucfirst($request->status) }}</span></div>
        <div class="mb-2">Price: ${{ number_format($request->service->price,2) }}</div>
        <div class="mb-2">Created: {{ $request->created_at->format('Y-m-d H:i') }}</div>
        @if($request->processed_at)
        <div class="mb-2">Processed: {{ $request->processed_at->format('Y-m-d H:i') }}</div>
        @endif
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h6 class="mb-0">Payment Records</h6>
      </div>
      <div class="card-body">
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Amount</th>
              <th>Date</th>
              <th>Status</th>
              <th>Reference</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @forelse($request->payments as $p)
              <tr>
                <td>{{ $p->id }}</td>
                <td>${{ number_format($p->amount,2) }}</td>
                <td>{{ \Carbon\Carbon::parse($p->payment_date)->format('Y-m-d') }}</td>
                <td><span class="badge bg-{{ $p->status==='verified' ? 'success' : ($p->status==='discrepancy' ? 'warning' : 'danger') }}">{{ ucfirst($p->status) }}</span></td>
                <td>****</td>
                <td>
                  @if($p->status==='verified')
                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.manual-requests.receipt', [$request, $p]) }}" target="_blank">Receipt</a>
                  @endif
                  @if($p->status==='discrepancy')
                  <form method="post" action="{{ route('admin.manual-requests.reconcile', [$request, $p]) }}" class="d-flex gap-2">
                    @csrf
                    <input type="number" step="0.01" name="reconciled_amount" class="form-control" placeholder="Reconciled amount" required>
                    <input type="text" name="reconciliation_notes" class="form-control" placeholder="Notes">
                    <button class="btn btn-outline-primary btn-sm">Reconcile</button>
                  </form>
                  @endif
                </td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center">No payments recorded.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-md-5">
    <div class="card mb-3">
      <div class="card-header">
        <h6 class="mb-0">Verify Payment</h6>
      </div>
      <div class="card-body">
        <form method="post" action="{{ route('admin.manual-requests.verify', $request) }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Payment Date</label>
            <input type="date" name="payment_date" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Reference Number</label>
            <input type="text" name="reference_number" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Notes</label>
            <input type="text" name="notes" class="form-control">
          </div>
          <button class="btn btn-primary">Verify</button>
          <button type="button" class="btn btn-outline-danger" data-bs-toggle="collapse" data-bs-target="#rejectForm">Reject</button>
        </form>

        <div id="rejectForm" class="collapse mt-3">
          <form method="post" action="{{ route('admin.manual-requests.reject', $request) }}" class="d-flex gap-2">
            @csrf
            <input type="text" name="reason" class="form-control" placeholder="Rejection reason" required>
            <button class="btn btn-danger">Confirm Reject</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
