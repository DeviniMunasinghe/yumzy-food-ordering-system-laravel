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
             // Adding the new columns
             $table->string('user_image', 255)->nullable()->default(null);
             $table->string('phone_no', 255)->nullable()->default(null);
             $table->string('address', 255)->nullable()->default(null);
             $table->string('first_name', 255)->nullable()->default(null);
             $table->string('last_name', 255)->nullable()->default(null);
             $table->tinyInteger('is_deleted')->default(0); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Dropping the added columns in case of rollback
            $table->dropColumn(['user_image', 'phone_no', 'address', 'first_name', 'last_name', 'is_deleted']);
        });
    }
};
