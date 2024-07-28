<?php

use App\Enums\TokenAbility;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])->name('register')->middleware('throttle:3,1');

Route::post('/login', [AuthController::class, 'login'])->name('login')->middleware('throttle:3,1');

Route::group(['middleware' => ['auth:sanctum', 'throttle:3,1']], function () {

    // Route for issue access token ability only
    Route::group(['middleware' => 'ability:'.TokenAbility::ISSUE_ACCESS_TOKEN->value], function () {
        Route::post('/refresh-token', [AuthController::class, 'refreshToken'])->name('refresh-token');
    });

    // Route for access api ability only
    Route::group(['middleware' => 'ability:'.TokenAbility::ACCESS_API->value], function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });
});
