<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UpdatePasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'login']);
Route::post('/users', [RegisterController::class, 'store']);
Route::put('/profile', [ProfileController::class, 'update']);
Route::put('/password', [UpdatePasswordController::class, 'update']);
