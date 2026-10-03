<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_orders', function (Blueprint $t) {
            $t->foreignId('admin_handle_id')
                ->nullable()
                ->after('seller_id')
                ->constrained('admin_handles')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('customer_orders', function (Blueprint $t) {
            $t->dropConstrainedForeignId('admin_handle_id');
        });
    }
};