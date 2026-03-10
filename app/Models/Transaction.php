<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['wallet_id', 'type', 'amount', 'related_wallet_id', 'description'];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
