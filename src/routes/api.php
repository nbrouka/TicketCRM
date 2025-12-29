<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TicketController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Enjoy building your API!
|
*/

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Sanctum authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Ticket routes
    Route::post('/tickets', [TicketController::class, 'storeForAuthenticated']);
    Route::get('/tickets', [TicketController::class, 'index']);
    Route::get('/tickets/statistics', [TicketController::class, 'statistics']);
    Route::get('/tickets/statistics-by-month', [TicketController::class, 'statisticsByMonth']);
    Route::get('/tickets/{ticket}', [TicketController::class, 'show']);
    Route::patch('/tickets/{ticket}/status', [TicketController::class, 'updateStatus']);
});

// Public route for feedback form (creates a ticket)
Route::post('/feedback', [TicketController::class, 'store']);

// Test route to verify Sanctum is working (can be removed later)
Route::middleware('auth:sanctum')->get('/sanctum/user', function (Request $request) {
    return $request->user();
});

// Route to test token creation (for testing purposes - can be removed later)
Route::post('/sanctum/token', function (Request $request) {
    $user = $request->user();
    if ($user) {
        return response()->json([
            'message' => 'Authenticated user accessed',
            'user' => $user,
        ]);
    }

    return response()->json([
        'message' => 'Not authenticated',
    ], Response::HTTP_UNAUTHORIZED);
});
