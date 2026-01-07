<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\ProfileController;

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

Route::view('/services/project-registration', 'services.project-registration')->name('services.project-registration');
Route::view('/services/developer-registration', 'services.developer-registration')->name('services.developer-registration');

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
    Route::view('/projects', 'admin.pages.projects')->name('projects');
    Route::view('/permits', 'admin.pages.permits')->name('permits');
    Route::view('/buildings', 'admin.pages.buildings')->name('buildings');
    Route::view('/licensing', 'admin.pages.licensing')->name('licensing');
    Route::view('/ownership', 'admin.pages.ownership')->name('ownership');
    Route::view('/transfers', 'admin.pages.transfers')->name('transfers');
    Route::view('/inspections', 'admin.pages.inspections')->name('inspections');
    Route::view('/audit', 'admin.pages.audit')->name('audit');
    Route::view('/roles', 'admin.pages.roles')->name('roles');
    Route::view('/reports', 'admin.pages.reports')->name('reports');
});
