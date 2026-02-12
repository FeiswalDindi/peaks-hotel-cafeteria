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
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained(); // Nullable for visitors/parents
        $table->string('customer_name')->nullable(); // For visitors without accounts
        
        // FINANCIALS
        $table->decimal('total_amount', 10, 2);   // Total Cost (e.g. 300)
        $table->decimal('wallet_paid', 10, 2)->default(0); // Paid by Allocation (e.g. 200)
        $table->decimal('mpesa_paid', 10, 2)->default(0);  // Paid by M-Pesa (e.g. 100)
        
        // M-PESA TRACKING
        $table->string('mpesa_code')->nullable(); // e.g. QWE2345TY
        $table->string('phone_number')->nullable();
        
        $table->enum('status', ['pending', 'paid', 'completed', 'cancelled'])->default('pending');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
