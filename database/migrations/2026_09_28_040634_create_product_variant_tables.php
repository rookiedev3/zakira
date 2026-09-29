<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /* ---- Kolom tambahan di tabel products (dilewati jika sudah ada) ---- */
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'description')) {
                $table->text('description')->nullable();
            }
            if (! Schema::hasColumn('products', 'image')) {
                $table->string('image')->nullable();            // gambar utama
            }
            if (! Schema::hasColumn('products', 'product_type')) {
                $table->string('product_type', 10)->default('ready'); // ready | po
            }
            if (! Schema::hasColumn('products', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
            if (! Schema::hasColumn('products', 'show_public')) {
                $table->boolean('show_public')->default(true);
            }
            if (! Schema::hasColumn('products', 'show_member')) {
                $table->boolean('show_member')->default(true);
            }
            if (! Schema::hasColumn('products', 'show_distributor')) {
                $table->boolean('show_distributor')->default(false);
            }
            if (! Schema::hasColumn('products', 'weight_grams')) {
                $table->unsignedInteger('weight_grams')->default(1000);
            }
            if (! Schema::hasColumn('products', 'product_note')) {
                $table->text('product_note')->nullable();
            }
        });

        /* ---- Warna ---- */
        if (! Schema::hasTable('product_colors')) {
            Schema::create('product_colors', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->string('hex_code', 7)->nullable();
                $table->string('image')->nullable();
                $table->timestamps();
            });
        }

        /* ---- Model ---- */
        if (! Schema::hasTable('product_models')) {
            Schema::create('product_models', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->string('description')->nullable();
                $table->string('image')->nullable();
                $table->timestamps();
            });
        }

        /* ---- Ukuran ---- */
        if (! Schema::hasTable('product_sizes')) {
            Schema::create('product_sizes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->string('size', 50);
                $table->boolean('is_available')->default(true);
                $table->timestamps();
            });
        }

        /* ---- Matriks harga (model x ukuran) ---- */
        if (! Schema::hasTable('product_prices')) {
            Schema::create('product_prices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_model_id')->constrained('product_models')->cascadeOnDelete();
                $table->foreignId('product_size_id')->constrained('product_sizes')->cascadeOnDelete();
                $table->unsignedBigInteger('price');
                $table->timestamps();

                $table->unique(['product_model_id', 'product_size_id']);
            });
        }

        /* ---- Gambar produk umum ---- */
        if (! Schema::hasTable('product_images')) {
            Schema::create('product_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->string('path');
                $table->boolean('is_primary')->default(false);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        /* ---- FREE barang / bonus ---- */
        if (! Schema::hasTable('product_free_items')) {
            Schema::create('product_free_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->unsignedInteger('quantity')->default(1);
                // Kondisi kosong (null) = berlaku untuk semua varian
                $table->foreignId('product_color_id')->nullable()->constrained('product_colors')->nullOnDelete();
                $table->foreignId('product_model_id')->nullable()->constrained('product_models')->nullOnDelete();
                $table->foreignId('product_size_id')->nullable()->constrained('product_sizes')->nullOnDelete();
                $table->timestamps();
            });
        }

        /* ---- Harga otomatis (tambah / potong) ---- */
        if (! Schema::hasTable('product_price_rules')) {
            Schema::create('product_price_rules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->string('label')->nullable();
                $table->string('type', 10);                       // add | cut
                $table->unsignedBigInteger('amount');             // nominal Rupiah
                $table->foreignId('product_color_id')->nullable()->constrained('product_colors')->nullOnDelete();
                $table->foreignId('product_model_id')->nullable()->constrained('product_models')->nullOnDelete();
                $table->foreignId('product_size_id')->nullable()->constrained('product_sizes')->nullOnDelete();
                $table->timestamps();
            });
        }

        /* ---- Relasi Kategori (many-to-many) ---- */
        if (! Schema::hasTable('product_categories')) {
            Schema::create('product_categories', function (Blueprint $table) {
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->foreignId('category_id')->constrained()->cascadeOnDelete();
                $table->primary(['product_id', 'category_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('product_price_rules');
        Schema::dropIfExists('product_free_items');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_prices');
        Schema::dropIfExists('product_sizes');
        Schema::dropIfExists('product_models');
        Schema::dropIfExists('product_colors');

        // Kolom tambahan di products sengaja tidak dihapus agar data produk tidak hilang.
    }
};