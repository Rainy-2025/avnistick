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
        Schema::create('ready_semi_products', function (Blueprint $table) {
                 $table->id();
        $table->string('name');
                $table->string('type')->default('readymade');
        $table->unsignedBigInteger('category_id');
        $table->unsignedBigInteger('subcategory_id');
        $table->text('short_description')->nullable();
        $table->longText('long_description')->nullable();
        $table->json('images')->nullable();
        $table->json('sizes')->nullable();
        $table->json('attributes')->nullable();
        $table->longText('properties')->nullable();
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
        Schema::dropIfExists('ready_semi_products');
    }
};
