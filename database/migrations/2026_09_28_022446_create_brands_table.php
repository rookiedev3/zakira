<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();              // path di storage/public
            $table->boolean('show_on_home')->default(false); // tampil di carousel homepage
            $table->unsignedInteger('home_order')->default(0); // urutan carousel
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['show_on_home', 'home_order']);
        });

        // Hubungkan produk ke brand (hanya jika tabel products sudah ada)
        if (Schema::hasTable('products') && ! Schema::hasColumn('products', 'brand_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('brand_id')
                    ->nullable()
                    ->constrained('brands')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'brand_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropConstrainedForeignId('brand_id');
            });
        }

        Schema::dropIfExists('brands');
    }
};