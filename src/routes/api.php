<?php

use App\Http\Controllers\Api\TicketController;
use Illuminate\Http\Request;
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
    ], 401);
});

// Sanctum authenticated routes for tickets
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('tickets', TicketController::class)->only(['store']);
    Route::patch('/tickets/{ticket}/status', [TicketController::class, 'updateStatus']);
});
