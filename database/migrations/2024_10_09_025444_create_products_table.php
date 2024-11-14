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
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->string('name');
            $table->foreignId('shop_id')->constrained('shops','shop_id')->onDelete('cascade');
            $table->string('category_code');
            $table->text('desc');
            $table->string('dimension')->nullable();
            $table->integer('weight');
            $table->enum('status', ['Publish', 'Draft'])->default('Draft');
            $table->timestamps();

            $table->foreign('category_code')->references('category_code')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
