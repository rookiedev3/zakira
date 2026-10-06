<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('customer_orders', 'edit_count')) {
                // Jumlah berapa kali pesanan diedit member
                $table->unsignedInteger('edit_count')->default(0);
            }

            if (! Schema::hasColumn('customer_orders', 'last_edited_at')) {
                // Waktu edit terakhir
                $table->timestamp('last_edited_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('customer_orders', function (Blueprint $table) {
            if (Schema::hasColumn('customer_orders', 'last_edited_at')) {
                $table->dropColumn('last_edited_at');
            }

            if (Schema::hasColumn('customer_orders', 'edit_count')) {
                $table->dropColumn('edit_count');
            }
        });
    }
};