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
            // Ensure we add the 'username' column if it doesn't already exist
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->unique();
            }

            // Drop the 'name' column if it exists
            if (Schema::hasColumn('users', 'name')) {
                $table->dropColumn('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove the 'username' column
            if (Schema::hasColumn('users', 'username')) {
                $table->dropColumn('username');
            }

            // Re-add the 'name' column if needed
            $table->string('name')->nullable();
        });
    }
};
