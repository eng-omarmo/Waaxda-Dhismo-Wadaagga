<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waaxda Dhismo Wadaagga – Dowladda Hoose ee Xamar</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <style>
        :root {
            --mu-blue: #002d80;
            /* Deepened for better contrast */
            --mu-blue-dark: #001a4d;
            --mu-green: #1e7e34;
            /* Darkened for accessibility */
            --mu-dark: #121416;
            --mu-grey-bg: #f8f9fc;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: #ffffff;
            color: var(--mu-dark);
            line-height: 1.6;
        }

        /* Top Bar Branding */
        .top-brand-bar {
            height: 4px;
            background: linear-gradient(to right, #41adff 33%, #fff 33%, #fff 66%, #1e7e34 66%);
        }

        .navbar {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 0.8rem 0;
        }

        /* Hero with sharper focus */
        .hero-gradient {
            background: linear-gradient(135deg, var(--mu-blue-dark) 0%, var(--mu-blue) 100%);
            color: white;
            padding: 120px 0;
            position: relative;
            overflow: hidden;
        }

        .hero-gradient::after {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: url("https://www.transparenttextures.com/patterns/cubes.png");
            opacity: 0.05;
        }

        /* Service Cards - Higher Contrast */
        .service-card {
            border: 1px solid #e3e6f0;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            background: #fff;
        }

        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
            border-color: var(--mu-blue);
        }

        .icon-box {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
        }

        /* Contact Section Sharpness */
        .contact-card-info {
            background: var(--mu-blue-dark);
            color: white;
            border-radius: 15px;
            padding: 40px;
            height: 100%;
        }

        .contact-form-container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            border: 1px solid #e3e6f0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .form-control,
        .form-select {
            border: 2px solid #edeff5;
            padding: 12px 15px;
            font-weight: 600;
        }

        .form-control:focus {
            border-color: var(--mu-blue);
            box-shadow: none;
        }

        .section-heading {
            font-weight: 800;
            color: var(--mu-blue-dark);
            margin-bottom: 1rem;
            letter-spacing: -0.5px;
        }

        .badge-gov {
            background-color: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
        }
    </style>
</head>

