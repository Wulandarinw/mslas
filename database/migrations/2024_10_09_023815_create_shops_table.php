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
        Schema::create('shops', function (Blueprint $table) {
            $table->id('shop_id');
            $table->string('seller_ktp_nik');
            $table->string('name')->unique();
            $table->string('url_domain')->unique();
            $table->text('description');
            $table->string('shop_icon');
            $table->string('kota');
            $table->timestamps();

            $table->foreign('seller_ktp_nik')->references('ktp_nik')->on('sellers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
