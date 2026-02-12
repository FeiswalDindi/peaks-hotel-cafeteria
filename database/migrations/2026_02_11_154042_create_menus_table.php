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
    Schema::create('menus', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // e.g., "Chapati"
        $table->decimal('price', 8, 2); // e.g., 20.00
        $table->text('description')->nullable();
        $table->string('image')->nullable();
        $table->boolean('is_available')->default(true); // To mark "Sold Out"
        $table->foreignId('category_id')->constrained()->onDelete('cascade'); // Links to Category
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
