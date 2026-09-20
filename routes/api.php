<?php

use App\Http\Controllers\Api\Member\MemberController;
use App\Http\Controllers\Api\Store\StoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('v1.')->group(function () {
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
});
