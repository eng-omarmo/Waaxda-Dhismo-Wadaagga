{{--
Usage:
<x-public-navbar
  brand-subtitle="Dowladda Hoose ee Xamar"
  brand-title="Waaxda Dhismo Wadaagga"
  services-url="{{ url('/#services') }}"
  process-url="{{ url('/#department-info') }}"
  login-url="{{ url('/login') }}"
  :sticky="true"
/>

Props:
- brand-subtitle: string subtitle under the logo
- brand-title: string title under the logo
- services-url: URL for “Services” link
- process-url: URL for “Process/Department Info” link
- login-url: URL for login button (set to null to hide)
- sticky: boolean to toggle sticky-top behavior

This component renders the public site navigation header used across guest pages.
It encapsulates header-specific markup and minimal styling to avoid duplication.
--}}
@props([
  'brandSubtitle' => 'Dowladda Hoose ee Xamar',
  'brandTitle' => 'Waaxda Dhismo Wadaagga',
  'servicesUrl' => url('/#services'),
  'processUrl' => url('/#department-info'),
  'loginUrl' => url('/login'),
  'sticky' => true,
])
<style>
  .top-brand-bar {
    height: 4px;
    background: linear-gradient(to right, #41adff 33%, #fff 33%, #fff 66%, #1e7e34 66%);
  }
</style>
<div class="top-brand-bar"></div>
<nav class="navbar navbar-expand-lg navbar-light bg-white {{ $sticky ? 'sticky-top' : '' }}">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ url('/') }}">
      <img src="{{ asset('assets/images/logo/logo.png') }}" alt="Logo" height="50" class="me-3">
      <div class="lh-1 border-start ps-3">
        <span class="fs-6 d-block text-muted fw-bold text-uppercase" style="font-size: 0.7rem !important;">{{ $brandSubtitle }}</span>
        <span class="fs-5 text-dark fw-extrabold">{{ $brandTitle }}</span>
      </div>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNavbar" aria-controls="publicNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="publicNavbar">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item"><a class="nav-link px-3 fw-bold" href="{{ $servicesUrl }}">Adeegyada</a></li>
        <li class="nav-item"><a class="nav-link px-3 fw-bold" href="{{ $processUrl }}">Habraaca</a></li>
        @if($loginUrl)
          <li class="nav-item ms-lg-3"><a class="btn btn-primary fw-bold" href="{{ $loginUrl }}">Gali System-ka</a></li>
        @endif
      </ul>
    </div>
  </div>
</nav>
