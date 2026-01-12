@extends('layouts.mazer')
@section('title','Profile')
@section('page-heading','My Profile')
@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-3">
      <div class="card-header">
        <h6 class="mb-0">Account Information</h6>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="mb-2"><strong>Name:</strong> {{ $user->first_name }} {{ $user->last_name }}</div>
            <div class="mb-2"><strong>Email:</strong> {{ $user->email }}</div>
            <div class="mb-2"><strong>Phone:</strong> {{ $user->contact_phone }}</div>
          </div>
          <div class="col-md-6">
            <a href="{{ route('profile.update') }}" class="btn btn-outline-primary">Update Profile</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Your Portal</h6>
        <span class="badge bg-primary">{{ ucfirst($role) }}</span>
      </div>
      <div class="card-body">
        <div class="d-flex flex-wrap gap-2 align-items-center">
          <button id="toggleServices" class="btn btn-sm btn-outline-secondary">Toggle Services</button>
          <button id="toggleRequests" class="btn btn-sm btn-outline-secondary">Toggle Requests</button>
          <button id="togglePayments" class="btn btn-sm btn-outline-secondary">Toggle Payments</button>
        </div>
        <div class="text-muted small mt-2">Personalize your view. Preferences are saved in your browser.</div>
      </div>
    </div>
  </div>
</div>

@if($role === 'user')
<div id="portalServices" class="row">
  <div class="col-12">
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Available Services</h6>
        <span class="text-muted small">Start or view service details</span>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              @forelse($services as $service)
                <tr>
                  <td>{{ $service->name }}</td>
                  <td>{{ Str::limit($service->description, 100) }}</td>
                  <td>${{ number_format($service->price, 2) }}</td>
                  <td class="text-end">
                    <a href="{{ route('services.show', $service->slug) }}" class="btn btn-sm btn-primary">Open</a>
                  </td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-center">No services available.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="portalRequests" class="row">
  <div class="col-12">
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">My Requests</h6>
        <span class="text-muted small">Track status and progress</span>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Service</th>
                <th>Status</th>
                <th>Requested</th>
                <th>Progress</th>
                <th>Last Payment</th>
              </tr>
            </thead>
            <tbody>
              @forelse($requests as $r)
                @php
                  $progress = match($r->status) {
                    'pending' => 33,
                    'discrepancy' => 50,
                    'verified' => 100,
                    'rejected' => 100,
                    default => 25,
                  };
                  $lastPayment = optional($r->payments()->latest()->first())->amount;
                @endphp
                <tr>
                  <td>{{ $r->service->name }}</td>
                  <td>
                    <span class="badge bg-{{ $r->status==='verified' ? 'success' : ($r->status==='discrepancy' ? 'warning' : ($r->status==='rejected' ? 'danger' : 'secondary')) }}">
                      {{ ucfirst($r->status) }}
                    </span>
                  </td>
                  <td>{{ $r->created_at->format('Y-m-d H:i') }}</td>
                  <td style="min-width:160px">
                    <div class="progress" style="height: 8px;">
                      <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%"></div>
                    </div>
                  </td>
                  <td>{{ $lastPayment ? '$'.number_format($lastPayment,2) : '—' }}</td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center">No requests yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@else
<div class="row">
  <div class="col-12">
    <div class="card mb-3">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <h6 class="mb-1">Admin Tools</h6>
          <div class="text-muted small">Use the sidebar to access admin functions.</div>
        </div>
        <a href="{{ url('/admin') }}" class="btn btn-outline-primary">Open Admin Panel</a>
      </div>
    </div>
  </div>
</div>
@endif

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Payment Confirmation</h6>
        <span class="text-muted small">Shows your payment history and receipts</span>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Receipt</th>
                <th>Service</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date/Time</th>
                <th>Transaction ID</th>
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
                  <td><a href="{{ $receiptUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary">View Receipt</a></td>
                  <td>{{ $service ? $service->name : 'Service' }}</td>
                  <td>${{ number_format($payment->amount, 2) }}</td>
                  <td>{{ ucfirst($payment->status) }}</td>
                  <td>{{ optional($payment->verified_at ?? $payment->created_at)->format('Y-m-d H:i') }}</td>
                  <td class="text-muted">{{ $payment->transaction_id }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center">No payments found.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@push('page-scripts')
<script>
  (function() {
    const ids = ['portalServices','portalRequests',''];
    function getPref(key, def) {
      try { return localStorage.getItem(key) ?? def; } catch(e) { return def; }
    }
    function setPref(key, val) {
      try { localStorage.setItem(key, val); } catch(e) {}
    }
    const ps = document.getElementById('portalServices');
    const pr = document.getElementById('portalRequests');
    const paymentsSection = document.querySelector('.card-header h6.mb-0')?.innerText === 'Payment Confirmation'
      ? document.querySelector('.card').parentElement : null;
    const prefServices = getPref('pref.portal.services','show');
    const prefRequests = getPref('pref.portal.requests','show');
    const prefPayments = getPref('pref.portal.payments','show');
    if (prefServices === 'hide' && ps) ps.style.display = 'none';
    if (prefRequests === 'hide' && pr) pr.style.display = 'none';
    if (prefPayments === 'hide' && paymentsSection) paymentsSection.style.display = 'none';
    const btnServices = document.getElementById('toggleServices');
    const btnRequests = document.getElementById('toggleRequests');
    const btnPayments = document.getElementById('togglePayments');
    if (btnServices && ps) btnServices.addEventListener('click', function() {
      const now = ps.style.display === 'none' ? 'show' : 'hide';
      ps.style.display = now === 'show' ? '' : 'none';
      setPref('pref.portal.services', now);
    });
    if (btnRequests && pr) btnRequests.addEventListener('click', function() {
      const now = pr.style.display === 'none' ? 'show' : 'hide';
      pr.style.display = now === 'show' ? '' : 'none';
      setPref('pref.portal.requests', now);
    });
    if (btnPayments && paymentsSection) btnPayments.addEventListener('click', function() {
      const now = paymentsSection.style.display === 'none' ? 'show' : 'hide';
      paymentsSection.style.display = now === 'show' ? '' : 'none';
      setPref('pref.portal.payments', now);
    });
  })();
</script>
@endpush
@endsection
