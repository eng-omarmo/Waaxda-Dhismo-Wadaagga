<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Payment Receipt</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <style>
      @media print {
        .no-print { display: none }
        body { background: #ffffff; }
      }

      body {
        padding: 24px;
        background: radial-gradient(circle at top left, #eef3ff 0, #f7f9fc 45%, #ffffff 100%);
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      }

      .receipt-card {
        background: #ffffff;
        border-radius: 12px;
        border-top: 4px solid #4f46e5;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.12);
        padding: 20px 24px 18px;
      }

      .receipt-header-title {
        font-weight: 600;
        letter-spacing: 0.03em;
        text-transform: uppercase;
      }

      .receipt-header-title span {
        color: #4f46e5;
      }

      .section-label {
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.14em;
        color: #6b7280;
        margin-bottom: 0.15rem;
      }

      .section-label-accent {
        color: #4f46e5;
      }

      .soft-label {
        font-size: 0.78rem;
        color: #6b7280;
      }

      hr {
        border: 0;
        border-top: 1px dashed #d1d5db;
        margin: 0.9rem 0 0.75rem;
      }

      .qr-frame {
        display: inline-block;
        background: #f9fafb;
        padding: 6px;
        border-radius: 10px;
        box-shadow: 0 0 0 1px rgba(148, 163, 184, 0.45), 0 4px 10px rgba(15, 23, 42, 0.12);
      }

      .badge-verified {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        box-shadow: 0 0 0 1px rgba(22, 163, 74, 0.45), 0 6px 14px rgba(22, 163, 74, 0.35);
      }

      .btn-print {
        border-radius: 999px;
        border-color: #4f46e5;
        color: #4f46e5;
      }

      .btn-print:hover {
        background-color: #4f46e5;
        color: #ffffff;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="receipt-card">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 class="mb-0 receipt-header-title">
            <span>Payment</span> Receipt #{{ $payment->receipt_number }}
          </h5>
          <div class="d-flex gap-2 align-items-center">
            @if(!empty($verified))
              <span class="badge badge-verified">Verified</span>
            @endif
            <button onclick="window.print()" class="btn btn-outline-secondary btn-sm no-print btn-print">
              Print
            </button>
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
            <div class="section-label section-label-accent">Payer</div>
            <div class="mb-1 fw-semibold">{{ $registration->full_name }}</div>
            <div class="mb-1 soft-label">{{ $registration->email }}</div>
            <div class="mb-1 soft-label">{{ $registration->phone }}</div>
          </div>
          <div class="col-md-6">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <div class="section-label section-label-accent">Registration</div>
                <div class="mb-1"><span class="soft-label">Status:</span> {{ ucfirst($registration->status) }}</div>
                <div class="mb-1"><span class="soft-label">Reference:</span> {{ $payment->reference }}</div>
              </div>
              <div class="text-end">
                <div class="small soft-label mb-1">Scan to verify</div>
                <div class="qr-frame">
                  {!! $qrSvg !!}
                </div>
                <a class="small no-print d-block mt-1" href="{{ $verifyUrl }}" target="_blank" rel="noopener">
                  Open verification link
                </a>
              </div>
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-6">
            <div class="section-label section-label-accent">Payment</div>
            <div class="mb-1"><span class="soft-label">Amount:</span> ${{ number_format($payment->amount,2) }}</div>
            <div class="mb-1"><span class="soft-label">Currency:</span> {{ $payment->currency }}</div>
            <div class="mb-1"><span class="soft-label">Provider:</span> {{ ucfirst($payment->provider) }}</div>
            <div class="mb-1"><span class="soft-label">Method:</span> {{ ucfirst($payment->payment_method) }}</div>
            <div class="mb-1"><span class="soft-label">Transaction:</span> {{ $payment->transaction_id }}</div>
          </div>
          <div class="col-md-6">
            <div class="section-label section-label-accent">Verification</div>
            <div class="mb-1"><span class="soft-label">Status:</span> {{ ucfirst($payment->status) }}</div>
            <div class="mb-1"><span class="soft-label">Verified At:</span> {{ $payment->verified_at }}</div>
          </div>
        </div>
        <hr>
        <div class="text-muted small soft-label">Generated: {{ now()->format('Y-m-d H:i') }}</div>
      </div>
    </div>
  </body>
</html>
