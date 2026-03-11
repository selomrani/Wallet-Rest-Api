<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/register', function (Request $request) {
    $userdata = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => ['required'],
    ]);
    $user = User::create([
        'name' => $userdata['name'],
        'email' => $userdata['email'],
        'password' => Hash::make($userdata['password']),
    ]);
    $token = $user->createToken('api-token');

    return response()->json([
        'user' => $user,
        'token' => $token->plainTextToken,
    ], 201);
});
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);
    if (! Auth::attempt($credentials)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $user = User::where('email', $request->email)->first();
    $token = $user->createToken('login-token');

    return response()->json([
        'token' => $token->plainTextToken,
    ]);
});
