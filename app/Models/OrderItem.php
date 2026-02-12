<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    // ✅ UNLOCKS THE ITEM COLUMNS
    protected $fillable = [
        'order_id', 
        'menu_id', 
        'menu_name', 
        'quantity', 
        'price'
    ];
}