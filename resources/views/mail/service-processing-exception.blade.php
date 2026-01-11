<!doctype html>
<html>
<body>
  <p>Dear {{ $request->user_full_name }},</p>
  <p>There is an issue with your {{ $request->service->name }} request:</p>
  <p>{{ $messageText }}</p>
  <p>Our team will contact you if further information is required.</p>
</body>
</html>
