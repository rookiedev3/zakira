<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();

            // Konfigurasi Diskon
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('value', 12, 2);
            $table->decimal('max_discount_amount', 12, 2)->nullable();

            // Tanggal & Batasan Penggunaan
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->unsignedInteger('usage_limit')->nullable();
            $table->unsignedInteger('usage_limit_per_customer')->nullable();
            $table->unsignedInteger('used_count')->default(0);

            // Target Voucher
            $table->enum('customer_scope', ['all', 'member', 'non_member'])->default('all');

            // Persyaratan
            $table->decimal('minimum_amount', 12, 2)->nullable();
            $table->unsignedInteger('minimum_quantity')->nullable();

            // Batasan Produk & Kategori
            $table->enum('restriction_type', ['only', 'except'])->nullable();

            // Pengaturan
            $table->boolean('active')->default(true);
            $table->boolean('new_customers_only')->default(false);
            $table->boolean('stackable')->default(false);
            $table->boolean('show_in_checkout')->default(false);

            // Tampilan di Checkout
            $table->string('checkout_label')->nullable();
            $table->text('checkout_description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};