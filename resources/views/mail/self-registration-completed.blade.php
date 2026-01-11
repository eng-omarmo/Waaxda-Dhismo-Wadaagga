<!doctype html>
<html>
<body>
  <p>Welcome {{ $user->first_name }} {{ $user->last_name }},</p>
  <p>Your registration is complete. You can now sign in to your dashboard using these credentials:</p>
  <p>Email: {{ $user->email }}<br>Password: {{ $passwordPlain }}</p>
  <p>For security, please change your password after logging in.</p>
  <p>Payment Receipt: <a href="{{ $receiptUrl }}">{{ $receiptUrl }}</a></p>
  <p>Thank you,<br>IPAMS Team</p>
</body>
</html>
