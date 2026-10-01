<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_confirmations', function (Blueprint $t) {
            $t->string('bank_name', 100)->nullable()->change();
            $t->string('account_name', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('payment_confirmations', function (Blueprint $t) {
            $t->string('bank_name', 100)->nullable(false)->change();
            $t->string('account_name', 100)->nullable(false)->change();
        });
    }
};