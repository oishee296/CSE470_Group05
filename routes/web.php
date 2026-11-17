<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FamilyProfileController;
use App\Http\Controllers\WarehouseController;
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
// ---------- F11: Warehouse Hub Management ----------
Route::middleware(['auth', 'role:logistics_manager'])->group(function () {

    // Warehouse
    Route::get('/warehouse', [WarehouseController::class, 'index'])->name('warehouse.index');
    Route::get('/warehouse/create', [WarehouseController::class, 'create'])->name('warehouse.create');
    Route::post('/warehouse', [WarehouseController::class, 'store'])->name('warehouse.store');

    // Inventory
    Route::get('/inventory/create', [WarehouseController::class, 'createInventory'])->name('inventory.create');
    Route::post('/inventory', [WarehouseController::class, 'storeInventory'])->name('inventory.store');

    // Incoming Assets
    Route::get('/incoming-assets/create', [WarehouseController::class, 'createIncomingAsset'])->name('incoming-asset.create');
    Route::post('/incoming-assets', [WarehouseController::class, 'storeIncomingAsset'])->name('incoming-asset.store');

    // Storage Lockers
    Route::get('/lockers/create', [WarehouseController::class, 'createLocker'])->name('locker.create');
    Route::post('/lockers', [WarehouseController::class, 'storeLocker'])->name('locker.store');

    // Locker Assignments
    Route::get('/locker-assignments/create', [WarehouseController::class, 'createLockerAssignment'])->name('locker-assignment.create');
    Route::post('/locker-assignments', [WarehouseController::class, 'storeLockerAssignment'])->name('locker-assignment.store');
});

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
