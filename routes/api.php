<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::get('/user', function () {
    return response()->json(['status' => 'success', 'data' => Auth::user()]);
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
        'status' => 'success',
        'message' => 'inscription réussie',
        'data' => $user,
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
    $user->tokens()->delete();
    $token = $user->createToken('login-token');

    return response()->json([
        'status' => 'success',
        'message' => 'connexion succés',
        'data' => $user,
        'token' => $token->plainTextToken,
    ]);
});
Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->tokens()->delete();

    return response()->json([
        'status' => 'success',
        'message' => 'Logged out successfully.',
    ]);
});
