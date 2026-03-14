<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            
            // The staff member sending the funds (Nullable if the system/admin is injecting funds)
            $table->unsignedBigInteger('sender_id')->nullable();
            
            // The staff member receiving the funds (Nullable if funds are spent on a meal)
            $table->unsignedBigInteger('receiver_id')->nullable();
            
            // The exact amount moved
            $table->decimal('amount', 10, 2);
            
            // To categorize: 'p2p_transfer', 'admin_override', or 'meal_purchase'
            $table->string('type'); 
            
            // Optional: A quick note like "Covering lunch for Jane"
            $table->text('description')->nullable(); 
            
            $table->timestamps();

            // Linking the IDs to your actual users table
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
