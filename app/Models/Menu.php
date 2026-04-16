<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
        use HasFactory;

    protected $guarded = []; // Allows us to save data easily

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    // Keep the old relationship for backward compatibility (if needed)
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
