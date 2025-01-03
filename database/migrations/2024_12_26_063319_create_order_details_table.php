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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->string('address', 255)->nullable();
            $table->string('city', 45)->nullable();
            $table->string('postal_code', 45)->nullable();
            $table->unsignedBigInteger('order_id');
            $table->string('phone_number', 255)->nullable();
            $table->string('first_name', 45)->nullable();
            $table->string('last_name', 255)->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
