<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Land Ownership Verification (Custodial) – IPAMS</title>
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
        }

        body {
            font-family: 'Nunito', sans-serif;
            color: var(--mu-dark);
        }

        .top-brand-bar {
            height: 4px;
            background: linear-gradient(to right, #41adff 33%, #fff 33%, #fff 66%, #1e7e34 66%);
        }

        .hero {
            background: linear-gradient(135deg, var(--mu-blue-dark) 0%, var(--mu-blue) 100%);
            color: #fff;
            padding: 64px 0;
        }
    </style>
</head>

<body>
    <x-public-navbar />

    <header class="hero">
        <div class="container">
            <h1 class="fw-extrabold mb-2">Land Ownership Verification (Custodial)</h1>
            <p class="mb-0">Important: This is not a land title. Custodial verification only.</p>
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
                                    <label class="form-label">Unit ID (UUID)</label>
                                    <input type="text" class="form-control" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx" required>
                                </div>

                                <h5 class="mt-4 mb-3">Owner Contact</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" class="form-control" placeholder="e.g., Ahmed Warsame" required>
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


                                <div class="mb-3">
                                    <label class="form-label">Verified By</label>
                                    <input type="text" class="form-control" value="Officer assigned" disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Verified At</label>
                                    <input type="text" class="form-control" value="Set after review" disabled>
                                </div>

                                <button type="submit" class="btn btn-primary">Submit Ownership Claim</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-3">Custodial Notice</h6>
                            <p class="text-muted">This process verifies custodial ownership for apartment units within registered projects. It does not constitute a land title.</p>
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
