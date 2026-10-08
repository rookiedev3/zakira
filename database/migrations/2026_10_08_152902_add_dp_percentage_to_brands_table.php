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
        Schema::table('brands', function (Blueprint $table) {
            $table->decimal('dp_percentage', 5, 2)->default(30.00)->after('description');
        });

        if (Schema::hasTable('customer_orders') && Schema::hasColumn('customer_orders', 'dp_percent')) {
            Schema::table('customer_orders', function (Blueprint $table) {
                $table->decimal('dp_percent', 5, 2)->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn('dp_percentage');
        });

        if (Schema::hasTable('customer_orders') && Schema::hasColumn('customer_orders', 'dp_percent')) {
            Schema::table('customer_orders', function (Blueprint $table) {
                $table->unsignedTinyInteger('dp_percent')->nullable()->change();
            });
        }
    }
};
