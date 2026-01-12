<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration – IPAMS</title>
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
            --mu-soft-bg: #f8f9fa;
        }

        body {
            font-family: 'Nunito', sans-serif;
            color: var(--mu-dark);
            background-color: var(--mu-soft-bg);
        }

        .hero {
            background: linear-gradient(135deg, var(--mu-blue-dark) 0%, var(--mu-blue) 100%);
            color: #fff;
            padding: 50px 0;
        }

        .section-header {
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 25px;
            color: var(--mu-blue);
            font-weight: 700;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.9rem;
            color: #444;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .sticky-top {
            top: 20px;
        }

        .btn-primary {
            background-color: var(--mu-blue);
            border: none;
            padding: 12px 30px;
            font-weight: 700;
        }

        .btn-primary:disabled {
            background-color: #ccc;
        }
    </style>
</head>

<body>
    <x-public-navbar />

    <header class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="fw-extrabold mb-2">Registration Portal</h1>
                    <p class="lead mb-0 opacity-75">Fadlan buuxi foomka hoose si aad u diiwaangaliso macluumaadkaaga.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <span class="badge bg-success p-2 px-3">Official Service</span>
                </div>
            </div>
        </div>
    </header>

    <main class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <form method="POST" action="{{ route('services.construction-permit.store') }}" enctype="multipart/form-data">
                        @csrf
                        @if(isset($payment))
                        <input type="hidden" name="payment_id" value="{{ $payment->id }}">
                        @endif

                        <div class="card mb-4">
                            <div class="card-body p-4">
                                <h5 class="section-header">
                                    <i class="bi bi-person-badge me-2"></i>1. Macluumaadka Codsadaha (Applicant)
                                </h5>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Magaca Buuxa (Full Name)</label>
                                        <input name="applicant_full_name" type="text" class="form-control" value="{{ old('applicant_full_name') }}" placeholder="Enter full name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Aqoonsiga Qaranka / Passport</label>
                                        <input name="applicant_national_id" type="text" class="form-control" value="{{ old('applicant_national_id') }}" placeholder="ID Number" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Kaalintaada (Role)</label>
                                        <select name="applicant_role" class="form-select" required>
                                            <option value="">Dooro kaalinta...</option>
                                            <option value="Owner">Milkiile (Owner)</option>
                                            <option value="Legal Representative">Wakiil Sharci ah</option>
                                            <option value="Developer">Horumariye (Developer)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Lambarka Taleefanka</label>
                                        <input name="applicant_phone" type="tel" class="form-control" value="{{ old('applicant_phone') }}" placeholder="061XXXXXXX" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email (Ikhtiyaar)</label>
                                        <input name="applicant_email" type="email" class="form-control" value="{{ old('applicant_email') }}" placeholder="name@example.com">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Degmada / Cinwaanka (Address)</label>
                                        <input name="applicant_address" type="text" class="form-control" value="{{ old('applicant_address') }}" placeholder="District, Neighborhood, House No." required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-body p-4">
                                <h5 class="section-header">
                                    <i class="bi bi-map me-2"></i>2. Macluumaadka Dhulka (Land Details)
                                </h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Lambarka Dhulka (Plot Number)</label>
                                        <input name="plot_number" type="text" class="form-control" value="{{ old('plot_number') }}" placeholder="e.g. PLT-990" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Caddeyn Lahaansho (Title Deed No.)</label>
                                        <input name="land_title_number" type="text" class="form-control" value="{{ old('land_title_number') }}" placeholder="e.g. T-12345" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Cabbirka Dhulka (sqm)</label>
                                        <div class="input-group">
                                            <input name="land_size_sqm" type="number" class="form-control" value="{{ old('land_size_sqm') }}" placeholder="e.g. 400" required>
                                            <span class="input-group-text">m²</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Degmada (District)</label>
                                        <input name="land_location_district" type="text" class="form-control" value="{{ old('land_location_district') }}" placeholder="e.g. Hodan" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nooca Isticmaalka (Zoning)</label>
                                        <select name="land_use_zoning" class="form-select" required>
                                            <option value="">Select zoning...</option>
                                            <option value="Residential">Residential</option>
                                            <option value="Commercial">Commercial</option>
                                            <option value="Mixed">Mixed Use</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Nooca Milkiyadda (Ownership)</label>
                                        <select name="land_ownership_type" class="form-select" required>
                                            <option value="">Select type...</option>
                                            <option value="Private">Milkiyad Gaar ah</option>
                                            <option value="Shared">Wadaag</option>
                                            <option value="Government">Dawlad</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-body p-4">
                                <h5 class="section-header">
                                    <i class="bi bi-file-earmark-arrow-up me-2"></i>3. Dukumiintiyada & Xaqiijinta
                                </h5>
                                <div class="mb-4">
                                    <label class="form-label">Upload Documents (ID, Title Deed, Photos)</label>
                                    <input name="documents[]" type="file" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="text-muted">You can upload multiple files (PDF or Images).</small>
                                </div>

                                <div class="form-check mb-4">
                                    <input name="terms" class="form-check-input" type="checkbox" id="termsCheck" required>
                                    <label class="form-check-label ms-2" for="termsCheck">
                                        Waxaan xaqiijinayaa in xogta kor ku xusan ay tahay mid sax ah. (I confirm the data is accurate).
                                    </label>
                                </div>

                                <button id="submitBtn" type="submit" class="btn btn-primary w-100 py-3 shadow-sm">
                                    Gudbi Codsiga (Submit Registration)
                                </button>

                                @if ($errors->any())
                                <div class="alert alert-danger mt-4">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                            </div>
                        </div>


                    </form>
                </div>

                <div class="col-lg-4">
                    <div class="sticky-top">
                        <div class="card bg-white mb-3">
                            <div class="card-body">
                                <h6 class="fw-bold text-primary"><i class="bi bi-info-circle me-2"></i>Tilmaamo Muhiim ah</h6>
                                <hr>
                                <ul class="small text-muted ps-3">
                                    <li class="mb-2">Hubi in magacaagu u qoran yahay sida ku cad aqoonsigaaga qaranka.</li>
                                    <li class="mb-2">Lambarka dhulka iyo Title Deed waa inay waafaqaan diiwaanka dowladda.</li>
                                    <li>Dukumiintiyada la soo raryo waa inay ahaadaan kuwo cad oo la akhrin karo.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="card bg-light border-0">
                            <div class="card-body text-center py-4">
                                <i class="bi bi-headset fs-1 text-muted"></i>
                                <h6 class="mt-2 mb-1">Ma u baahan tahay caawinaad?</h6>
                                <p class="small text-muted">Nagala soo xiriir <strong>support@ipams.so</strong></p>
                            </div>
                        </div>
                        @if(isset($payment))
                        <div class="card bg-white mt-3">
                            <div class="card-body">
                                <h6 class="fw-bold text-primary"><i class="bi bi-receipt-cutoff me-2"></i>Payment</h6>
                                <div class="d-flex justify-content-between">
                                    <div class="text-muted small">Transaction</div>
                                    <div class="small">{{ $payment->transaction_id }}</div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div class="text-muted small">Reference</div>
                                    <div class="small">{{ $payment->reference }}</div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div class="text-muted small">Amount</div>
                                    <div class="small">${{ number_format($payment->amount,2) }}</div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="py-4 bg-white border-top">
        <div class="container text-center">
            <p class="mb-0 text-muted fw-bold small">© {{ date('Y') }} Dowladda Hoose ee Xamar – IPAMS Portal</p>
        </div>
    </footer>

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        const terms = document.getElementById('termsCheck');
        const submitBtn = document.getElementById('submitBtn');
        if (terms && submitBtn) {
            submitBtn.disabled = true;
            terms.addEventListener('change', () => {
                submitBtn.disabled = !terms.checked;
            });
        }
    </script>
</body>

</html>
