<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'amount',
        'type',
        'description',
    ];

    // Link back to the Sender
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Link back to the Receiver
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}