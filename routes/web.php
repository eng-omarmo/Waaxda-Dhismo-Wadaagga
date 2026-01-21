<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessLicenseController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\OrganizationRegistrationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectRegistrationController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceTrackingController;
use Illuminate\Support\Facades\Route;
use App\Models\Certificate;

Route::get('/', [LandingPageController::class, 'index'])->name('landing.page.index');
Route::post('/contact', [LandingPageController::class, 'storeContact'])->middleware('throttle:10,1')->name('contact.store');

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/register/start', [\App\Http\Controllers\SelfRegistrationController::class, 'start'])->name('register.start');
Route::post('/register/complete', [\App\Http\Controllers\SelfRegistrationController::class, 'complete'])->name('register.complete');
// Legacy routes for backward compatibility
Route::post('/register/step1', [\App\Http\Controllers\SelfRegistrationController::class, 'storeStep1'])->name('register.step1.store');
Route::get('/register/step2', [\App\Http\Controllers\SelfRegistrationController::class, 'step2'])->name('register.step2');
Route::post('/register/pay', [\App\Http\Controllers\SelfRegistrationController::class, 'processPayment'])->name('register.pay');
Route::get('/register/resume/{token}', [\App\Http\Controllers\SelfRegistrationController::class, 'resume'])->name('register.resume');
Route::get('/receipt/online/{payment}', [\App\Http\Controllers\SelfRegistrationController::class, 'publicReceiptOnline'])->middleware('signed')->name('receipt.online.show');

