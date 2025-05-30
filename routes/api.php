<?php

use App\Http\Controllers\GroupeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ExpenseController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/tags', [TagController::class, 'store']);
    Route::get('/tags', [TagController::class, 'index']);
    Route::get('/tags/{id}', [TagController::class, 'show']);
    Route::put('/tags/{id}', [TagController::class, 'update']);
    Route::delete('/tags/{id}', [TagController::class, 'destroy']);
    Route::post('/expenses/{id}/tags', [ExpenseController::class, 'attachTags']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/expenses', [ExpenseController::class, 'store']);
    Route::get('/expenses', [ExpenseController::class, 'index']);
    Route::get('/expenses/{id}', [ExpenseController::class, 'show']);
    Route::put('/expenses/{id}', [ExpenseController::class, 'update']);
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/groups', [GroupeController::class, 'store']);
    Route::get('/groups', [GroupeController::class, 'index']);
    Route::get('/groups/{id}', [GroupeController::class, 'show']);
    Route::delete('/groups/{id}', [GroupeController::class, 'destroy']);

    Route::post('/groups/{id}/expenses', [GroupeController::class, 'addExpense']);
    Route::get('/groups/{id}/expenses', [GroupeController::class, 'getExpenses']);
    Route::delete('/groups/{id}/expenses/{expenseId}', [GroupeController::class, 'detachExpense']);
});