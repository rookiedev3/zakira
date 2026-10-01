<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_confirmations', function (Blueprint $t) {
            $t->id();
            $t->foreignId('order_id')->constrained('customer_orders')->cascadeOnDelete();
            $t->string('bank_name', 100);          // bank pengirim
            $t->string('account_name', 100);       // nama pemilik rekening pengirim
            $t->unsignedBigInteger('amount');      // nominal yang ditransfer
            $t->date('transfer_date');
            $t->string('proof_path');              // path bukti transfer (disk public)
            $t->text('note')->nullable();
            $t->string('status', 20)->default('pending'); // pending | verified | rejected
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_confirmations');
    }
};