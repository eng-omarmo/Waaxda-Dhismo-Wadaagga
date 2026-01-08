<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Business License – IPAMS</title>
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
      <h1 class="fw-extrabold mb-2">Business License</h1>
      <p class="mb-0">Apply for a license attached to an existing project.</p>
    </div>
  </header>

  <main class="py-5">
    <div class="container">
      <div class="row">
        <div class="col-lg-8">
          <div class="card">
            <div class="card-body">
              <form>
                <div class="mb-3">
                  <label class="form-label">ID (UUID)</label>
                  <input type="text" class="form-control" value="Auto-generated on submission" disabled>
                </div>
                <div class="mb-3">
                  <label class="form-label">Project ID (UUID)</label>
                  <input type="text" class="form-control" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" required pattern="^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$">
                  <div class="form-text">Reference an existing project UUID.</div>
                </div>
                <div class="mb-3">
                  <label class="form-label">License Type</label>
                  <select class="form-select" required>
                    <option value="">Select type</option>
                    <option value="Rental">Rental</option>
                    <option value="Commercial">Commercial</option>
                  </select>
                </div>
                <button type="submit" class="btn btn-primary">Submit Application</button>
              </form>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card">
            <div class="card-body">
              <h6 class="mb-3">Notes</h6>
              <p class="text-muted">The license is linked to a project via project_id. Choose the appropriate license type: Rental or Commercial.</p>
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
</body>
</html>