<body>
    <div class="top-brand-bar"></div>
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
                <img src="{{ asset('assets/images/logo/logo.png') }}" alt="Logo" height="50" class="me-3">
                <div class="lh-1 border-start ps-3">
                    <span class="fs-6 d-block text-muted fw-bold text-uppercase" style="font-size: 0.7rem !important;">Dowladda Hoose ee Xamar</span>
                    <span class="fs-5 text-dark fw-extrabold">Waaxda Dhismo Wadaagga</span>
                </div>
            </a>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link px-3 fw-bold" href="#services">Adeegyada</a></li>
                    <li class="nav-item"><a class="nav-link px-3 fw-bold" href="#department-info">Habraaca</a></li>
                    <li class="nav-item ms-lg-3"><a class="btn btn-primary fw-bold" href="/login">Gali System-ka</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero-gradient">
        <div class="container position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <span class="badge badge-gov mb-3 px-3 py-2">Nidaamka Maamulka Dhismooyinka (IPAMS)</span>
                    <h1 class="display-3 fw-extrabold mb-3">Adeeg Hufan & <br><span class="text-info">Diiwaangelin Casri ah</span></h1>
                    <p class="fs-5 opacity-90 mb-4 fw-normal">Waxaan u fududaynaynaa muwaadiniinta reer Muqdisho helista fasaxyada dhismaha, diiwaangelinta hantida, iyo kala wareejinta abaartada si sharci ah.</p>
                    <div class="d-flex gap-3">
                        <a href="#services" class="btn btn-light btn-lg px-5 fw-bold text-primary shadow">Adeegyada</a>
                        <a href="#contact" class="btn btn-outline-light btn-lg px-5">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section id="services" class="container py-5 mt-4">
            <div class="text-center mb-5">
                <h2 class="section-heading">Adeegyada aan Bixino</h2>
                <p class="text-muted mx-auto" style="max-width: 600px;">
                    Ku dhufo adeegga aad u baahan tahay si aad u bilowdo habraaca diiwaangelinta ama codsiga.
                </p>
            </div>

            <div class="row g-4 justify-content-center">
                <div class="col-lg-4 col-md-6">
                    <a href="/services/project-registration" class="text-decoration-none h-100 d-block">
                        <div class="card h-100 service-card border-0 shadow-sm p-4">
                            <div class="icon-box bg-primary text-white shadow-sm">
                                <i class="bi bi-file-earmark-medical"></i>
                            </div>
                            <span class="badge bg-light text-primary mb-2 w-25">Step 1</span>
                            <h5 class="fw-bold text-dark">Diiwaangelinta Mashruuca</h5>
                            <p class="text-muted small mb-0">Bilow adiga oo diiwaangelinaya mashruuca si adeegyadu ugu xirmaan.</p>
                        </div>
                    </a>
                </div>

                <div class="col-lg-4 col-md-6">
                    <a href="/services/developer-registration" class="text-decoration-none h-100 d-block">
                        <div class="card h-100 service-card border-0 shadow-sm p-4">
                            <div class="icon-box bg-mu-blue text-white shadow-sm" style="background-color: var(--mu-blue) !important;">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <span class="badge bg-light text-primary mb-2 w-25">Step 2</span>
                            <h5 class="fw-bold text-dark">Diiwaangelinta Shirkadaha</h5>
                            <p class="text-muted small mb-0">Diiwaangelinta shirkadaha iyo dhismeyaasha wadaagga ah ee dhismaha fuliya.</p>
                        </div>
                    </a>
                </div>

                <div class="col-lg-4 col-md-6">
                    <a href="/services/business-license" class="text-decoration-none h-100 d-block">
                        <div class="card h-100 service-card border-0 shadow-sm p-4">
                            <div class="icon-box bg-info text-white shadow-sm">
                                <i class="bi bi-card-checklist"></i>
                            </div>
                            <span class="badge bg-light text-primary mb-2 w-25">Step 3</span>
                            <h5 class="fw-bold text-dark">Ruqsadda Ganacsiga</h5>
                            <p class="text-muted small mb-0">Bixinta ruqsadda rasmiga ah ee looga ganacsado dhismooyinka wadaagga ah.</p>
                        </div>
                    </a>
                </div>
