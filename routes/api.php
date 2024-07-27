<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])->name('register')->middleware('throttle:3,1');

Route::post('/login', [AuthController::class, 'login'])->name('login')->middleware('throttle:3,1');

Route::group(['middleware' => ['auth:sanctum', 'throttle:3,1']], function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
