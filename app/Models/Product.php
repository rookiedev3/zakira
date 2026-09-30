<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    // Jika di tabel products ada kolom lain yang sudah dipakai, tambahkan di sini.
    protected $fillable = [
        'name',
        'description',
        'brand_id',
        'category_id',
        'image',
        'product_type',
        'is_active',
        'show_public',
        'show_member',
        'show_distributor',
        'weight_grams',
        'product_note',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_public' => 'boolean',
        'show_member' => 'boolean',
        'show_distributor' => 'boolean',
        'weight_grams' => 'integer',
    ];

    /* ---------------- Relasi ---------------- */

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function colors(): HasMany
    {
        return $this->hasMany(ProductColor::class);
    }

    public function models(): HasMany
    {
        return $this->hasMany(ProductModel::class);
    }

    public function sizes(): HasMany
    {
        return $this->hasMany(ProductSize::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function freeItems(): HasMany
    {
        return $this->hasMany(ProductFreeItem::class);
    }

    public function priceRules(): HasMany
    {
        return $this->hasMany(ProductPriceRule::class);
    }

    /* ---------------- Accessor untuk halaman index ---------------- */

    // Contoh: "Rp 80.000" atau "Rp 10.001 - Rp 20.001"
    public function getPriceRangeAttribute(): ?string
    {
        // Memakai hasil withMin/withMax dari controller bila ada (hindari N+1)
        $min = $this->prices_min_price ?? $this->prices()->min('price');
        $max = $this->prices_max_price ?? $this->prices()->max('price');

        if ($min === null) {
            return null;
        }

        $format = fn($n) => 'Rp ' . number_format((int) $n, 0, ',', '.');

        return (int) $min === (int) $max
            ? $format($min)
            : $format($min) . ' - ' . $format($max);
    }

    // Contoh: ['Umum', 'Member', 'Distributor']
    public function getAccessLabelsAttribute(): array
    {
        return array_values(array_filter([
            $this->show_public ? 'Umum' : null,
            $this->show_member ? 'Member' : null,
            $this->show_distributor ? 'Distributor' : null,
        ]));
    }

    public function priceFor($modelId = null, $colorId = null, $sizeId = null): int
    {
        $match = $this->prices
            ->filter(function ($p) use ($modelId, $sizeId) {
                return (is_null($p->product_size_id)  || $p->product_size_id  == $sizeId)
                    && (is_null($p->product_model_id) || $p->product_model_id == $modelId);
            })
            ->sortByDesc(
                fn($p) =>
                (int) ! is_null($p->product_size_id) +
                    (int) ! is_null($p->product_model_id)
            )
            ->first();

        // Cadangan: harga terendah bila tidak ada baris yang cocok
        return (int) ($match->price ?? $this->prices->min('price') ?? 0);
    }
}
