<!doctype html>
<html>
<body>
  <p>Dear {{ $request->user_full_name }},</p>
  <p>Your payment for the {{ $request->service->name }} request has been verified.</p>
  <p>Amount: ${{ number_format($payment->amount,2) }} on {{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}.</p>
  <p>We will proceed with processing. Thank you.</p>
  <p>You can download/print your receipt here: <a href="{{ $receiptUrl }}">{{ $receiptUrl }}</a></p>
</body>
</html>
