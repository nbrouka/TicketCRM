<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;

// Welcome route
Route::get('/', function () {
    return view('welcome');
});

// Dashboard route - protected
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['web', 'auth'])
    ->name('dashboard');

// Authentication views
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Authentication routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);
