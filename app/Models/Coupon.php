<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'description',
        'type', 'value', 'max_discount_amount',
        'starts_at', 'expires_at', 'usage_limit', 'usage_limit_per_customer', 'used_count',
        'customer_scope',
        'minimum_amount', 'minimum_quantity',
        'restriction_type',
        'active', 'new_customers_only', 'stackable', 'show_in_checkout',
        'checkout_label', 'checkout_description',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'active' => 'boolean',
        'new_customers_only' => 'boolean',
        'stackable' => 'boolean',
        'show_in_checkout' => 'boolean',
        'value' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
    ];

    /* ---------------- Relasi ---------------- */

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'coupon_category');
    }

    public function brands(): BelongsToMany
    {
        return $this->belongsToMany(Brand::class, 'coupon_brand');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'coupon_product');
    }

    /* ---------------- Scopes ---------------- */

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('code', 'like', "%{$term}%");
        });
    }

    public function scopeType(Builder $query, ?string $type): Builder
    {
        return $type && $type !== 'all' ? $query->where('type', $type) : $query;
    }

    public function scopeAudience(Builder $query, ?string $scope): Builder
    {
        return $scope && $scope !== 'all' ? $query->where('customer_scope', $scope) : $query;
    }

    /* ---------------- Accessor status ---------------- */

    public function getStatusAttribute(): string
    {
        if (! $this->active) {
            return 'inactive';
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return 'expired';
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return 'used_up';
        }

        return 'active';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Aktif',
            'inactive' => 'Nonaktif',
            'expired' => 'Kedaluwarsa',
            'used_up' => 'Limit Tercapai',
        };
    }

    public function getDiscountLabelAttribute(): string
    {
        $value = $this->type === 'percentage'
            ? number_format((float) $this->value, 0) . '%'
            : 'Rp ' . number_format((float) $this->value, 0, ',', '.');

        return $value;
    }

    public function getAudienceLabelAttribute(): string
    {
        return match ($this->customer_scope) {
            'member' => 'Khusus Member',
            'non_member' => 'Non Member',
            default => 'Semua',
        };
    }
}