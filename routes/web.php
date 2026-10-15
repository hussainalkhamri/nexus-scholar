<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\DocRequestController;
use App\Http\Controllers\IncentiveController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;

// Public Home & Lang Switcher
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/lang/{locale}', [HomeController::class, 'switchLang'])->name('lang.switch');
Route::get('/verify-document/{code}', [DocRequestController::class, 'verifyDocument'])->name('documents.verify');

// Guest Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    // Route::get('/register', [LoginController::class, 'showRegisterForm'])->name('register');
    // Route::post('/register', [LoginController::class, 'register']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard Home
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Projects (CRUD)
    Route::get('/dashboard/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/dashboard/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/dashboard/projects/{id}', [ProjectController::class, 'show'])->name('projects.show');
    Route::post('/dashboard/projects/{id}/edit', [ProjectController::class, 'update'])->name('projects.update');
    Route::post('/dashboard/projects/{id}/delete', [ProjectController::class, 'destroy'])->name('projects.destroy');
    Route::post('/dashboard/projects/{id}/stage', [ProjectController::class, 'advanceStage'])->name('projects.stage.advance');
    Route::post('/dashboard/projects/{id}/comment', [ProjectController::class, 'storeComment'])->name('projects.comment.store');
    Route::post('/dashboard/projects/{id}/upload', [ProjectController::class, 'uploadFile'])->name('projects.upload');

    // Supervisors Analytics
    Route::get('/dashboard/supervisors', [SupervisorController::class, 'index'])->name('supervisors.index');

    // Alumni Directory
    Route::get('/dashboard/alumni', [AlumniController::class, 'index'])->name('alumni.index');

    // Document Requests
    Route::get('/dashboard/alumni/requests', [DocRequestController::class, 'index'])->name('alumni.requests.index');
    Route::post('/dashboard/alumni/requests', [DocRequestController::class, 'store'])->name('alumni.requests.store');
    Route::post('/dashboard/alumni/requests/{id}/status', [DocRequestController::class, 'updateStatus'])->name('alumni.requests.status');
    Route::get('/dashboard/alumni/requests/{id}/download', [DocRequestController::class, 'downloadDocument'])->name('alumni.requests.download');

    // Incentives (CRUD)
    Route::get('/dashboard/incentives', [IncentiveController::class, 'index'])->name('incentives.index');
    Route::post('/dashboard/incentives', [IncentiveController::class, 'store'])->name('incentives.store');
    Route::post('/dashboard/incentives/{id}/edit', [IncentiveController::class, 'update'])->name('incentives.update');
    Route::post('/dashboard/incentives/{id}/delete', [IncentiveController::class, 'destroy'])->name('incentives.destroy');
    Route::post('/dashboard/incentives/{id}/claim', [IncentiveController::class, 'claim'])->name('incentives.claim');

    // User Profile
    Route::get('/dashboard/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/dashboard/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Users & Roles (CRUD) & Backup - Admin and Super Admin only
    Route::middleware('can:manage-users')->group(function () {
        Route::get('/dashboard/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/dashboard/users', [UserController::class, 'store'])->name('users.store');
        Route::post('/dashboard/users/import', [UserController::class, 'import'])->name('users.import');
        Route::post('/dashboard/users/{id}/update', [UserController::class, 'update'])->name('users.update');
        Route::post('/dashboard/users/{id}/delete', [UserController::class, 'destroy'])->name('users.destroy');

        // Database Backup & Full System Maintenance Control Center
        Route::get('/dashboard/backup', [\App\Http\Controllers\BackupController::class, 'index'])->name('backup.index');
        Route::get('/dashboard/backup/sqlite', [\App\Http\Controllers\BackupController::class, 'downloadDatabase'])->name('backup.sqlite');
        Route::get('/dashboard/backup/sql', [\App\Http\Controllers\BackupController::class, 'exportSqlDump'])->name('backup.sql');
        Route::post('/dashboard/backup/restore', [\App\Http\Controllers\BackupController::class, 'restoreSqlDump'])->name('backup.restore');
        Route::post('/dashboard/backup/clean-cache', [\App\Http\Controllers\BackupController::class, 'cleanCache'])->name('backup.clean');
    });

    // Reports Engine (Access is validated in controller to redirect unauthorized users)
    Route::get('/dashboard/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/dashboard/reports/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');
    Route::get('/dashboard/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');

});
