<?php

use App\Http\Controllers\Api\Member\MemberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->name('v1.')->group(function () {
    Route::post('/members', [MemberController::class, 'create'])->name('members.create');
    Route::get('/members', [MemberController::class, 'memberList'])->name('members.list');
    Route::get('/members/options', [MemberController::class, 'dropDown'])->name('members.dropDown');
    Route::patch('/members/{memberCode}', [MemberController::class, 'update'])
        ->where('memberCode', '[A-Za-z0-9-]+')->name('members.update');
});
