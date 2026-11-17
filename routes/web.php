<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WarehouseController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/warehouse', [WarehouseController::class, 'index'])
    ->name('warehouse.index');

Route::get('/warehouse/create', [WarehouseController::class, 'create'])
    ->name('warehouse.create');

Route::post('/warehouse', [WarehouseController::class, 'store'])
    ->name('warehouse.store');    
Route::get('/inventory/create', [WarehouseController::class, 'createInventory'])
    ->name('inventory.create');

Route::post('/inventory', [WarehouseController::class, 'storeInventory'])
    ->name('inventory.store');    

Route::get('/incoming-assets/create', [WarehouseController::class, 'createIncomingAsset'])
    ->name('incoming-asset.create');

Route::post('/incoming-assets', [WarehouseController::class, 'storeIncomingAsset'])
    ->name('incoming-asset.store');    
Route::get('/lockers/create', [WarehouseController::class, 'createLocker'])
    ->name('locker.create');

Route::post('/lockers', [WarehouseController::class, 'storeLocker'])
    ->name('locker.store');    

Route::get('/locker-assignments/create', [WarehouseController::class, 'createLockerAssignment'])
    ->name('locker-assignment.create');

Route::post('/locker-assignments', [WarehouseController::class, 'storeLockerAssignment'])
    ->name('locker-assignment.store');    