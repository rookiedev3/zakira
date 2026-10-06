<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_confirmations', function (Blueprint $table) {
            // 'dp' = bukti DP, 'remaining' = bukti pelunasan
            $table->string('type', 20)->default('dp')->after('order_id');
        });
    }

    public function down(): void
    {
        Schema::table('payment_confirmations', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};