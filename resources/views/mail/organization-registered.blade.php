<!DOCTYPE html>
<html>
<body>
  <p>Dear {{ $org->contact_full_name }},</p>
  <p>Your organization registration has been received.</p>
  <p>Organization: {{ $org->name }}</p>
  <p>Status: {{ $org->status }}</p>
  <p>We will review your application and notify you upon approval.</p>
  <p>Thank you.</p>
</body>
</html>

