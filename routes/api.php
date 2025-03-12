<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', function (Request $request) {
    $token = $request->user()->createToken('token-name');   
    return ['token' => $token->plainTextToken];
});
Route::post('/logout', [AuthController::class, 'logout']);
