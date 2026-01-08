<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - IPAMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <style>
        body {
            background-color: #f4f7f9;
            font-family: 'Nunito', sans-serif;
            display: grid;
            place-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 2rem;
        }
        .form-control {
            border-color: #e2e8f0;
            padding: 0.75rem;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #002d80;
        }
        .btn-primary {
            background-color: #002d80;
            border: none;
            padding: 0.75rem;
        }
    </style>
</head>
<body>

<div class="login-card shadow-sm">
    <div class="text-center mb-4">
        <h2 class="fw-bold text-dark mb-1">IPAMS</h2>
        <p class="text-muted small">Enter credentials to access portal</p>
    </div>

    <form method="POST" action="{{ route('login.attempt') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label small fw-bold">Email</label>
            <input name="email" type="email" class="form-control" placeholder="email@example.com" required autofocus>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-bold">Password</label>
            <input name="password" type="password" class="form-control" placeholder="••••••••" required>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                <label class="form-check-label small text-muted" for="remember">Remember me</label>
            </div>
            <a href="#" class="small text-decoration-none">Forgot?</a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger p-2 small">
                @foreach ($errors->all() as $error)
                    <div class="mb-0">{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <button type="submit" class="btn btn-primary w-100 fw-bold">Sign In</button>
    </form>

    <div class="mt-4 text-center">
        <p class="small text-muted mb-0">Unauthorized access is prohibited.</p>
    </div>
</div>

</body>
</html>
