<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
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
        $transactions = Transaction::where('wallet_id', $wallet->id)->get();

        return response()->json(['status' => 'success', 'wallet' => $wallet, 'historique' => $transactions]);
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
        $transaction = Transaction::create(['wallet_id' => $wallet->id, 'type' => 'deposit', 'amount' => $validated['amount'], 'description' => "{$validated['amount']} was deposited in your wallet."]);

        return response()->json([
            'status' => 'success',
            'transaction' => $transaction,
            'new_balance' => $wallet->balance,
        ]);
    }

    public function withdraw(Wallet $wallet, Request $request)
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
        if ($validated['amount'] > $wallet->balance) {
            return response()->json([
                'status' => 'error',
                'message' => 'nsuffusiant balance',
            ], 403);
        }
        $wallet->decrement('balance', $validated['amount']);
        $transaction = Transaction::create(['wallet_id' => $wallet->id, 'type' => 'withdrawj', 'amount' => $validated['amount'], 'description' => "{$validated['amount']} was withdrawed from your wallet."]);

        return response()->json([
            'status' => 'success',
            'transaction' => $transaction,
            'new_balance' => $wallet->balance,
        ]);
    }

    public function viewTransactions(Wallet $wallet)
    {
        $transactions = Transaction::where('wallet_id', $wallet->id)->get();

        return response()->json(['status' => 'success', 'Historique des transactions' => $transactions]);
    }
}
