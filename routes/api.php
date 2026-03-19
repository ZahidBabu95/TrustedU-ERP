<?php

use App\Http\Controllers\Api\SupportTicketController;
use App\Http\Controllers\Api\SystemSettingsController;
use App\Http\Controllers\Api\TeamController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public team listing
Route::get('/team', [TeamController::class, 'index']);
Route::get('/team/{user}', [TeamController::class, 'show']);

// Public system settings (non-sensitive only)
Route::get('/settings', [SystemSettingsController::class, 'index']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::put('/profile', [TeamController::class, 'updateProfile']);
    Route::put('/settings', [SystemSettingsController::class, 'update']);
    Route::get('/settings/audit', [SystemSettingsController::class, 'auditLog']);

    // Support Tickets
    Route::get('/tickets', [SupportTicketController::class, 'index']);
    Route::post('/tickets', [SupportTicketController::class, 'store']);
    Route::get('/tickets/{id}', [SupportTicketController::class, 'show']);
    Route::post('/tickets/{id}/reply', [SupportTicketController::class, 'reply']);
});
