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
        // -----------------------------------------------------------------
        // Products (main table)
        // -----------------------------------------------------------------
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();

            // FK ke brand
            $table->foreignId('brand_id')
                  ->constrained()
                  ->cascadeOnDelete();

            // -----------------------------------------------------------------
            // Zakira‑specific settings
            // -----------------------------------------------------------------
            $table->enum('product_type', ['ready', 'po'])->default('ready'); // ready = stok, po = pre‑order
            $table->boolean('active')->default(true);

            // Visibility
            $table->boolean('show_public')->default(true);
            $table->boolean('show_member')->default(true);
            $table->boolean('show_distributor')->default(false);

            // Berat (gram) – untuk perhitungan ongkir
            $table->unsignedInteger('weight_grams')->default(1000);

            // Catatan internal
            $table->text('product_note')->nullable();

            $table->timestamps();
        });

        // -----------------------------------------------------------------
        // Pivot: product ↔ category (many‑to‑many)
        // -----------------------------------------------------------------
        Schema::create('category_product', function (Blueprint $table) {
            $table->foreignId('product_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('category_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->primary(['product_id', 'category_id']);
        });

        // -----------------------------------------------------------------
        // product_color (warna per produk dengan optional image)
        // -----------------------------------------------------------------
        Schema::create('product_color', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('color_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->string('image_path')->nullable(); // path ke storage
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // -----------------------------------------------------------------
        // product_model (model per produk dengan optional image)
        // -----------------------------------------------------------------
        Schema::create('product_model', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('model_id')
                  ->constrained('models')
                  ->cascadeOnDelete();

            $table->string('image_path')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // -----------------------------------------------------------------
        // product_size (ukuran per produk)
        // -----------------------------------------------------------------
        Schema::create('product_size', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('size_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->boolean('available')->default(true);
            $table->timestamps();
        });

        // -----------------------------------------------------------------
        // product_image (multiple gambar umum, satu dapat dijadikan primary)
        // -----------------------------------------------------------------
        Schema::create('product_image', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->string('image_path');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        // -----------------------------------------------------------------
        // product_model_price (matriks harga: model × size → price)
        // -----------------------------------------------------------------
        Schema::create('product_model_price', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('model_id')
                  ->constrained('models')
                  ->cascadeOnDelete();

            $table->foreignId('size_id')
                  ->constrained('sizes')
                  ->cascadeOnDelete();

            $table->unsignedInteger('price'); // dalam rupiah
            $table->unique(['product_id', 'model_id', 'size_id']);
            $table->timestamps();
        });

        // -----------------------------------------------------------------
        // product_free_item (pivot produk ↔ free_item)
        // -----------------------------------------------------------------
        Schema::create('product_free_item', function (Blueprint $table) {
            $table->foreignId('product_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('free_item_id')
                  ->constrained('free_items')
                  ->cascadeOnDelete();

            $table->primary(['product_id', 'free_item_id']);
        });

        // -----------------------------------------------------------------
        // product_price_rule (pivot produk ↔ price_rule)
        // -----------------------------------------------------------------
        Schema::create('product_price_rule', function (Blueprint $table) {
            $table->foreignId('product_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('price_rule_id')
                  ->constrained('price_rules')
                  ->cascadeOnDelete();

            $table->primary(['product_id', 'price_rule_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_price_rule');
        Schema::dropIfExists('product_free_item');
        Schema::dropIfExists('product_model_price');
        Schema::dropIfExists('product_image');
        Schema::dropIfExists('product_size');
        Schema::dropIfExists('product_model');
        Schema::dropIfExists('product_color');
        Schema::dropIfExists('category_product');
        Schema::dropIfExists('products');
    }
};