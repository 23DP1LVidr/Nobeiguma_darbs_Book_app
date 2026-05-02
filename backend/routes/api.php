<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\ExchangeController;
use App\Http\Controllers\Api\ConversationController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::get('/users/{user}', [AuthController::class, 'showUser']);

    Route::get('/books', [BookController::class, 'index']);
    Route::post('/books', [BookController::class, 'store']);
    Route::put('/books/{book}', [BookController::class, 'update']);
    Route::delete('/books/{book}', [BookController::class, 'destroy']);
    Route::get('/books/available', [BookController::class, 'available']);

    Route::get('/exchanges', [ExchangeController::class, 'index']);
    Route::post('/exchanges', [ExchangeController::class, 'store']);
    Route::put('/exchanges/{exchange}/accept', [ExchangeController::class, 'accept']);
    Route::put('/exchanges/{exchange}/reject', [ExchangeController::class, 'reject']);
    Route::put('/exchanges/{exchange}/complete', [ExchangeController::class, 'complete']);
    Route::delete('/exchanges/{exchange}', [ExchangeController::class, 'destroy']);

    Route::get('/conversations', [ConversationController::class, 'index']);
    Route::get('/conversations/user/{user}', [ConversationController::class, 'showOrCreate']);
    Route::get('/conversations/{conversation}/messages', [ConversationController::class, 'messages']);
    Route::post('/conversations/{conversation}/messages', [ConversationController::class, 'sendMessage']);
});