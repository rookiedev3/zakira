<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_orders', function (Blueprint $t) {
            $t->id();
            $t->string('order_number', 30)->unique();
            $t->string('seller_id', 50)->nullable();
            $t->string('whatsapp_number', 20);
            $t->string('shipping_method', 50);
            $t->string('first_name', 100);
            $t->string('last_name', 100);
            $t->text('address');
            $t->string('city', 100);
            $t->string('province', 100);
            $t->string('postal_code', 10);
            $t->text('notes')->nullable();
            $t->unsignedBigInteger('subtotal');
            $t->unsignedBigInteger('discount')->default(0);
            $t->string('coupon_code', 50)->nullable();
            $t->unsignedBigInteger('total');
            $t->string('payment_method', 10);
            $t->unsignedTinyInteger('dp_percent')->nullable();
            $t->unsignedBigInteger('amount_due');
            $t->string('status', 20)->default('pending');
            $t->timestamps();
        });

        Schema::create('customer_order_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('customer_order_id')->constrained('customer_orders')->cascadeOnDelete();
            $t->unsignedBigInteger('product_id')->nullable();
            $t->string('product_name');
            $t->string('model')->nullable();
            $t->string('color')->nullable();
            $t->string('size')->nullable();
            $t->unsignedBigInteger('price');
            $t->unsignedInteger('quantity');
            $t->unsignedBigInteger('subtotal');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_order_items');
        Schema::dropIfExists('customer_orders');
    }
};