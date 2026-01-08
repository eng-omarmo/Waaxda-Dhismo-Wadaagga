<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You – Project Registration</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>

<body>
    <x-public-navbar />
    <main class="py-5">`
        <div class="container">`
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body text-center">
                            <h3 class="mb-3">Thank you for your submission</h3>
                            <p class="text-muted mb-4">Your project registration has been received. Reference ID:</p>
                            <div class="display-6 fw-bold">{{ $id }}</div>
                            <p class="mt-3">You will be contacted for further steps. You may continue to browse services or return to the homepage.</p>
                            <div class="d-flex justify-content-center gap-2 mt-4">
                                <a href="{{ url('/#services') }}" class="btn btn-outline-primary">Browse Services</a>
                                <a href="{{ url('/') }}" class="btn btn-primary">Go Home</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
