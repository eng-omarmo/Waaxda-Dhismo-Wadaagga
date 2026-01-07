<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - IPAMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/auth.css') }}">
</head>
<body>
<div id="auth">
    <div class="row h-100">
        <div class="col-lg-6 col-12">
            <div id="auth-left">
                <div class="auth-logo mb-3">
                    <a href="{{ url('/') }}"><img src="{{ asset('assets/images/logo/logo.png') }}" alt="Logo"></a>
                </div>
                <h1 class="auth-title">Create your account</h1>
                <form method="POST" action="{{ route('register.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <input name="first_name" value="{{ old('first_name') }}" type="text" class="form-control form-control-xl" placeholder="First name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <input name="last_name" value="{{ old('last_name') }}" type="text" class="form-control form-control-xl" placeholder="Last name" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <input name="email" value="{{ old('email') }}" type="email" class="form-control form-control-xl" placeholder="Email address" required>
                    </div>
                    <div class="mb-3">
                        <input name="contact_phone" value="{{ old('contact_phone') }}" type="text" class="form-control form-control-xl" placeholder="Contact phone" required>
                    </div>
                    <div class="mb-3">
                        <input name="contact_address" value="{{ old('contact_address') }}" type="text" class="form-control form-control-xl" placeholder="Contact address">
                    </div>
                    <div class="mb-3">
                        <input name="password" type="password" class="form-control form-control-xl" placeholder="Password" required>
                        <small class="text-muted">Minimum 12 characters, mixed case, numbers, symbols.</small>
                    </div>
                    <div class="mb-3">
                        <input name="password_confirmation" type="password" class="form-control form-control-xl" placeholder="Confirm password" required>
                    </div>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <button class="btn btn-primary btn-block btn-lg shadow-lg mt-3" type="submit">Register</button>
                </form>
                <div class="text-center mt-4">
                    <a href="{{ route('login') }}">Already have an account? Log in</a>
                </div>
            </div>
        </div>
        <div class="col-lg-6 d-none d-lg-block">
            <div id="auth-right"></div>
        </div>
    </div>
</div>
</body>
</html>
