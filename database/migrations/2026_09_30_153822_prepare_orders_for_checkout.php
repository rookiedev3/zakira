<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Aman dijalankan walau tabel `orders` sudah ada: hanya membuat tabel / menambah kolom yang belum ada.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $t) {
                $t->id();
                $t->timestamps();
            });
        }

        $columns = [
            'order_number'    => fn (Blueprint $t) => $t->string('order_number', 30)->nullable()->unique(),
            'seller_id'       => fn (Blueprint $t) => $t->string('seller_id', 50)->nullable(),
            'whatsapp_number' => fn (Blueprint $t) => $t->string('whatsapp_number', 20)->nullable(),
            'shipping_method' => fn (Blueprint $t) => $t->string('shipping_method', 50)->nullable(),
            'first_name'      => fn (Blueprint $t) => $t->string('first_name', 100)->nullable(),
            'last_name'       => fn (Blueprint $t) => $t->string('last_name', 100)->nullable(),
            'address'         => fn (Blueprint $t) => $t->text('address')->nullable(),
            'city'            => fn (Blueprint $t) => $t->string('city', 100)->nullable(),
            'province'        => fn (Blueprint $t) => $t->string('province', 100)->nullable(),
            'postal_code'     => fn (Blueprint $t) => $t->string('postal_code', 10)->nullable(),
            'notes'           => fn (Blueprint $t) => $t->text('notes')->nullable(),
            'subtotal'        => fn (Blueprint $t) => $t->unsignedBigInteger('subtotal')->default(0),
            'discount'        => fn (Blueprint $t) => $t->unsignedBigInteger('discount')->default(0),
            'coupon_code'     => fn (Blueprint $t) => $t->string('coupon_code', 50)->nullable(),
            'total'           => fn (Blueprint $t) => $t->unsignedBigInteger('total')->default(0),
            'payment_method'  => fn (Blueprint $t) => $t->string('payment_method', 10)->nullable(),
            'dp_percent'      => fn (Blueprint $t) => $t->unsignedTinyInteger('dp_percent')->nullable(),
            'amount_due'      => fn (Blueprint $t) => $t->unsignedBigInteger('amount_due')->default(0),
            'status'          => fn (Blueprint $t) => $t->string('status', 20)->default('pending'),
        ];

        foreach ($columns as $name => $define) {
            if (! Schema::hasColumn('orders', $name)) {
                Schema::table('orders', fn (Blueprint $t) => $define($t));
            }
        }

        if (! Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $t) {
                $t->id();
                $t->foreignId('order_id')->constrained()->cascadeOnDelete();
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
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};