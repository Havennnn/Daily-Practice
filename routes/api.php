<?php

use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\TicketController;
use App\Http\Controllers\V1\CommentController;
use App\Http\Controllers\V1\TicketLeaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::middleware('throttle:5,1')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        /* Route::get('/tickets', [TicketController::class, 'index']);
        Route::post('/tickets', [TicketController::class, 'store']);
        Route::get('/tickets/{ticket}', [TicketController::class, 'show']);
        Route::put('/tickets/{ticket}', [TicketController::class, 'update']);
        Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy']); */

        Route::apiResource('tickets', TicketController::class);
        Route::put('/tickets/{ticket}/lease', [TicketLeaseController::class, 'lease']);
        Route::post('/tickets/{ticket}/comments', [CommentController::class, 'addComment']);
        Route::get('/tickets/{ticket}/comments', [CommentController::class, 'getComments']);
    });
});
