<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\SeekerController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public job board
Route::get('/', [JobController::class, 'index'])->name('home');
Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');

// Guest auth
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Authenticated
Route::middleware('auth')->group(function () {
    // Role-aware dashboard dispatcher
    Route::get('/dashboard', function () {
        $user = Auth::user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return $user->isProvider()
            ? redirect()->route('provider.dashboard')
            : redirect()->route('seeker.dashboard');
    })->name('dashboard');

    // Admin backend
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('users.role');
        Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
        Route::get('/jobs', [AdminController::class, 'jobs'])->name('jobs');
        Route::post('/jobs/{job}/toggle', [AdminController::class, 'toggleJob'])->name('jobs.toggle');
        Route::delete('/jobs/{job}', [AdminController::class, 'deleteJob'])->name('jobs.delete');
        Route::get('/applications', [AdminController::class, 'applications'])->name('applications');
    });

    // Seeker
    Route::get('/seeker', [SeekerController::class, 'dashboard'])->name('seeker.dashboard');
    Route::post('/seeker/resume', [SeekerController::class, 'uploadResume'])->name('seeker.resume.upload');
    Route::post('/jobs/{job}/apply', [SeekerController::class, 'apply'])->name('jobs.apply');
    Route::get('/my-applications', [SeekerController::class, 'applications'])->name('seeker.applications');

    // Provider
    Route::get('/provider', [ProviderController::class, 'dashboard'])->name('provider.dashboard');
    Route::get('/provider/jobs/create', [ProviderController::class, 'create'])->name('provider.jobs.create');
    Route::post('/provider/jobs', [ProviderController::class, 'store'])->name('provider.jobs.store');
    Route::post('/provider/jobs/{job}/toggle', [ProviderController::class, 'toggle'])->name('provider.jobs.toggle');
    Route::get('/provider/jobs/{job}/applicants', [ProviderController::class, 'applicants'])->name('provider.jobs.applicants');
    Route::post('/applications/{application}/status', [ProviderController::class, 'updateApplication'])->name('provider.applications.status');

    // Resume file (access-controlled)
    Route::get('/resumes/{resume}', [ResumeController::class, 'show'])->name('resumes.show');
});