Route::get('/portal', [\App\Http\Controllers\SelfServiceController::class, 'start'])->name('portal.start');
Route::post('/portal/service', [\App\Http\Controllers\SelfServiceController::class, 'storeService'])->name('portal.service.store');
Route::get('/portal/info', [\App\Http\Controllers\SelfServiceController::class, 'info'])->name('portal.info');
Route::post('/portal/info', [\App\Http\Controllers\SelfServiceController::class, 'storeInfo'])->name('portal.info.store');
Route::get('/portal/details', [\App\Http\Controllers\SelfServiceController::class, 'details'])->name('portal.details');
Route::post('/portal/details', [\App\Http\Controllers\SelfServiceController::class, 'storeDetails'])->name('portal.details.store');
Route::get('/portal/docs', [\App\Http\Controllers\SelfServiceController::class, 'docs'])->name('portal.docs');
Route::post('/portal/docs', [\App\Http\Controllers\SelfServiceController::class, 'storeDocs'])->name('portal.docs.store');
Route::get('/portal/pay', [\App\Http\Controllers\SelfServiceController::class, 'pay'])->name('portal.pay');
Route::post('/portal/pay', [\App\Http\Controllers\SelfServiceController::class, 'processPay'])->name('portal.pay.store');
Route::get('/portal/receipt', [\App\Http\Controllers\SelfServiceController::class, 'receipt'])->name('portal.receipt');
Route::get('/portal/resume/{token}', [\App\Http\Controllers\SelfServiceController::class, 'resume'])->name('portal.resume');
Route::get('/portal/receipt/{payment}', [\App\Http\Controllers\SelfServiceController::class, 'publicReceipt'])->middleware('signed')->name('portal.receipt.public');
Route::match(['GET','POST'], '/portal/callback/success', [\App\Http\Controllers\SelfServiceController::class, 'callbackSuccess'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->name('portal.success');
Route::match(['GET','POST'], '/portal/callback/failure', [\App\Http\Controllers\SelfServiceController::class, 'callbackFailure'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->name('portal.failure');
Route::match(['GET','POST'], '/payment/callback/success', [\App\Http\Controllers\SelfServiceController::class, 'callbackSuccess'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->name('payment.callback.success');
Route::match(['GET','POST'], '/payment/callback/failure', [\App\Http\Controllers\SelfServiceController::class, 'callbackFailure'])->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->name('payment.callback.failure');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Public self-registration disabled. Use admin panel to create users.

Route::get('/email/verify', [AuthController::class, 'verifyNotice'])->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/admin', function () {
    return view('admin');
});

Route::get('/services/project-registration', [ProjectRegistrationController::class, 'show'])->name('services.project-registration');
Route::post('/services/project-registration', [ProjectRegistrationController::class, 'store'])->name('services.project-registration.store');
Route::get('/services/project-registration/thank-you/{id}', [ProjectRegistrationController::class, 'thankyou'])->name('services.project-registration.thankyou');
Route::get('/services/construction-permit', [\App\Http\Controllers\ApartmentConstructionPermitController::class, 'publicShow'])->name('services.construction-permit');
Route::post('/services/construction-permit', [\App\Http\Controllers\ApartmentConstructionPermitController::class, 'publicStore'])->middleware('throttle:15,1')->name('services.construction-permit.store');
Route::get('/services/construction-permit/thank-you/{id}', [\App\Http\Controllers\ApartmentConstructionPermitController::class, 'publicThankyou'])->name('services.construction-permit.thankyou');
Route::get('/services/developer-registration', [OrganizationRegistrationController::class, 'show'])->name('services.developer-registration');
Route::post('/services/developer-registration', [OrganizationRegistrationController::class, 'store'])->middleware('throttle:10,1')->name('services.developer-registration.store');
Route::get('/services/business-license', [BusinessLicenseController::class, 'show'])->name('services.business-license');
Route::post('/services/business-license', [BusinessLicenseController::class, 'store'])->middleware('throttle:15,1')->name('services.business-license.store');
Route::view('/services/ownership-certificate', 'services.ownership-certificate')->name('services.ownership-certificate');
Route::view('/services/ownership-transfer', 'services.ownership-transfer')->name('services.ownership-transfer');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');

Route::get('/track', [ServiceTrackingController::class, 'show'])->name('track.show');
Route::post('/track', [ServiceTrackingController::class, 'lookup'])->middleware('throttle:15,1')->name('track.lookup');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
Route::get('/certificates/verify/{uid}', function (\Illuminate\Http\Request $request, $uid) {
    $sig = (string) $request->query('sig', '');
    $expected = hash_hmac('sha256', (string) $uid, config('app.key'));
    $certificate = Certificate::where('certificate_uid', $uid)->first();
    $valid = (bool) ($certificate && hash_equals($expected, $sig));
    return view('admin.certificates.verify', [
        'certificate' => $certificate,
        'valid' => $valid,
        'uid' => $uid,
    ]);
})->name('certificates.verify');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::get('/projects', [\App\Http\Controllers\AdminProjectController::class, 'index'])->name('projects');
    Route::post('/projects', [\App\Http\Controllers\AdminProjectController::class, 'store'])->name('projects.store');
    Route::post('/projects/{project}/assign-developer', [\App\Http\Controllers\AdminProjectController::class, 'assignDeveloper'])->name('projects.assignDeveloper');
    Route::get('/projects/{project}/edit', [\App\Http\Controllers\AdminProjectController::class, 'edit'])->name('projects.edit');
    Route::delete('/projects/{project}', [\App\Http\Controllers\AdminProjectController::class, 'destroy'])->name('projects.destroy');
    Route::put('/projects/{project}', [\App\Http\Controllers\AdminProjectController::class, 'update'])->name('projects.update');
    Route::get('/projects/create', [\App\Http\Controllers\AdminProjectController::class, 'create'])->name('projects.create');
    Route::get('/organizations', [\App\Http\Controllers\AdminOrganizationController::class, 'index'])->name('organizations.index');
    Route::get('/organizations/{organization}', [\App\Http\Controllers\AdminOrganizationController::class, 'show'])->name('organizations.show');
    Route::post('/organizations', [\App\Http\Controllers\AdminOrganizationController::class, 'store'])->name('organizations.store');
    Route::put('/organizations/{organization}', [\App\Http\Controllers\AdminOrganizationController::class, 'update'])->name('organizations.update');
    Route::post('/organizations/{organization}/approve', [\App\Http\Controllers\AdminOrganizationController::class, 'approve'])->name('organizations.approve');
    Route::post('/organizations/{organization}/reject', [\App\Http\Controllers\AdminOrganizationController::class, 'reject'])->name('organizations.reject');
    Route::get('/organizations/{organization}/documents/{document}', [\App\Http\Controllers\AdminOrganizationController::class, 'downloadDoc'])->name('organizations.documents.download');
    // Construction Permits
    Route::resource('permits', \App\Http\Controllers\ApartmentConstructionPermitController::class)->names('permits');
    Route::get('permits/{permit}/download', [\App\Http\Controllers\ApartmentConstructionPermitController::class, 'downloadDrawing'])->name('permits.download');
    Route::post('permits/{permit}/approve', [\App\Http\Controllers\ApartmentConstructionPermitController::class, 'approve'])->name('permits.approve');
    Route::post('permits/{permit}/reject', [\App\Http\Controllers\ApartmentConstructionPermitController::class, 'reject'])->name('permits.reject');

    // Apartment (Building) Management
    Route::view('/buildings', 'admin.pages.buildings')->name('buildings');
    Route::resource('apartments', \App\Http\Controllers\ApartmentController::class);

    Route::get('/licensing', [\App\Http\Controllers\AdminBusinessLicenseController::class, 'index'])->name('licensing.index');
    Route::get('/licensing/{license}/edit', [\App\Http\Controllers\AdminBusinessLicenseController::class, 'edit'])->name('licensing.edit');
    Route::put('/licensing/{license}/save', [\App\Http\Controllers\AdminBusinessLicenseController::class, 'save'])->name('licensing.save');
    Route::post('/licensing/{license}/approve', [\App\Http\Controllers\AdminBusinessLicenseController::class, 'approve'])->name('licensing.approve');
    Route::post('/licensing/{license}/reject', [\App\Http\Controllers\AdminBusinessLicenseController::class, 'reject'])->name('licensing.reject');
    Route::put('/licensing/{license}', [\App\Http\Controllers\AdminBusinessLicenseController::class, 'update'])->name('licensing.update');
    Route::get('/licensing/{license}/documents/{docId}', [\App\Http\Controllers\AdminBusinessLicenseController::class, 'downloadDoc'])->name('licensing.documents.download');
    // add issue licence
    Route::get('/new-business-license', [\App\Http\Controllers\AdminBusinessLicenseController::class, 'displayIssuePage'])->name('licensing.issue');
    Route::get('/ownership', [\App\Http\Controllers\AdminOwnershipController::class, 'index'])->name('ownership.index');
    Route::post('/ownership', [\App\Http\Controllers\AdminOwnershipController::class, 'store'])->name('ownership.store');
    Route::put('/ownership/{claim}', [\App\Http\Controllers\AdminOwnershipController::class, 'update'])->name('ownership.update');
    Route::post('/ownership/{claim}/approve', [\App\Http\Controllers\AdminOwnershipController::class, 'approve'])->name('ownership.approve');
    Route::post('/ownership/{claim}/reject', [\App\Http\Controllers\AdminOwnershipController::class, 'reject'])->name('ownership.reject');
    Route::get('/ownership/{claim}/documents/{index}', [\App\Http\Controllers\AdminOwnershipController::class, 'viewDoc'])->whereNumber('index')->name('ownership.documents.view');

    // Land Ownership Verification
    Route::get('/land-parcels', [\App\Http\Controllers\LandParcelController::class, 'index'])->name('land-parcels.index');
    Route::get('/land-parcels/create', [\App\Http\Controllers\LandParcelController::class, 'create'])->name('land-parcels.create');
    Route::post('/land-parcels', [\App\Http\Controllers\LandParcelController::class, 'store'])->name('land-parcels.store');
    Route::get('/land-parcels/{landParcel}', [\App\Http\Controllers\LandParcelController::class, 'show'])->name('land-parcels.show');
    Route::post('/land-parcels/{landParcel}/verify', [\App\Http\Controllers\LandParcelController::class, 'verify'])->name('land-parcels.verify');

    Route::get('/land-verifications', [\App\Http\Controllers\LandOwnershipVerificationController::class, 'index'])->name('land-verifications.index');
    Route::get('/land-verifications/create', [\App\Http\Controllers\LandOwnershipVerificationController::class, 'create'])->name('land-verifications.create');
    Route::post('/land-verifications', [\App\Http\Controllers\LandOwnershipVerificationController::class, 'store'])->name('land-verifications.store');
    Route::get('/land-verifications/{verification}', [\App\Http\Controllers\LandOwnershipVerificationController::class, 'show'])->name('land-verifications.show');
    Route::post('/land-verifications/{verification}/process', [\App\Http\Controllers\LandOwnershipVerificationController::class, 'process'])->name('land-verifications.process');

    Route::get('/transfers', [\App\Http\Controllers\ApartmentTransferController::class, 'index'])->name('apartment-transfers.index');
    Route::get('/transfers/create', [\App\Http\Controllers\ApartmentTransferController::class, 'create'])->name('apartment-transfers.create');
    Route::post('/transfers', [\App\Http\Controllers\ApartmentTransferController::class, 'store'])->name('apartment-transfers.store');
    Route::post('/transfers/{transfer}/approve', [\App\Http\Controllers\ApartmentTransferController::class, 'approve'])->name('apartment-transfers.approve');
    Route::post('/transfers/{transfer}/reject', [\App\Http\Controllers\ApartmentTransferController::class, 'reject'])->name('apartment-transfers.reject');
    Route::get('/transfers/{transfer}/deed', [\App\Http\Controllers\ApartmentTransferController::class, 'deed'])->name('apartment-transfers.deed');
    Route::get('/transfers/owner/{apartment}', [\App\Http\Controllers\ApartmentTransferController::class, 'ownerProfile'])->name('apartment-transfers.owner');
    Route::get('/transfers/owners/lookup', [\App\Http\Controllers\ApartmentTransferController::class, 'ownersLookup'])->name('apartment-transfers.owners.lookup');

    Route::view('/inspections', 'admin.pages.inspections')->name('inspections');
    Route::view('/audit', 'admin.pages.audit')->name('audit');
    Route::view('/roles', 'admin.pages.roles')->name('roles');
    Route::view('/reports', 'admin.pages.reports')->name('reports');

    Route::get('/certificates', [\App\Http\Controllers\CertificateController::class, 'index'])->name('certificates.index');
    Route::get('/certificates/create', [\App\Http\Controllers\CertificateController::class, 'create'])->name('certificates.create');
    Route::get('/certificates/{certificate}', [\App\Http\Controllers\CertificateController::class, 'show'])->whereNumber('certificate')->name('certificates.show');
    Route::post('/certificates', [\App\Http\Controllers\CertificateController::class, 'store'])->name('certificates.store');
    Route::post('/certificates/generate-phone', [\App\Http\Controllers\CertificateController::class, 'generateFromPhone'])->name('certificates.generate_phone');
    Route::get('/certificates/templates/{service}', [\App\Http\Controllers\CertificateController::class, 'template'])->name('certificates.template');
    Route::get('/certificates/{certificate}/download', [\App\Http\Controllers\CertificateController::class, 'download'])->name('certificates.download');

    Route::get('/payments/sync', function () {
        $updated = \App\Models\OnlinePayment::where('status', 'initiated')->update([
            'status' => 'completed',
            'verified_at' => now(),
        ]);
        \App\Models\ManualOperationLog::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'action' => 'payments_sync',
            'target_type' => 'OnlinePayment',
            'target_id' => 'bulk',
            'details' => ['updated_count' => $updated],
        ]);

        return redirect()->route('admin.reports')->with('status', "Synchronized {$updated} payment(s)");
    })->name('payments.sync');

    Route::get('/manual-requests', [\App\Http\Controllers\AdminManualRequestController::class, 'index'])->name('manual-requests.index');
    Route::get('/manual-requests/create', [\App\Http\Controllers\AdminManualRequestController::class, 'create'])->name('manual-requests.create');
    Route::post('/manual-requests', [\App\Http\Controllers\AdminManualRequestController::class, 'store'])->name('manual-requests.store');
    Route::get('/manual-requests/{manual_request}', [\App\Http\Controllers\AdminManualRequestController::class, 'show'])->name('manual-requests.show');
    Route::get('/manual-requests/{manual_request}/form', [\App\Http\Controllers\AdminManualRequestController::class, 'form'])->name('manual-requests.form');
    Route::post('/manual-requests/{manual_request}/form', [\App\Http\Controllers\AdminManualRequestController::class, 'submitForm'])->name('manual-requests.form.submit');
    Route::post('/manual-requests/{manual_request}/verify-payment', [\App\Http\Controllers\AdminManualRequestController::class, 'verifyPayment'])->name('manual-requests.verify');
    Route::post('/manual-requests/{manual_request}/payments/{payment}/reconcile', [\App\Http\Controllers\AdminManualRequestController::class, 'reconcile'])->name('manual-requests.reconcile');
    Route::post('/manual-requests/{manual_request}/reject', [\App\Http\Controllers\AdminManualRequestController::class, 'reject'])->name('manual-requests.reject');
    Route::get('/manual-requests/{manual_request}/payments/{payment}/receipt', [\App\Http\Controllers\AdminManualRequestController::class, 'receipt'])->name('manual-requests.receipt');
    Route::post('/manual-requests/{manual_request}/generate-certificate', [\App\Http\Controllers\AdminManualRequestController::class, 'generateCertificate'])->name('manual-requests.generateCertificate');
    Route::get('/contacts', [\App\Http\Controllers\AdminContactController::class, 'index'])->name('contacts.index');
});

Route::get('/receipt/{payment}', [\App\Http\Controllers\AdminManualRequestController::class, 'publicReceipt'])->middleware('signed')->name('receipt.show');

Route::get('/certificate/{certificate}', [\App\Http\Controllers\CertificateController::class, 'publicShow'])->middleware('signed')->name('certificate.public');
