<?php

use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\TicketController;
use App\Http\Controllers\V1\CommentController;
use App\Http\Controllers\V1\TicketLeaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::middleware('throttle:5,1')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::apiResource('tickets', TicketController::class);
        Route::apiResource('tickets.comments', CommentController::class)->only(['index','store']);
        Route::put('/tickets/{ticket}/lease', [TicketLeaseController::class, 'lease'])->name('tickets.lease');
        Route::put('/tickets/{ticket}/unlease', [TicketLeaseController::class, 'unlease'])->name('tickets.unlease');
    });
});