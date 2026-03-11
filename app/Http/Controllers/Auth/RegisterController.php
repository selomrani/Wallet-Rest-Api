<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
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
    }
}
