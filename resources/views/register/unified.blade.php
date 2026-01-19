<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Registration – IPAMS</title>
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
    body { 
      font-family: 'Nunito', sans-serif; 
      color: var(--mu-dark); 
      background-color: var(--mu-grey-bg);
    }
    .hero { 
      background: linear-gradient(135deg, var(--mu-blue-dark) 0%, var(--mu-blue) 100%); 
      color: #fff; 
      padding: 48px 0;
    }
    .section-heading { 
      font-weight: 800; 
      color: var(--mu-blue-dark); 
      border-bottom: 2px solid #e9ecef;
      padding-bottom: 12px;
      margin-bottom: 24px;
    }
    .form-section {
      background: white;
      border-radius: 8px;
      padding: 24px;
      margin-bottom: 24px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .form-label {
      font-weight: 600;
      color: #495057;
      margin-bottom: 8px;
    }
    .form-label .required {
      color: #dc3545;
    }
    .form-label .help-icon {
      color: var(--mu-blue);
      cursor: help;
      margin-left: 4px;
    }
    .form-control:focus, .form-select:focus {
      border-color: var(--mu-blue);
      box-shadow: 0 0 0 0.2rem rgba(0, 45, 128, 0.25);
    }
    .form-text-help {
      font-size: 0.875rem;
      color: #6c757d;
      margin-top: 4px;
    }
    .form-error {
      color: #dc3545;
      font-size: 0.875rem;
      margin-top: 4px;
      display: none;
    }
    .form-error.show {
      display: block;
    }
    .field-group {
      margin-bottom: 20px;
    }
    .progress-indicator {
      background: #e9ecef;
      border-radius: 10px;
      height: 8px;
      margin-bottom: 24px;
      overflow: hidden;
    }
    .progress-indicator-bar {
      background: linear-gradient(90deg, var(--mu-blue) 0%, var(--mu-green) 100%);
      height: 100%;
      transition: width 0.3s ease;
      border-radius: 10px;
    }
    .progress-text {
      text-align: center;
      color: #6c757d;
      font-size: 0.875rem;
      margin-bottom: 8px;
    }
    .save-indicator {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background: var(--mu-green);
      color: white;
      padding: 12px 20px;
      border-radius: 6px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      display: none;
      z-index: 1000;
      animation: slideIn 0.3s ease;
    }
    .save-indicator.show {
      display: block;
    }
    @keyframes slideIn {
      from {
        transform: translateX(100%);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }
    .review-section {
      display: none;
    }
    .review-section.active {
      display: block;
    }
    .review-item {
      padding: 12px;
      border-bottom: 1px solid #e9ecef;
    }
    .review-item:last-child {
      border-bottom: none;
    }
    .review-label {
      font-weight: 600;
      color: #495057;
      margin-bottom: 4px;
    }
    .review-value {
      color: #6c757d;
    }
    .btn-group-actions {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      margin-top: 24px;
    }
    .btn {
      font-weight: 600;
      padding: 12px 24px;
    }
    .payment-details {
      background: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 8px;
      padding: 20px;
      margin-top: 20px;
    }
    .payment-amount {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--mu-blue);
    }
    .card-details-section {
      display: none;
      margin-top: 20px;
      padding: 20px;
      background: #f8f9fa;
      border-radius: 8px;
    }
    .card-details-section.show {
      display: block;
    }
    .loading-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.5);
      z-index: 9999;
      align-items: center;
      justify-content: center;
    }
    .loading-overlay.show {
      display: flex;
    }
    .loading-spinner {
      background: white;
      padding: 30px;
      border-radius: 8px;
      text-align: center;
    }
    @media (max-width: 768px) {
      .hero {
        padding: 32px 0;
      }
      .form-section {
        padding: 16px;
      }
      .btn-group-actions {
        flex-direction: column;
      }
      .btn-group-actions .btn {
        width: 100%;
      }
    }
    .form-control:invalid, .form-select:invalid {
      border-color: #dc3545;
    }
    .form-control:valid:not(:placeholder-shown), .form-select:valid {
      border-color: #28a745;
    }
    [data-bs-toggle="tooltip"] {
      cursor: help;
    }
    .btn:focus, .form-control:focus, .form-select:focus {
      outline: 2px solid var(--mu-blue);
      outline-offset: 2px;
    }
  </style>
  @stack('page-styles')
