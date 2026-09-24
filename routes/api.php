<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Category\CategoryController;
use App\Http\Controllers\Api\Employee\EmployeeController;
use App\Http\Controllers\Api\Employee\PermissionController;
use App\Http\Controllers\Api\Member\MemberController;
use App\Http\Controllers\Api\Product\ProductController;
use App\Http\Controllers\Api\Store\StoreController;
use App\Http\Controllers\Api\Supplier\SupplierController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('v1.')->group(function () {
    // Authentication
    Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:login')
        ->name('auth.login');

    // Employee
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/employees', [EmployeeController::class, 'create'])->middleware('permission:employee.create')
            ->name('employees.create');
        Route::patch('/employees/{employeeCode}', [EmployeeController::class, 'update'])->middleware('permission:employee.edit')
            ->where('employeeCode', '[A-Za-z0-9-]+')->name('employees.update');
        Route::get('/employees', [EmployeeController::class, 'employeeList'])->middleware('permission:employee.view')
            ->name('employees.list');
        Route::put('/employees/{employeeCode}/permissions', [PermissionController::class, 'update'])
            ->middleware('permission:employee.manage-permission')
            ->where('employeeCode', '[A-Za-z0-9-]+')->name('employees.permissions.update');
        Route::get('/employees/{employeeCode}/permissions', [PermissionController::class, 'index'])
            ->middleware('permission:employee.manage-permission')
            ->where('employeeCode', '[A-Za-z0-9-]+')->name('employees.permissions.list');
    });

    // Member
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/members', [MemberController::class, 'create'])
            ->middleware('permission:member.create')->name('members.create');
        Route::get('/members', [MemberController::class, 'memberList'])
            ->middleware('permission:member.view')->name('members.list');
        Route::get('/members/options', [MemberController::class, 'options'])
            ->middleware('permission:member.view')->name('members.dropDown');
        Route::patch('/members/{memberCode}', [MemberController::class, 'update'])
            ->middleware('permission:member.edit')
            ->where('memberCode', '[A-Za-z0-9-]+')->name('members.update');
    });

    // Store
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/stores/options', [StoreController::class, 'options'])
            ->middleware('permission:store.view')->name('stores.options');
        Route::get('/stores', [StoreController::class, 'storeList'])
            ->middleware('permission:store.view')->name('stores.list');
        Route::post('/stores', [StoreController::class, 'create'])
            ->middleware('permission:store.create')->name('stores.create');
        Route::patch('/stores/{storeCode}', [StoreController::class, 'update'])
            ->middleware('permission:store.edit')
            ->where('storeCode', '[A-Za-z0-9-]+')->name('stores.update');
    });

    // Supplier
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/suppliers/options', [SupplierController::class, 'options'])
            ->middleware('permission:supplier.view')->name('suppliers.options');
        Route::get('/suppliers', [SupplierController::class, 'supplierList'])
            ->middleware('permission:supplier.view')->name('suppliers.list');
        Route::post('/suppliers', [SupplierController::class, 'create'])
            ->middleware('permission:supplier.create')->name('suppliers.create');
        Route::patch('/suppliers/{supplierCode}', [SupplierController::class, 'update'])
            ->middleware('permission:supplier.edit')
            ->where('supplierCode', '[A-Za-z0-9-]+')->name('suppliers.update');
    });

    // Category
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/categories', [CategoryController::class, 'categoryList'])
            ->middleware('permission:product.view')->name('categories.list');
        Route::post('/categories', [CategoryController::class, 'create'])
            ->middleware('permission:product.manage')->name('categories.create');
        Route::patch('/categories/{categoryCode}', [CategoryController::class, 'update'])
            ->middleware('permission:product.manage')
            ->where('categoryCode', '[A-Za-z0-9-]+')->name('categories.update');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/categories/options', [CategoryController::class, 'options'])
            ->middleware('permission:sale.create')->name('sale.options');
    });

    // Product
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/products', [ProductController::class, 'productList'])
            ->middleware('permission:product.view')->name('products.list');
        Route::get('/products/{sku}', [ProductController::class, 'detail'])
            ->middleware('permission:product.view')->where('sku', '[A-Za-z0-9-]+')
            ->name('products.detail');
        Route::post('/products', [ProductController::class, 'create'])
            ->middleware('permission:product.manage')->name('products.create');
        Route::patch('/products/{sku}', [ProductController::class, 'update'])
            ->where('sku', '[A-Za-z0-9-]+')->name('products.update');
    });
});
