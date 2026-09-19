<?php

use App\Http\Controllers\Api\Member\MemberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->name('v1.')->group(function () {
    Route::get('/members', [MemberController::class, 'memberList'])->name('members.list');
});
