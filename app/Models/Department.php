<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];

    // A department has many staff members
// A department has many users (staff)
    public function staff()
    {
        // We removed "->where('role', 'staff')" because the column doesn't exist.
        // Since only staff usually belong to departments, this is safe!
        return $this->hasMany(User::class);
    }
}