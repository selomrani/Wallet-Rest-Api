<?php

namespace App\Services;

use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function transfer(int $senderId, int $recipientId, $amount)
    {
        $sender = Wallet::findOrFail($senderId);
        $receiver = Wallet::findOrFail($recipientId);

        return DB::transaction(function () use ($sender, $receiver, $amount) {
            if ($sender->balance < $amount) {
                return response()->json(['status' => 'error', 'message' => 'insufficient balance']);
            }
            $sender->decrement('balance', $amount);
            $receiver->increment('balance', $amount);

            return $sender->fresh();
        });
    }
}
