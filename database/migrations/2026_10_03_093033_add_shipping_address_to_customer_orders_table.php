<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_orders', function (Blueprint $t) {
            $t->boolean('ship_different')->default(false)->after('notes');
            $t->string('ship_recipient', 100)->nullable()->after('ship_different');
            $t->string('ship_phone', 20)->nullable()->after('ship_recipient');
            $t->text('ship_address')->nullable()->after('ship_phone');
            $t->string('ship_city', 100)->nullable()->after('ship_address');
            $t->string('ship_province', 100)->nullable()->after('ship_city');
            $t->string('ship_postal_code', 10)->nullable()->after('ship_province');
        });
    }

    public function down(): void
    {
        Schema::table('customer_orders', function (Blueprint $t) {
            $t->dropColumn([
                'ship_different',
                'ship_recipient',
                'ship_phone',
                'ship_address',
                'ship_city',
                'ship_province',
                'ship_postal_code',
            ]);
        });
    }
};