</head>
<body>
  <x-public-navbar />

  <header class="hero">
    <div class="container">
      <h1 class="fw-extrabold mb-2">User Registration</h1>
      <p class="mb-0">Create your account and complete registration with secure payment. All fields marked with <span class="required">*</span> are required.</p>
    </div>
  </header>

  <main class="py-5">
    <div class="container">
      <div class="row">
        <div class="col-lg-10 mx-auto">
          <!-- Progress Indicator -->
          <div class="progress-text">
            <span id="progressText">0% Complete</span>
          </div>
          <div class="progress-indicator">
            <div class="progress-indicator-bar" id="progressBar" style="width: 0%"></div>
          </div>

          @if ($errors->any())
            <div class="alert alert-danger" role="alert">
              <h6 class="alert-heading fw-bold mb-2">
                <i class="bi bi-exclamation-triangle me-2"></i>Please correct the following errors:
              </h6>
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <!-- Form Section -->
          <div id="formSection" class="form-section">
            <form id="registrationForm" method="POST" action="{{ route('register.complete') }}" novalidate>
              @csrf

              <!-- Section 1: Personal Information -->
              <h5 class="section-heading">
                <i class="bi bi-person-circle me-2"></i>Personal Information
              </h5>

              <div class="row g-3">
                <div class="col-md-12 field-group">
                  <label for="full_name" class="form-label">
                    Full Name <span class="required">*</span>
                    <i class="bi bi-info-circle help-icon" 
                       data-bs-toggle="tooltip" 
                       data-bs-placement="top"
                       title="Enter your full legal name as it appears on official documents"></i>
                  </label>
                  <input 
                    type="text" 
                    class="form-control" 
                    id="full_name" 
                    name="full_name" 
                    placeholder="e.g., Mohamed Ali Hassan"
                    value="{{ old('full_name', '') }}"
                    required
                    aria-describedby="full_name_help full_name_error"
                    autocomplete="name">
                  <div class="form-text-help" id="full_name_help">
                    Use your legal name for official records
                  </div>
                  <div class="form-error" id="full_name_error" role="alert"></div>
                </div>

                <div class="col-md-6 field-group">
                  <label for="email" class="form-label">
                    Email Address <span class="required">*</span>
                    <i class="bi bi-info-circle help-icon" 
                       data-bs-toggle="tooltip" 
                       data-bs-placement="top"
                       title="This will be your login email and used for account notifications"></i>
                  </label>
                  <input 
                    type="email" 
                    class="form-control" 
                    id="email" 
                    name="email" 
                    placeholder="you@example.com"
                    value="{{ old('email', '') }}"
                    required
                    aria-describedby="email_help email_error"
                    autocomplete="email">
                  <div class="form-text-help" id="email_help">
                    Enter a valid email address for your account
                  </div>
                  <div class="form-error" id="email_error" role="alert"></div>
                </div>

                <div class="col-md-6 field-group">
                  <label for="phone" class="form-label">
                    Phone Number
                    <i class="bi bi-info-circle help-icon" 
                       data-bs-toggle="tooltip" 
                       data-bs-placement="top"
                       title="Optional: Provide phone number for account recovery"></i>
                  </label>
                  <input 
                    type="tel" 
                    class="form-control" 
                    id="phone" 
                    name="phone" 
                    placeholder="0612345678"
                    value="{{ old('phone', '') }}"
                    pattern="[0-9]{9,12}"
                    aria-describedby="phone_help phone_error"
                    autocomplete="tel">
                  <div class="form-text-help" id="phone_help">
                    Optional: Format 9-12 digits without spaces or dashes
                  </div>
                  <div class="form-error" id="phone_error" role="alert"></div>
                </div>
              </div>

              <!-- Section 2: Payment Information -->
              <h5 class="section-heading mt-4">
                <i class="bi bi-credit-card me-2"></i>Payment Information
              </h5>

              <div class="payment-details">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <span class="fw-bold">Registration Fee:</span>
                  <span class="payment-amount">${{ number_format($amount, 2) }}</span>
                </div>
                <p class="small text-muted mb-0">This one-time fee activates your account and provides full system access.</p>
              </div>

              <div class="field-group">
                <label for="payment_method" class="form-label">
                  Payment Method <span class="required">*</span>
                  <i class="bi bi-info-circle help-icon" 
                     data-bs-toggle="tooltip" 
                     data-bs-placement="top"
                     title="Select your preferred payment method"></i>
                </label>
                <select 
                  class="form-select" 
                  id="payment_method" 
                  name="payment_method" 
                  required
                  aria-describedby="payment_method_help payment_method_error">
                  <option value="">Select payment method...</option>
                  <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Credit/Debit Card</option>
                  <option value="paypal" {{ old('payment_method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                </select>
                <div class="form-text-help" id="payment_method_help">
                  Choose your preferred payment method
                </div>
                <div class="form-error" id="payment_method_error" role="alert"></div>
              </div>

              <!-- Card Details Section (shown when card is selected) -->
              <div id="cardDetailsSection" class="card-details-section">
                <h6 class="mb-3">
                  <i class="bi bi-shield-lock me-2"></i>Card Details
                </h6>
                <div class="row g-3">
                  <div class="col-md-12 field-group">
                    <label for="card_name" class="form-label">
                      Cardholder Name <span class="required">*</span>
                    </label>
                    <input 
                      type="text" 
                      class="form-control" 
                      id="card_name" 
                      name="card_name" 
                      placeholder="Name as it appears on card"
                      value="{{ old('card_name', '') }}"
                      aria-describedby="card_name_help card_name_error"
                      autocomplete="cc-name">
                    <div class="form-text-help" id="card_name_help">
                      Enter the name exactly as shown on your card
                    </div>
                    <div class="form-error" id="card_name_error" role="alert"></div>
                  </div>

                  <div class="col-md-12 field-group">
                    <label for="card_number" class="form-label">
                      Card Number <span class="required">*</span>
                    </label>
                    <input 
                      type="text" 
                      class="form-control" 
                      id="card_number" 
                      name="card_number" 
                      placeholder="•••• •••• •••• ••••"
                      value="{{ old('card_number', '') }}"
                      minlength="12"
                      maxlength="19"
                      pattern="[0-9\s]{12,19}"
                      aria-describedby="card_number_help card_number_error"
                      autocomplete="cc-number">
                    <div class="form-text-help" id="card_number_help">
                      Enter 12-19 digit card number
                    </div>
                    <div class="form-error" id="card_number_error" role="alert"></div>
                  </div>

                  <div class="col-md-6 field-group">
                    <label for="card_expiry" class="form-label">
                      Expiry Date <span class="required">*</span>
                    </label>
                    <input 
                      type="text" 
                      class="form-control" 
                      id="card_expiry" 
                      name="card_expiry" 
                      placeholder="MM/YY"
                      value="{{ old('card_expiry', '') }}"
                      pattern="^(0[1-9]|1[0-2])\/\d{2}$"
                      maxlength="5"
                      aria-describedby="card_expiry_help card_expiry_error"
                      autocomplete="cc-exp">
                    <div class="form-text-help" id="card_expiry_help">
                      Format: MM/YY (e.g., 12/25)
                    </div>
                    <div class="form-error" id="card_expiry_error" role="alert"></div>
                  </div>

                  <div class="col-md-6 field-group">
                    <label for="card_cvc" class="form-label">
                      CVC <span class="required">*</span>
                    </label>
                    <input 
                      type="text" 
                      class="form-control" 
                      id="card_cvc" 
                      name="card_cvc" 
                      placeholder="•••"
                      value="{{ old('card_cvc', '') }}"
                      minlength="3"
                      maxlength="4"
                      pattern="[0-9]{3,4}"
                      aria-describedby="card_cvc_help card_cvc_error"
                      autocomplete="cc-csc">
                    <div class="form-text-help" id="card_cvc_help">
                      3 or 4 digit security code
                    </div>
                    <div class="form-error" id="card_cvc_error" role="alert"></div>
                  </div>
                </div>
                <div class="alert alert-info mt-3 mb-0">
                  <i class="bi bi-shield-check me-2"></i>We do not store card numbers. Payment is processed securely.
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="btn-group-actions">
                <button type="button" class="btn btn-outline-primary" id="reviewBtn">
                  <i class="bi bi-eye me-2"></i>Review Before Submit
                </button>
                <button type="submit" class="btn btn-success" id="submitBtn">
                  <i class="bi bi-check-circle me-2"></i>Complete Registration & Pay
                </button>
              </div>
            </form>
          </div>

          <!-- Review Section -->
          <div id="reviewSection" class="review-section form-section">
            <h5 class="section-heading">
              <i class="bi bi-check-circle me-2"></i>Review Your Registration
            </h5>
            <p class="text-muted mb-4">Please review all information before submitting. Click "Edit" to make changes.</p>
            
            <div id="reviewContent"></div>

            <div class="btn-group-actions">
              <button type="button" class="btn btn-outline-secondary" id="editBtn">
                <i class="bi bi-pencil me-2"></i>Edit Information
              </button>
              <button type="button" class="btn btn-success" id="confirmSubmitBtn">
                <i class="bi bi-check-circle me-2"></i>Confirm & Complete Registration
              </button>
            </div>
          </div>

          <!-- Help Card -->
          <div class="card mt-4">
            <div class="card-body">
              <h6 class="card-title">
                <i class="bi bi-question-circle me-2"></i>About Registration
              </h6>
              <p class="text-muted mb-2">The registration fee is a one-time payment that activates your account and provides full access to the municipal property management system.</p>
              <hr>
              <ul class="small text-muted ps-3 mb-0">
                <li>Your account will be created immediately after payment</li>
                <li>You'll receive login credentials via email</li>
                <li>All payments are processed securely</li>
                <li>Card details are never stored on our servers</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Loading Overlay -->
  <div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Processing...</span>
      </div>
      <p class="mt-3 mb-0">Processing payment and creating your account...</p>
      <small class="text-muted">Please do not close this window</small>
    </div>
  </div>

  <!-- Auto-save Indicator -->
  <div class="save-indicator" id="saveIndicator" role="status" aria-live="polite">
    <i class="bi bi-check-circle me-2"></i><span>Draft saved automatically</span>
  </div>

  <footer class="py-4 bg-white border-top mt-5">
    <div class="container text-center">
      <p class="mb-0 text-muted fw-bold">© {{ date('Y') }} Dowladda Hoose ee Xamar – Integrated Property & Apartment Management System</p>
    </div>
  </footer>

  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <script>
    // Configuration
    const STORAGE_KEY = 'registration_draft';
    const form = document.getElementById('registrationForm');
    const formFields = ['full_name', 'email', 'phone'];
    const cardFields = ['card_name', 'card_number', 'card_expiry', 'card_cvc'];
    const paymentMethod = document.getElementById('payment_method');
    const cardDetailsSection = document.getElementById('cardDetailsSection');
    const amount = {{ $amount ?? env('REGISTRATION_FEE', 25.00) }};

    // Show/hide card details based on payment method
    function toggleCardDetails() {
      if (paymentMethod.value === 'card') {
        cardDetailsSection.classList.add('show');
        // Make card fields required
        cardFields.forEach(field => {
          const input = document.getElementById(field);
          if (input) {
            input.setAttribute('required', 'required');
          }
        });
      } else {
        cardDetailsSection.classList.remove('show');
        // Remove required from card fields
        cardFields.forEach(field => {
          const input = document.getElementById(field);
          if (input) {
            input.removeAttribute('required');
          }
        });
      }
      updateProgress();
    }

    paymentMethod.addEventListener('change', toggleCardDetails);

    // Load saved draft
    function loadDraft() {
      const saved = localStorage.getItem(STORAGE_KEY);
      if (saved) {
        try {
          const data = JSON.parse(saved);
          [...formFields, ...cardFields, 'payment_method'].forEach(field => {
            const input = document.getElementById(field);
            if (input && data[field]) {
              input.value = data[field];
            }
          });
          toggleCardDetails();
          updateProgress();
        } catch (e) {
          console.error('Error loading draft:', e);
        }
      }
    }

    // Save draft
    function saveDraft() {
      const data = {};
      [...formFields, ...cardFields, 'payment_method'].forEach(field => {
        const input = document.getElementById(field);
        if (input) {
          data[field] = input.value;
        }
      });
      localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
      showSaveIndicator();
      updateProgress();
    }

    // Show save indicator
    function showSaveIndicator() {
      const indicator = document.getElementById('saveIndicator');
      indicator.classList.add('show');
      setTimeout(() => {
        indicator.classList.remove('show');
      }, 2000);
    }

    // Auto-save on input (debounced)
    let saveTimeout;
    [...formFields, ...cardFields].forEach(field => {
      const input = document.getElementById(field);
      if (input) {
        input.addEventListener('input', () => {
          clearTimeout(saveTimeout);
          saveTimeout = setTimeout(saveDraft, 1000);
          validateField(input);
          updateProgress();
        });
        input.addEventListener('blur', () => {
          validateField(input);
        });
      }
    });

    paymentMethod.addEventListener('change', () => {
      saveDraft();
      validateField(paymentMethod);
      updateProgress();
    });

    // Format card number input
    const cardNumber = document.getElementById('card_number');
    if (cardNumber) {
      cardNumber.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\s+/g, '');
        let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
        if (formattedValue.length <= 19) {
          e.target.value = formattedValue;
        }
      });
    }

    // Format expiry date input
    const cardExpiry = document.getElementById('card_expiry');
    if (cardExpiry) {
      cardExpiry.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 2) {
          value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        e.target.value = value;
      });
    }

    // Real-time validation
    function validateField(input) {
      const errorDiv = document.getElementById(input.id + '_error');
      const isValid = input.checkValidity();
      
      if (isValid) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        if (errorDiv) {
          errorDiv.classList.remove('show');
          errorDiv.textContent = '';
        }
      } else {
        input.classList.remove('is-valid');
        input.classList.add('is-invalid');
        if (errorDiv) {
          errorDiv.classList.add('show');
          let message = input.validationMessage;
          if (input.id === 'card_expiry' && !message) {
            message = 'Expiry must be MM/YY format';
          }
          errorDiv.textContent = message || 'This field is required';
        }
      }
      return isValid;
    }

    // Validate form
    function validateForm() {
      let isValid = true;
      [...formFields].forEach(field => {
        const input = document.getElementById(field);
        if (input && !validateField(input)) {
          isValid = false;
        }
      });
      
      // Validate payment method
      if (!validateField(paymentMethod)) {
        isValid = false;
      }
      
      // Validate card fields if card is selected
      if (paymentMethod.value === 'card') {
        cardFields.forEach(field => {
          const input = document.getElementById(field);
          if (input && !validateField(input)) {
            isValid = false;
          }
        });
      }
      
      return isValid;
    }

    // Update progress
    function updateProgress() {
      let filled = 0;
      const total = formFields.length + 1; // +1 for payment method
      
      formFields.forEach(field => {
        const input = document.getElementById(field);
        if (input && input.value.trim()) {
          filled++;
        }
      });
      
      if (paymentMethod.value) {
        filled++;
      }
      
      const percentage = (filled / total) * 100;
      document.getElementById('progressBar').style.width = percentage + '%';
      document.getElementById('progressText').textContent = Math.round(percentage) + '% Complete';
    }

    // Review functionality
    document.getElementById('reviewBtn').addEventListener('click', function(e) {
      e.preventDefault();
      if (validateForm()) {
        const reviewContent = document.getElementById('reviewContent');
        reviewContent.innerHTML = '';
        
        const sections = [
          { title: 'Personal Information', fields: formFields },
          { title: 'Payment Information', fields: ['payment_method'] }
        ];
        
        sections.forEach(section => {
          const sectionDiv = document.createElement('div');
          sectionDiv.className = 'mb-4';
          sectionDiv.innerHTML = `<h6 class="fw-bold mb-3">${section.title}</h6>`;
          
          section.fields.forEach(field => {
            const input = document.getElementById(field);
            if (input) {
              const label = input.previousElementSibling?.textContent || field;
              const value = input.value || 'Not provided';
              const reviewItem = document.createElement('div');
              reviewItem.className = 'review-item';
              reviewItem.innerHTML = `
                <div class="review-label">${label.replace('*', '').trim()}</div>
                <div class="review-value">${value}</div>
              `;
              sectionDiv.appendChild(reviewItem);
            }
          });
          
          reviewContent.appendChild(sectionDiv);
        });
        
        // Add payment amount
        const paymentReview = document.createElement('div');
        paymentReview.className = 'review-item';
        paymentReview.innerHTML = `
          <div class="review-label">Registration Fee</div>
          <div class="review-value fw-bold">$${amount.toFixed(2)}</div>
        `;
        reviewContent.appendChild(paymentReview);
        
        // If card payment, mask card details in review
        if (paymentMethod.value === 'card') {
          const cardNumberInput = document.getElementById('card_number');
          const cardExpiryInput = document.getElementById('card_expiry');
          if (cardNumberInput && cardNumberInput.value) {
            const masked = '•••• •••• •••• ' + cardNumberInput.value.slice(-4);
            const cardReview = document.createElement('div');
            cardReview.className = 'review-item';
            cardReview.innerHTML = `
              <div class="review-label">Card Number</div>
              <div class="review-value">${masked}</div>
            `;
            reviewContent.appendChild(cardReview);
          }
        }
        
        document.getElementById('formSection').style.display = 'none';
        document.getElementById('reviewSection').classList.add('active');
      } else {
        alert('Please fill in all required fields correctly before reviewing.');
      }
    });

    document.getElementById('editBtn').addEventListener('click', function() {
      document.getElementById('reviewSection').classList.remove('active');
      document.getElementById('formSection').style.display = 'block';
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    document.getElementById('confirmSubmitBtn').addEventListener('click', function(e) {
      e.preventDefault();
      if (validateForm()) {
        document.getElementById('loadingOverlay').classList.add('show');
        form.submit();
      }
    });

    // Form submission - show loading and clear draft
    form.addEventListener('submit', function(e) {
      if (!validateForm()) {
        e.preventDefault();
        return false;
      }
      
      document.getElementById('loadingOverlay').classList.add('show');
      localStorage.removeItem(STORAGE_KEY);
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
      });
      
      loadDraft();
      toggleCardDetails();
      updateProgress();
      window.addEventListener('beforeunload', saveDraft);
    });

    window.addEventListener('pageshow', function(event) {
      if (event.persisted) {
        loadDraft();
        toggleCardDetails();
        updateProgress();
      }
    });
  </script>
  @stack('page-scripts')
</body>
</html>
