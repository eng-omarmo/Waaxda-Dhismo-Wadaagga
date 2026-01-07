<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Project Registration – IPAMS</title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
  <style>
    :root {
      --mu-blue: #002d80;
      --mu-blue-dark: #001a4d;
      --mu-green: #1e7e34;
      --mu-dark: #121416;
      --mu-grey-bg: #f8f9fc;
    }
    body { font-family: 'Nunito', sans-serif; color: var(--mu-dark); }
    .top-brand-bar { height: 4px; background: linear-gradient(to right, #41adff 33%, #fff 33%, #fff 66%, #1e7e34 66%); }
    .hero { background: linear-gradient(135deg, var(--mu-blue-dark) 0%, var(--mu-blue) 100%); color: #fff; padding: 64px 0; }
    .section-heading { font-weight: 800; color: var(--mu-blue-dark); }
  </style>
  @stack('page-styles')
</head>
<body>
  <div class="top-brand-bar"></div>
  <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
    <div class="container">
      <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ url('/') }}">
        <img src="{{ asset('assets/images/logo/logo.png') }}" alt="Logo" height="50" class="me-3">
        <div class="lh-1 border-start ps-3">
          <span class="fs-6 d-block text-muted fw-bold text-uppercase" style="font-size: 0.7rem !important;">Dowladda Hoose ee Xamar</span>
          <span class="fs-5 text-dark fw-extrabold">Waaxda Dhismo Wadaagga</span>
        </div>
      </a>
      <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav ms-auto align-items-center">
          <li class="nav-item"><a class="nav-link px-3 fw-bold" href="{{ url('/#services') }}">Adeegyada</a></li>
          <li class="nav-item"><a class="nav-link px-3 fw-bold" href="{{ url('/#department-info') }}">Habraaca</a></li>
          <li class="nav-item ms-lg-3"><a class="btn btn-primary fw-bold" href="/login">Gali System-ka</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <header class="hero">
    <div class="container">
      <h1 class="fw-extrabold mb-2">Project Registration</h1>
      <p class="mb-0">Public registration. No account required. Developer can be assigned later.</p>
    </div>
  </header>

  <main class="py-5">
    <div class="container">
      <div class="row">
        <div class="col-lg-8">
          <div class="card">
            <div class="card-body">
              <h5 class="mb-4">Your Details</h5>
              <form>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" placeholder="e.g., Mohamed Ali" required>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="tel" class="form-control" placeholder="061XXXXXXX" required>
                  </div>
                  <div class="col-md-12 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" placeholder="you@example.com" required>
                  </div>
                </div>

                <h5 class="mt-4 mb-3">Project Details</h5>
                <div class="mb-3">
                  <label class="form-label">Project ID</label>
                  <input type="text" class="form-control" value="Auto-generated on submission" disabled>
                </div>
                <div class="mb-3">
                  <label class="form-label">Project Name</label>
                  <input type="text" class="form-control" placeholder="e.g., Daru Salaam Apartments Phase II" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Location</label>
                  <input type="text" class="form-control" placeholder="District, neighborhood or map reference" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Developer (optional)</label>
                  <input type="text" class="form-control" placeholder="Leave blank; assign later by an officer">
                </div>
                <div class="mb-3">
                  <label class="form-label">Status</label>
                  <select class="form-select">
                    <option selected>Draft</option>
                    <option>Submitted</option>
                    <option disabled>Approved</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">Created At</label>
                  <input type="text" class="form-control" value="{{ date('Y-m-d') }}" disabled>
                </div>
                <div class="d-flex gap-2">
                  <button type="button" class="btn btn-primary">Save Draft</button>
                  <button type="button" class="btn btn-success">Submit Registration</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card">
            <div class="card-body">
              <h6 class="mb-3">About Project Registration</h6>
              <p class="text-muted">All services attach to projects. Register the project first to proceed with permits, buildings, units, licensing, ownership and transfers.</p>
              <hr>
              <div class="list-group small">
                <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                  Construction Permits
                  <span class="badge bg-light text-primary">After submission</span>
                </a>
                <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                  Buildings & Units
                  <span class="badge bg-light text-primary">After approval</span>
                </a>
                <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                  Licensing
                  <span class="badge bg-light text-primary">After approval</span>
                </a>
                <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                  Ownership & Transfers
                  <span class="badge bg-light text-primary">After unit creation</span>
                </a>
              </div>
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
  @stack('page-scripts')
</body>
</html>
