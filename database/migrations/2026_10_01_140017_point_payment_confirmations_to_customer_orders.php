<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Foreign key lama menunjuk ke tabel `orders` (desain lama).
        // Pesanan checkout sekarang ada di `customer_orders`.
        Schema::table('payment_confirmations', function (Blueprint $t) {
            $t->dropForeign(['order_id']);
        });

        Schema::table('payment_confirmations', function (Blueprint $t) {
            $t->foreign('order_id')
              ->references('id')->on('customer_orders')
              ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payment_confirmations', function (Blueprint $t) {
            $t->dropForeign(['order_id']);
        });

        Schema::table('payment_confirmations', function (Blueprint $t) {
            $t->foreign('order_id')
              ->references('id')->on('orders')
              ->cascadeOnDelete();
        });
    }
};