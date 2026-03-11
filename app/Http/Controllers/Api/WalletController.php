<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_id = Auth::user()->id;

        if (! $user_id) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }
        $wallets = Wallet::where('user_id', $user_id)->get();

        return response()->json(['status' => 'success', 'wallets' => $wallets]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user_id = Auth::user()->id;
        if (! $user_id) {
            return response()->json(['status' => 'error', 'message' => 'You need to be logged in order to create a wallet'], 401);
        }
        $walletdata = $request->validate(['name' => 'required', 'currency' => 'required']);
        $wallet = Wallet::create([
            'name' => $walletdata['name'],
            'currency' => $walletdata['currency'],
            'user_id' => $user_id,
        ]);

        return response()->json(['status' => 'success', 'message' => 'wallet was created successfuly', 'data' => $wallet]);
    }

    public function show(Wallet $wallet)
    {
        return response()->json(['status' => 'success', 'data' => $wallet]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {}

    public function deposit(Wallet $wallet, Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);
        $user_id = Auth::user()->id;
        if ($request->user()->id !== $wallet->user_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'This wallet does not belong to you.',
            ], 403);
        }
        $wallet->increment('balance', $validated['amount']);

        return response()->json([
            'status' => 'success',
            'message' => "{$validated['amount']} was deposited in your wallet.",
            'new_balance' => $wallet->balance,
        ]);
    }
}
