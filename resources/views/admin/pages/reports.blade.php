@extends('layouts.mazer')
@section('title','Reports & Analytics')
@section('page-heading','Reports & Analytics')
@section('content')
<div class="card">
  <div class="card-body">
    <h5 id="projects" class="mb-3">Project Reports</h5>
    <p class="text-muted">Project reporting overview.</p>
    <hr>
    <h5 id="permits" class="mb-3">Permit & License Statistics</h5>
    <p class="text-muted">Numbers and charts coming soon.</p>
    <hr>
    <h5 id="ownership" class="mb-3">Ownership & Transfer Trends</h5>
    <p class="text-muted">Trend analysis placeholder.</p>
    <hr>
<h5 id="payments" class="mb-3">Online Payments</h5>
    <form method="get" class="row g-2 align-items-end mb-3">
      <div class="col-md-3">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          @php $statusSel = request('status',''); @endphp
          <option value="" {{ $statusSel==='' ? 'selected' : '' }}>All</option>
          <option value="initiated" {{ $statusSel==='initiated' ? 'selected' : '' }}>Initiated</option>
          <option value="completed" {{ $statusSel==='completed' ? 'selected' : '' }}>Completed</option>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">Limit</label>
        <select name="limit" class="form-select">
          @php $limitSel = (int) request('limit', 100); @endphp
          @foreach([50,100,200,500] as $opt)
            <option value="{{ $opt }}" {{ $limitSel===$opt ? 'selected' : '' }}>{{ $opt }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <button class="btn btn-outline-primary">Apply</button>
      </div>
    </form>
    @php
      $status = request('status','');
      $limit = (int) request('limit', 100);
      if ($limit < 1) { $limit = 100; }
      if ($limit > 500) { $limit = 500; }
      $query = \App\Models\OnlinePayment::with(['registration', 'registration.service'])->latest();
      if (in_array($status, ['initiated','completed'], true)) {
        $query->where('status', $status);
      }
      $payments = $query->limit($limit)->get();
    @endphp
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Transaction ID</th>
            <th>Amount</th>
            <th>Date/Time</th>
            <th>Service</th>
            <th>Customer</th>
            <th>Status</th>
            <th>Receipt</th>
          </tr>
        </thead>
        <tbody>
          @forelse($payments as $payment)
            @php
              $reg = $payment->registration;
              $service = $reg->service ?? null;
              $receiptUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute('portal.receipt.public', now()->addDays(7), ['payment' => $payment->id]);
            @endphp
            <tr>
              <td class="text-muted">{{ $payment->transaction_id }}</td>
              <td>${{ number_format($payment->amount, 2) }}</td>
              <td>{{ optional($payment->verified_at ?? $payment->created_at)->format('Y-m-d H:i') }}</td>
              <td>{{ $service ? $service->name : 'Service' }}</td>
              <td>
                <div>{{ $reg->full_name }}</div>
                <div class="small text-muted">{{ $reg->email }}</div>
              </td>
              <td>{{ ucfirst($payment->status) }}</td>
              <td><a href="{{ $receiptUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary">View</a></td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center">No online payments found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