~

                <div class="col-lg-4 col-md-6">
                    <a href="/services/ownership-certificate" class="text-decoration-none h-100 d-block">
                        <div class="card h-100 service-card border-0 shadow-sm p-4">
                            <div class="icon-box bg-primary text-white shadow-sm">
                                <i class="bi bi-building-check"></i>
                            </div>
                            <span class="badge bg-light text-primary mb-2 w-25">Step 5</span>
                            <h5 class="fw-bold text-dark">Lahaanshaha Abaartada</h5>
                            <p class="text-muted small mb-0">Soo saarista waraaqaha caddaynta lahaanshaha cutubyada guryaha (Units).</p>
                        </div>
                    </a>
                </div>

                <div class="col-lg-4 col-md-6">
                    <a href="/services/ownership-transfer" class="text-decoration-none h-100 d-block">
                        <div class="card h-100 service-card border-0 shadow-sm p-4">
                            <div class="icon-box bg-dark text-white shadow-sm">
                                <i class="bi bi-arrow-left-right"></i>
                            </div>
                            <span class="badge bg-light text-primary mb-2 w-25">Step 6</span>
                            <h5 class="fw-bold text-dark">Kala Wareejinta</h5>
                            <p class="text-muted small mb-0">Habraaca rasmiga ah ee hantida looga iibinayo ama loogu wareejinayo qof kale.</p>
                        </div>
                    </a>
                </div>

                <div class="col-lg-4 col-md-6">
                    <a href="/services/building-management" class="text-decoration-none h-100 d-block">
                        <div class="card h-100 service-card border-0 shadow-sm p-4">
                            <div class="icon-box bg-secondary text-white shadow-sm">
                                <i class="bi bi-gear-wide-connected"></i>
                            </div>
                            <span class="badge bg-light text-primary mb-2 w-25">Step 7</span>
                            <h5 class="fw-bold text-dark">Maamulida Dhismaha</h5>
                            <p class="text-muted small mb-0">Adeegyada la xiriira maamulida iyo dabagalka dhismooyinka wadaagga ah.</p>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <section id="contact" class="py-5 bg-light">
            <div class="container">
                <div class="row g-0 shadow-lg rounded-4 overflow-hidden border">
                    <div class="col-lg-5">
                        <div class="contact-card-info">
                            <h3 class="fw-bold mb-4">Macluumaadka Xafiiska</h3>
                            <p class="opacity-75 mb-5">Xafiiska Waaxda Dhismo Wadaagga wuxuu u furan yahay dadweynaha inta lagu jiro saacadaha shaqada ee rasmiga ah.</p>

                            <div class="d-flex mb-4">
                                <div class="fs-3 me-3"><i class="bi bi-geo-alt-fill text-info"></i></div>
                                <div>
                                    <h6 class="fw-bold mb-0">Location</h6>
                                    <span class="small opacity-75">Hamarweyne, Municipal Building, Floor 2</span>
                                </div>
                            </div>

                            <div class="d-flex mb-4">
                                <div class="fs-3 me-3"><i class="bi bi-clock-fill text-info"></i></div>
                                <div>
                                    <h6 class="fw-bold mb-0">Office Hours</h6>
                                    <span class="small opacity-75">Sat — Thu: 8:00 AM - 2:00 PM</span>
                                </div>
                            </div>

                            <div class="d-flex mb-5">
                                <div class="fs-3 me-3"><i class="bi bi-shield-lock-fill text-info"></i></div>
                                <div>
                                    <h6 class="fw-bold mb-0">Official Email</h6>
                                    <span class="small opacity-75">info.property@xamar.so</span>
                                </div>
                            </div>

                            <div class="mt-auto border-top pt-4 border-secondary">
                                <span class="small d-block opacity-50 mb-2">Social Connect</span>
                                <div class="d-flex gap-3">
                                    <a href="#" class="text-white fs-5"><i class="bi bi-facebook"></i></a>
                                    <a href="#" class="text-white fs-5"><i class="bi bi-twitter-x"></i></a>
                                    <a href="#" class="text-white fs-5"><i class="bi bi-linkedin"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7 bg-white p-5">
                        <h3 class="fw-bold text-dark mb-4">Codsi ama Weydiin</h3>
                        <form action="#">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase">Magacaaga oo Buuxa</label>
                                    <input type="text" class="form-control" placeholder="Tusaale: Mohamed Ali" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase">Telefoonka</label>
                                    <input type="tel" class="form-control" placeholder="061XXXXXXX" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-uppercase">Nooca Adeegga</label>
                                    <select class="form-select">
                                        <option selected disabled>Dooro adeegga aad rabto...</option>
                                        <option>Codsiga Fasaxa Dhismaha</option>
                                        <option>Warqadda Lahaanshaha</option>
                                        <option>Kala Wareejin Hanti</option>
                                        <option>Cabasho / Mid kale</option>
                                    </select>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm mt-3">Dir Codsiga <i class="bi bi-arrow-right ms-2"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="py-4 bg-white border-top">
        <div class="container text-center">
            <p class="mb-0 text-muted fw-bold">© {{ date('Y') }} Dowladda Hoose ee Xamar – Integrated Property & Apartment Management System</p>
        </div>
    </footer>

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
