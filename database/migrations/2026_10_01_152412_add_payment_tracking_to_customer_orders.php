<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_orders', function (Blueprint $t) {
            // pending | paid | failed | refunded
            $t->string('payment_status', 20)->default('pending')->after('status');
            // Untuk pesanan Down Payment: kapan DP dan sisa pembayaran dilunasi
            $t->timestamp('dp_paid_at')->nullable()->after('payment_status');
            $t->timestamp('remaining_paid_at')->nullable()->after('dp_paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('customer_orders', function (Blueprint $t) {
            $t->dropColumn(['payment_status', 'dp_paid_at', 'remaining_paid_at']);
        });
    }
};