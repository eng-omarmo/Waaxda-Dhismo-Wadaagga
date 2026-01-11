<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Payment Receipt</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <style>
      @media print {
        .no-print { display: none; }
      }
      body { padding: 20px; }
    </style>
  </head>
  <body>
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h5 class="mb-0">Receipt #{{ $receiptNumber }}</h5>
      <button onclick="window.print()" class="btn btn-outline-secondary btn-sm no-print">Print</button>
    </div>
    <hr>
    <div class="row">
      <div class="col-md-6">
        <h6 class="text-muted">Payer</h6>
        <div class="mb-1">{{ $request->user_full_name }}</div>
        <div class="mb-1">{{ $request->user_email }}</div>
        <div class="mb-1">{{ $request->user_phone }}</div>
      </div>
      <div class="col-md-6">
        <h6 class="text-muted">Service</h6>
        <div class="mb-1">{{ $request->service->name }}</div>
        <div class="mb-1">Price: ${{ number_format($request->service->price,2) }}</div>
        <div class="mb-1">Request ID: {{ $request->id }}</div>
      </div>
    </div>
    <hr>
    <div class="row">
      <div class="col-md-6">
        <h6 class="text-muted">Payment</h6>
        <div class="mb-1">Amount: ${{ number_format($payment->amount,2) }}</div>
        <div class="mb-1">Date: {{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}</div>
        <div class="mb-1">Reference: {{ $payment->reference_number }}</div>
      </div>
      <div class="col-md-6">
        <h6 class="text-muted">Verification</h6>
        <div class="mb-1">Status: {{ ucfirst($payment->status) }}</div>
        <div class="mb-1">Verified At: {{ $payment->verified_at }}</div>
        <div class="mb-1">Verified By: {{ $payment->verifier?->first_name }} {{ $payment->verifier?->last_name }}</div>
      </div>
    </div>
    @if($payment->reconciled_amount)
      <hr>
      <h6 class="text-muted">Reconciliation</h6>
      <div class="mb-1">Reconciled Amount: ${{ number_format($payment->reconciled_amount,2) }}</div>
      <div class="mb-1">Notes: {{ $payment->reconciliation_notes }}</div>
    @endif
    <hr>
    <div class="text-muted small">Generated: {{ now()->format('Y-m-d H:i') }}</div>
  </body>
</html>
