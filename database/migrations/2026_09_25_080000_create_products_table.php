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
        /* -----------------------------------------------------------------
         |  products – tabel inti untuk menampung data produk
         |  (sederhana: hanya field yang diperlukan oleh tampilan)
         ----------------------------------------------------------------- */
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Nama & deskripsi produk
            $table->string('name');
            $table->text('description')->nullable();

            // Relasi ke kategori (banyak‑ke‑satu, sesuai relation di Category.php)
            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->cascadeOnDelete()
                  ->nullable();   // bila produk belum memiliki kategori

            // Status aktif / non‑aktif
            $table->boolean('is_active')->default(true);

            // Kolom‑kolom tambahan yang sering dipakai pada form “Buat Produk”
            $table->enum('product_type', ['ready', 'po'])->default('ready');
            $table->unsignedInteger('weight_grams')->default(1000);
            $table->text('product_note')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};