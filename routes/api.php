<?php

use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/user', function () {
    return response()->json(['status' => 'success', 'data' => Auth::user()]);
})->middleware('auth:sanctum');
Route::post('/login', LoginController::class);
Route::post('/register', RegisterController::class);
Route::post('/logout', LogoutController::class)->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('wallets', WalletController::class);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::post('wallets/{wallet}/deposit', [WalletController::class, 'deposit']);
});
