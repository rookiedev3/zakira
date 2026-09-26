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
        // Brands
        // -----------------------------------------------------------------
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // -----------------------------------------------------------------
        // Categories
        // -----------------------------------------------------------------
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // -----------------------------------------------------------------
        // Colors
        // -----------------------------------------------------------------
        Schema::create('colors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('hex_code')->nullable(); // e.g. #FF0000
            $table->timestamps();
        });

        // -----------------------------------------------------------------
        // Models (product model variants)
        // -----------------------------------------------------------------
        Schema::create('models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // -----------------------------------------------------------------
        // Sizes
        // -----------------------------------------------------------------
        Schema::create('sizes', function (Blueprint $table) {
            $table->id();
            $table->string('size'); // e.g. XS, S, M, L, XL
            $table->boolean('available')->default(true);
            $table->timestamps();
        });

        // -----------------------------------------------------------------
        // Free Items (bonus items)
        // -----------------------------------------------------------------
        Schema::create('free_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('condition')->nullable(); // JSON for flexible rules
            $table->timestamps();
        });

        // -----------------------------------------------------------------
        // Price Rules (automatic add/subtract)
        // -----------------------------------------------------------------
        Schema::create('price_rules', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['add', 'subtract']);
            $table->unsignedInteger('value'); // value in rupiah
            $table->json('condition')->nullable(); // e.g. {"size":"L"}
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_rules');
        Schema::dropIfExists('free_items');
        Schema::dropIfExists('sizes');
        Schema::dropIfExists('models');
        Schema::dropIfExists('colors');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('brands');
    }
};