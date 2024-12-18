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
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');  
            $table->string('item_name', 45)->nullable();
            $table->string('item_description', 225)->nullable();
            $table->decimal('item_price', 10, 2)->nullable();
            $table->string('item_image', 255)->nullable();
            $table->unsignedInteger('category_id');  
            $table->tinyInteger('is_deleted')->default(0);  // 0 = not deleted, 1 = deleted
            $table->timestamps(); 

             // Foreign key constraint to reference categories table 'id'
             $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
