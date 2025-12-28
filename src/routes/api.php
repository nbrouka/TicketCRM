<?php

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

// Test route to verify Sanctum is working
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route to test token creation (for testing purposes)
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

// Public route for feedback form (creates a ticket)
Route::post('/feedback', [TicketController::class, 'store']);

// Sanctum authenticated routes for other ticket operations
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/tickets', [TicketController::class, 'storeForAuthenticated']);
    Route::get('/tickets', [TicketController::class, 'index']);
    Route::get('/tickets/{ticket}', [TicketController::class, 'show']);
    Route::patch('/tickets/{ticket}/status', [TicketController::class, 'updateStatus']);
});
