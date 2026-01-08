<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectRegistrationController;
use App\Http\Controllers\ServiceTrackingController;
use App\Http\Controllers\OrganizationRegistrationController;
use App\Http\Controllers\BusinessLicenseController;

Route::get('/', function () {
    return view('landing');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

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
Route::get('/services/developer-registration', [OrganizationRegistrationController::class, 'show'])->name('services.developer-registration');
Route::post('/services/developer-registration', [OrganizationRegistrationController::class, 'store'])->middleware('throttle:10,1')->name('services.developer-registration.store');
Route::get('/services/business-license', [BusinessLicenseController::class, 'show'])->name('services.business-license');
Route::post('/services/business-license', [BusinessLicenseController::class, 'store'])->middleware('throttle:15,1')->name('services.business-license.store');
Route::view('/services/ownership-certificate', 'services.ownership-certificate')->name('services.ownership-certificate');
Route::view('/services/ownership-transfer', 'services.ownership-transfer')->name('services.ownership-transfer');
Route::get('/track', [ServiceTrackingController::class, 'show'])->name('track.show');
Route::post('/track', [ServiceTrackingController::class, 'lookup'])->middleware('throttle:15,1')->name('track.lookup');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

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
    Route::get('/organizations', [\App\Http\Controllers\AdminOrganizationController::class, 'index'])->name('organizations.index');
    Route::get('/organizations/{organization}', [\App\Http\Controllers\AdminOrganizationController::class, 'show'])->name('organizations.show');
    Route::post('/organizations', [\App\Http\Controllers\AdminOrganizationController::class, 'store'])->name('organizations.store');
    Route::put('/organizations/{organization}', [\App\Http\Controllers\AdminOrganizationController::class, 'update'])->name('organizations.update');
    Route::post('/organizations/{organization}/approve', [\App\Http\Controllers\AdminOrganizationController::class, 'approve'])->name('organizations.approve');
    Route::post('/organizations/{organization}/reject', [\App\Http\Controllers\AdminOrganizationController::class, 'reject'])->name('organizations.reject');
    Route::get('/organizations/{organization}/documents/{document}', [\App\Http\Controllers\AdminOrganizationController::class, 'downloadDoc'])->name('organizations.documents.download');
    Route::view('/permits', 'admin.pages.permits')->name('permits');
    Route::view('/buildings', 'admin.pages.buildings')->name('buildings');
    Route::get('/licensing', [\App\Http\Controllers\AdminBusinessLicenseController::class, 'index'])->name('licensing.index');
    Route::post('/licensing/{license}/approve', [\App\Http\Controllers\AdminBusinessLicenseController::class, 'approve'])->name('licensing.approve');
    Route::post('/licensing/{license}/reject', [\App\Http\Controllers\AdminBusinessLicenseController::class, 'reject'])->name('licensing.reject');
    Route::put('/licensing/{license}', [\App\Http\Controllers\AdminBusinessLicenseController::class, 'update'])->name('licensing.update');
    Route::get('/licensing/{license}/documents/{docId}', [\App\Http\Controllers\AdminBusinessLicenseController::class, 'downloadDoc'])->name('licensing.documents.download');
    Route::view('/ownership', 'admin.pages.ownership')->name('ownership');
    Route::view('/transfers', 'admin.pages.transfers')->name('transfers');
    Route::view('/inspections', 'admin.pages.inspections')->name('inspections');
    Route::view('/audit', 'admin.pages.audit')->name('audit');
    Route::view('/roles', 'admin.pages.roles')->name('roles');
    Route::view('/reports', 'admin.pages.reports')->name('reports');
});
