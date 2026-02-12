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
        // Staff Number: Unique, Nullable (for students/admins who might not have one initially)
        $table->string('staff_number')->unique()->nullable()->after('name'); 
        $table->string('department')->nullable()->after('staff_number');
        // We ensure email is nullable if you want staff to ONLY use Staff Number, 
        // but for now we keep email required as per standard Laravel, just allowed to login with ID.
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['staff_number', 'department']);
    });
}
};
