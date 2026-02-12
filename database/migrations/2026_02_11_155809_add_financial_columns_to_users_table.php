<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        // For Students & Staff Identification
        $table->string('identifier')->nullable()->unique()->after('email'); // Admission No or Staff ID
        
        // For Students (M-Pesa Top-ups)
        $table->decimal('wallet_balance', 10, 2)->default(0.00)->after('identifier');
        
        // For Staff (Daily Allowance)
        $table->decimal('daily_allocation', 10, 2)->default(0.00)->after('wallet_balance');
        
        // Track if they have used their allocation today
        $table->boolean('allocation_used_today')->default(false)->after('daily_allocation');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
