<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
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
    }
}
