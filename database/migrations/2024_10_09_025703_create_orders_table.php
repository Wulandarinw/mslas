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
        Schema::create('orders', function (Blueprint $table) {
            $table->string('order_code')->primary();
            $table->foreignId('customer_address_id')->constrained('customer_addresses', 'customer_address_id')->onDelete('cascade');
            $table->string('payment_name');
            $table->string('shipment_name');
            $table->date('order_date');
            $table->integer('shopping_cost');
            $table->enum('payment_status', ['paid', 'unpaid']);
            $table->date('payment_date');
            $table->enum('shipment_status',['processing', 'shipping', 'arrived'])->nullable();
            $table->integer('total_amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
