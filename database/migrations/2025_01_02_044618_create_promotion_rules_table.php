<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotion_rules', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('promotion_id') 
                  ->constrained('promotions') 
                  ->onDelete('cascade'); 
            $table->decimal('min_price', 10, 2);
            $table->decimal('discount_percentage', 5, 2);
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotion_rules');
    }
}
