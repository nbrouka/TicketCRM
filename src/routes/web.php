<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

// Welcome route
Route::get('/', [HomeController::class, 'welcome']);

// Dashboard route - protected
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['web', 'auth'])
    ->name('dashboard');

// Authentication views
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::get('/register', [AuthController::class, 'showRegister'])
    ->name('register');

// Authentication routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);

// Ticket routes - protected
Route::get('/tickets', [TicketController::class, 'index'])
    ->middleware(['web', 'auth'])
    ->name('tickets.index');

Route::get('/tickets/{ticket}', [TicketController::class, 'show'])
    ->middleware(['web', 'auth'])
    ->name('tickets.show');

Route::get('/tickets/{ticket}/files/{mediaId}', [TicketController::class, 'downloadFile'])
    ->middleware(['web', 'auth'])
    ->name('tickets.download');

Route::put('/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])
    ->middleware(['web', 'auth'])
    ->name('tickets.updateStatus');

// Feedback widget routes
Route::get('/feedback-widget', [FeedbackController::class, 'show'])
    ->middleware(['web'])
    ->name('feedback.widget');
Route::get('/feedback-demo', [FeedbackController::class, 'demo'])
    ->middleware(['web'])
    ->name('feedback.demo');
