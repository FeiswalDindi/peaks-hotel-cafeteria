<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 
        'total_amount', 
        'wallet_paid', 
        'mpesa_paid', 
        'phone_number', 
        'status', 
        'mpesa_code'
    ];

    // ✅ THIS IS THE MISSING RELATIONSHIP causing the Dashboard Error
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}