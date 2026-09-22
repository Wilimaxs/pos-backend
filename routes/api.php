<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Employee\EmployeeController;
use App\Http\Controllers\Api\Member\MemberController;
use App\Http\Controllers\Api\Store\StoreController;
use App\Http\Controllers\Api\Supplier\SupplierController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('v1.')->group(function () {
    // Authentication
    Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:login')
        ->name('auth.login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/employees', [EmployeeController::class, 'create'])->middleware('permission:employee.create')
            ->name('employees.create');
    });

    // Member
    Route::post('/members', [MemberController::class, 'create'])->name('members.create');
    Route::get('/members', [MemberController::class, 'memberList'])->name('members.list');
    Route::get('/members/options', [MemberController::class, 'options'])->name('members.dropDown');
    Route::patch('/members/{memberCode}', [MemberController::class, 'update'])
        ->where('memberCode', '[A-Za-z0-9-]+')->name('members.update');

    // Store
    Route::get('/stores/options', [StoreController::class, 'options'])->name('stores.options');
    Route::get('/stores', [StoreController::class, 'storeList'])->name('stores.list');
    Route::post('/stores', [StoreController::class, 'create'])->name('stores.create');
    Route::patch('/stores/{storeCode}', [StoreController::class, 'update'])
        ->where('storeCode', '[A-Za-z0-9-]+')->name('stores.update');

    // Supplier
    Route::get('/suppliers/options', [SupplierController::class, 'options'])->name('suppliers.options');
    Route::get('/suppliers', [SupplierController::class, 'supplierList'])->name('suppliers.list');
    Route::post('/suppliers', [SupplierController::class, 'create'])->name('suppliers.create');
    Route::patch('/suppliers/{supplierCode}', [SupplierController::class, 'update'])
        ->where('supplierCode', '[A-Za-z0-9-]+')->name('suppliers.update');
});
