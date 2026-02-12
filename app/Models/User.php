<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable; // ✅ SPATIE REMOVED to prevent conflict

    protected $fillable = [
        'name',
        'email',
        'password',
        'wallet_balance',
        'daily_allocation',
        'staff_number',
        'department',
        'department_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // This tells Laravel to use 'staff_number' for finding users if needed
    public function username()
    {
        return 'staff_number';
    }

    // ✅ FIXED: Defines the relationship to Orders
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // ✅ CRITICAL: This is the logic that lets you in as Admin
public function hasRole($role)
    {
        // 1. Grant Access to Admin
        if ($role === 'admin') {
            // Checks for your specific email OR User ID 1 (The first user created)
            return $this->email === 'admin@kcau.ac.ke' || $this->id === 1; 
        }

        // 2. Grant Access to Staff
        if ($role === 'staff') {
            return !empty($this->staff_number);
        }

        return false;
    }
    // A user belongs to a department
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}