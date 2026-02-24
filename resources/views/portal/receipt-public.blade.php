<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Payment Receipt – IPAMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <style>
      @media print {.no-print{display:none}}
      body { padding:20px; background-color:#f5f5f5; }
      .receipt-card {
        max-width: 900px;
        margin: 0 auto;
        background: #ffffff;
        padding: 24px 28px;
        border-radius: 12px;
        box-shadow: 0 6px 16px rgba(0,0,0,0.08);
        border-top: 4px solid #002d80;
      }
      .brand-header {
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 12px;
        margin-bottom: 16px;
      }
      .brand-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #002d80;
        margin-bottom: 0;
      }
      .brand-sub {
        font-size: 0.8rem;
        color: #6c757d;
      }
      .receipt-title {
        font-size: 1.1rem;
        font-weight: 700;
      }
    </style>
  </head>
  <body>
    <div class="receipt-card">
      <div class="brand-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
          <img src="{{ asset('assets/images/logo/logo.png') }}" alt="IPAMS" style="height:40px;">
          <div>
            <div class="brand-title">Banaadir Regional Administration</div>
            <div class="brand-sub">Integrated Property & Apartment Management System (IPAMS)</div>
          </div>
        </div>
        <div class="text-end">
          <div class="receipt-title">Payment Receipt</div>
          <div class="text-muted small">#{{ $payment->receipt_number }}</div>
        </div>
      </div>
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
          <div class="small text-muted">Service</div>
          <div class="fw-semibold">{{ $service->name }}</div>
        </div>
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
      $verifyUrl = route('portal.receipt.verify', ['payment' => $payment->id, 'sig' => $sig]);
      $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(120)->margin(1)->generate($verifyUrl);
    @endphp
      <div class="row">
      <div class="col-md-6">
        <h6 class="text-muted">Payer</h6>
        <div class="mb-1">{{ $reg->full_name }}</div>
        <div class="mb-1">{{ $reg->email }}</div>
        <div class="mb-1">{{ $reg->phone }}</div>
      </div>
      <div class="col-md-6">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h6 class="text-muted">Service</h6>
            <div class="mb-1">{{ $service->name }}</div>
            <div class="mb-1">Price: ${{ number_format($service->price,2) }}</div>
          </div>
          <div class="text-end">
            <div class="small text-muted mb-1">Scan to verify</div>
            <div style="display:inline-block; background:#fff; padding:4px; border:1px solid #e9ecef; border-radius:6px">
              {!! $qrSvg !!}
            </div>
            <div class="small text-muted mt-1" style="max-width: 180px; word-break: break-word;">
              Ref: {{ $payment->reference }}
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
        <h6 class="text-muted">Confirmation</h6>
        <div class="mb-1">Status: {{ ucfirst($payment->status) }}</div>
        <div class="mb-1">Verified At: {{ $payment->verified_at }}</div>
      </div>
      </div>
      <hr>
      <div class="text-muted small">Generated: {{ now()->format('Y-m-d H:i') }}</div>
    </div>
  </body>
</html>
