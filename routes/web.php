<?php

use App\Http\Controllers\Admin\AdminApplicationController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\DocumentTypeController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentApplicationController;
use App\Http\Controllers\StudentBookmarkController;
use App\Http\Controllers\StudentDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Pages
|--------------------------------------------------------------------------
*/

Route::view('/', 'landing')->name('landing');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');

    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Student Area
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function (): void {
    Route::get('/home', [StudentDashboardController::class, 'home'])->name('student.home');
    Route::get('/profile', [StudentDashboardController::class, 'profile'])->name('student.profile');
    Route::get('/profile/edit', [StudentDashboardController::class, 'editProfile'])->name('student.profile.edit');
    Route::put('/profile', [StudentDashboardController::class, 'updateProfile'])->name('student.profile.update');

    Route::get('/information', [StudentDashboardController::class, 'information'])->name('student.information');
    Route::get('/bookmarks', [StudentBookmarkController::class, 'index'])->name('student.bookmarks.index');
    Route::post('/bookmarks/{documentType}', [StudentBookmarkController::class, 'store'])->name('student.bookmarks.store');
    Route::delete('/bookmarks/{documentType}', [StudentBookmarkController::class, 'destroy'])->name('student.bookmarks.destroy');
    Route::get('/analytics', [StudentDashboardController::class, 'analytics'])->name('student.analytics');

    Route::get('/applications', [StudentApplicationController::class, 'index'])->name('student.applications.index');
    Route::get('/applications/create', [StudentApplicationController::class, 'create'])->name('student.applications.create');
    Route::post('/applications', [StudentApplicationController::class, 'store'])->name('student.applications.store');
    Route::get('/applications/{studentApplication}', [StudentApplicationController::class, 'show'])->name('student.applications.show');
    Route::patch('/applications/{studentApplication}/documents/{applicationDocument}/revision', [StudentApplicationController::class, 'reviseDocument'])
        ->name('student.applications.documents.revise');
});

/*
|--------------------------------------------------------------------------
| Admin Area
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function (): void {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/applications', [AdminApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/{studentApplication}', [AdminApplicationController::class, 'show'])->name('applications.show');
        Route::patch('/applications/{studentApplication}/status', [AdminApplicationController::class, 'updateStatus'])->name('applications.update-status');
        Route::patch('/applications/{studentApplication}/documents/{applicationDocument}', [AdminApplicationController::class, 'updateDocument'])
            ->name('applications.documents.update');

        Route::get('/document-types', [DocumentTypeController::class, 'index'])->name('document-types.index');
        Route::post('/document-types', [DocumentTypeController::class, 'store'])->name('document-types.store');
        Route::put('/document-types/{documentType}', [DocumentTypeController::class, 'update'])->name('document-types.update');
        Route::delete('/document-types/{documentType}', [DocumentTypeController::class, 'destroy'])->name('document-types.destroy');

        Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    });
