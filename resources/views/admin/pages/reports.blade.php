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
    @php
      $payments = \App\Models\OnlinePayment::with(['registration', 'registration.service'])->latest()->limit(100)->get();
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
