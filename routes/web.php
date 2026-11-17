<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FamilyProfileController;
use App\Http\Controllers\DonationController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

// ---------- Auth ----------
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// ---------- R2: Family Vulnerability Tracking (Affected Citizen) ----------
Route::middleware(['auth', 'role:citizen'])
    ->prefix('profile/family')
    ->name('family.')
    ->group(function () {
        Route::get('/', [FamilyProfileController::class, 'show'])->name('show');
        Route::post('/', [FamilyProfileController::class, 'save'])->name('save');
        Route::delete('/', [FamilyProfileController::class, 'destroy'])->name('destroy');
    });

// ---------- (Teammates: add your feature routes in your own section below) ----------
// ---------- R6: Supply Donation Logistics ----------
Route::middleware('auth')->group(function () {
    Route::get('/donations', [DonationController::class, 'index'])
        ->name('donations.index');

    Route::get('/donations/create', [DonationController::class, 'create'])
        ->name('donations.create');

    Route::post('/donations', [DonationController::class, 'store'])
        ->name('donations.store');

    Route::get('/donations/{donation}/receipt', [DonationController::class, 'receipt'])
        ->name('donations.receipt');
});
