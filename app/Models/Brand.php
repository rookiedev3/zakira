<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'logo',
        'show_on_home',
        'home_order',
        'is_active',
    ];

    protected $casts = [
        'show_on_home' => 'boolean',
        'is_active'    => 'boolean',
        'home_order'   => 'integer',
    ];

    /* ---------------- Relasi ---------------- */

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'coupon_brand');
    }

    /* ---------------- Scopes ---------------- */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    // Untuk carousel "Brand Pilihan" di halaman depan
    public function scopeHome(Builder $query): Builder
    {
        return $query->active()
            ->where('show_on_home', true)
            ->orderBy('home_order')
            ->orderBy('name');
    }
}