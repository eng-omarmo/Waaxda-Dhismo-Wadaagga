<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Payment Receipt</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <style>
      @media print {.no-print{display:none}}
      body { padding: 20px; }
    </style>
  </head>
  <body>
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h5 class="mb-0">Receipt #{{ $payment->receipt_number }}</h5>
      <div class="d-flex gap-2 align-items-center">
        @if(!empty($verified))
          <span class="badge bg-success">Verified</span>
        @endif
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm no-print">Print</button>
      </div>
    </div>
    <hr>
    @php
      $sig = hash_hmac('sha256', (string) $payment->id, config('app.key'));
      $verifyUrl = route('receipt.online.verify', ['payment' => $payment->id, 'sig' => $sig]);
      $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(120)->margin(1)->generate($verifyUrl);
    @endphp
    <div class="row">
      <div class="col-md-6">
        <h6 class="text-muted">Payer</h6>
        <div class="mb-1">{{ $registration->full_name }}</div>
        <div class="mb-1">{{ $registration->email }}</div>
        <div class="mb-1">{{ $registration->phone }}</div>
      </div>
      <div class="col-md-6">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h6 class="text-muted">Registration</h6>
            <div class="mb-1">Status: {{ ucfirst($registration->status) }}</div>
            <div class="mb-1">Reference: {{ $payment->reference }}</div>
          </div>
          <div class="text-end">
            <div class="small text-muted mb-1">Scan to verify</div>
            <div style="display:inline-block; background:#fff; padding:4px; border:1px solid #e9ecef; border-radius:6px">
              {!! $qrSvg !!}
            </div>
            <a class="small no-print" href="{{ $verifyUrl }}" target="_blank" rel="noopener">Open verification link</a>
          </div>
        </div>
      </div>
    </div>
    <hr>
    <div class="row">
      <div class="col-md-6">
        <h6 class="text-muted">Payment</h6>
        <div class="mb-1">Amount: ${{ number_format($payment->amount,2) }}</div>
        <div class="mb-1">Currency: {{ $payment->currency }}</div>
        <div class="mb-1">Provider: {{ ucfirst($payment->provider) }}</div>
        <div class="mb-1">Method: {{ ucfirst($payment->payment_method) }}</div>
        <div class="mb-1">Transaction: {{ $payment->transaction_id }}</div>
      </div>
      <div class="col-md-6">
        <h6 class="text-muted">Verification</h6>
        <div class="mb-1">Status: {{ ucfirst($payment->status) }}</div>
        <div class="mb-1">Verified At: {{ $payment->verified_at }}</div>
      </div>
    </div>
    <hr>
    <div class="text-muted small">Generated: {{ now()->format('Y-m-d H:i') }}</div>
  </body>
</html>
