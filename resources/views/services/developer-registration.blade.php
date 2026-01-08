<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Organization Registration – IPAMS</title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
  <style>
    :root { --mu-blue: #002d80; --mu-blue-dark: #001a4d; --mu-green: #1e7e34; --mu-dark: #121416; }
    body { font-family: 'Nunito', sans-serif; color: var(--mu-dark); }
    .top-brand-bar { height: 4px; background: linear-gradient(to right, #41adff 33%, #fff 33%, #fff 66%, #1e7e34 66%); }
    .hero { background: linear-gradient(135deg, var(--mu-blue-dark) 0%, var(--mu-blue) 100%); color: #fff; padding: 64px 0; }
  </style>
</head>
<body>
  <x-public-navbar />

  <header class="hero">
    <div class="container">
      <h1 class="fw-extrabold mb-2">Organization Registration</h1>
      <p class="mb-0">Register your organization to access IPAMS services.</p>
    </div>
  </header>

  <main class="py-5">
    <div class="container">
      <div class="row">
        <div class="col-lg-8">
          <div class="card">
            <div class="card-body">
              @if (session('status'))
                <div class="alert alert-success" role="alert">{{ session('status') }}</div>
              @endif
              <form method="POST" action="{{ route('services.developer-registration.store') }}" enctype="multipart/form-data" novalidate>
                @csrf
                <h5 class="mb-3">Organization Information</h5>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Organization Name</label>
                    <input name="name" type="text" class="form-control" placeholder="e.g., Daru Salaam Developers Ltd." required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Registration Number</label>
                    <input name="registration_number" type="text" class="form-control" placeholder="e.g., REG-12345">
                  </div>
                  <div class="col-md-12 mb-3">
                    <label class="form-label">Address</label>
                    <input name="address" type="text" class="form-control" placeholder="Street, District, City" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Organization Type</label>
                    <select name="type" class="form-select" required>
                      <option value="">Select type</option>
                      <option value="Developer">Developer</option>
                      <option value="Contractor">Contractor</option>
                      <option value="Consultant">Consultant</option>
                      <option value="Other">Other</option>
                    </select>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Website (optional)</label>
                    <input type="url" class="form-control" placeholder="https://example.com">
                  </div>
                </div>

                <h5 class="mt-4 mb-3">Documents</h5>
                <div class="mb-3">
                  <label class="form-label">Upload Documents</label>
                  <input name="documents[]" type="file" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                </div>

                <h5 class="mt-4 mb-3">Owner / Contact</h5>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Full Name</label>
                    <input name="contact_full_name" type="text" class="form-control" placeholder="e.g., Mohamed Ali" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Role</label>
                    <input name="contact_role" type="text" class="form-control" placeholder="e.g., Owner / Director" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input name="contact_phone" type="tel" class="form-control" placeholder="061XXXXXXX" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input name="contact_email" type="email" class="form-control" placeholder="you@example.com" required>
                  </div>
                </div>

                <div class="form-check mt-3 mb-4">
                  <input name="terms" class="form-check-input" type="checkbox" value="1" id="termsCheck" required>
                  <label class="form-check-label" for="termsCheck">I accept the terms and conditions.</label>
                </div>
                <button id="submitBtn" type="submit" class="btn btn-primary">Submit Registration</button>
              </form>
              @if ($errors->any())
                <div class="alert alert-danger mt-3" role="alert">
                  <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card">
            <div class="card-body">
              <h6 class="mb-3">Guidelines</h6>
              <p class="text-muted">Provide accurate organization details and attach supporting documents. The owner contact is used for verification and follow-up.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <footer class="py-4 bg-white border-top">
    <div class="container text-center">
      <p class="mb-0 text-muted fw-bold">© {{ date('Y') }} Dowladda Hoose ee Xamar – Integrated Property & Apartment Management System</p>
    </div>
  </footer>

  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script>
    const terms = document.getElementById('termsCheck');
    const submitBtn = document.getElementById('submitBtn');
    if (terms && submitBtn) {
      submitBtn.disabled = true;
      terms.addEventListener('change', () => { submitBtn.disabled = !terms.checked; });
    }
  </script>
</body>
</html>
