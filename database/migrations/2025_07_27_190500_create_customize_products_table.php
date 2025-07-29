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
        Schema::create('customize_products', function (Blueprint $table) {
              $table->id();
        $table->string('name');
        $table->unsignedBigInteger('category_id');
        $table->unsignedBigInteger('subcategory_id');
        $table->text('short_description')->nullable();
        $table->longText('long_description')->nullable();
        $table->json('images')->nullable();
        $table->json('attributes')->nullable(); // heading, type, value
        $table->text('properties')->nullable(); // paragraph content
        $table->decimal('expected_price', 10, 2)->nullable();
        $table->timestamps();

        $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        $table->foreign('subcategory_id')->references('id')->on('subcategories')->onDelete('cascade');
   
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customize_products');
    }
};
