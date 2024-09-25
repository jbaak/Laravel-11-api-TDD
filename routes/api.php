<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\UpdatePasswordController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'login']);
Route::post('/users', [RegisterController::class, 'store']);
Route::put('/profile', [ProfileController::class, 'update']);
Route::put('/password', [UpdatePasswordController::class, 'update']);
Route::post('/reset-password', [ResetPasswordController::class, 'send']);
Route::put('/reset-password', [ResetPasswordController::class, 'resetPassword']);
