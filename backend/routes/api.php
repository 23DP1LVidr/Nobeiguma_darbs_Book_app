<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\ExchangeController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\PostController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/posts', [PostController::class, 'index']);
Route::get('/books/available', [BookController::class, 'available']);
Route::get('/users/{user}', [AuthController::class, 'showUser']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/password', [AuthController::class, 'updatePassword']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::post('/posts/{post}/like', [PostController::class, 'toggleLike']);
    Route::post('/posts/{post}/comments', [PostController::class, 'comment']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);
    Route::delete('/posts/{post}/comments/{comment}', [PostController::class, 'destroyComment']);

    Route::get('/books', [BookController::class, 'index']);
    Route::post('/books', [BookController::class, 'store']);
    Route::put('/books/{book}', [BookController::class, 'update']);
    Route::delete('/books/{book}', [BookController::class, 'destroy']);
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

Route::prefix('admin')
    ->middleware(['auth:sanctum', 'admin'])
    ->group(function () {
        Route::get('/users', [AdminController::class, 'indexUsers']);
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
        Route::get('/books', [AdminController::class, 'indexBooks']);
        Route::delete('/books/{id}', [AdminController::class, 'deleteBook']);
        Route::get('/exchanges', [AdminController::class, 'indexExchanges']);
    });
