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
      <button onclick="window.print()" class="btn btn-outline-secondary btn-sm no-print">Print</button>
    </div>
    <hr>
    <div class="row">
      <div class="col-md-6">
        <h6 class="text-muted">Payer</h6>
        <div class="mb-1">{{ $registration->full_name }}</div>
        <div class="mb-1">{{ $registration->email }}</div>
        <div class="mb-1">{{ $registration->phone }}</div>
      </div>
      <div class="col-md-6">
        <h6 class="text-muted">Registration</h6>
        <div class="mb-1">Status: {{ ucfirst($registration->status) }}</div>
        <div class="mb-1">Reference: {{ $payment->reference }}</div>
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
