<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/** Pesanan hasil checkout (terpisah dari model Order yang sudah ada). */
class CustomerOrder extends Model
{
    protected $guarded = [];

    public function items(): HasMany
    {
        return $this->hasMany(CustomerOrderItem::class, 'customer_order_id');
    }

    public function paymentConfirmations(): HasMany
    {
        return $this->hasMany(PaymentConfirmation::class, 'order_id');
    }

    public function adminHandle(): BelongsTo
    {
        return $this->belongsTo(AdminHandle::class);
    }

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'customer_order_id')->latestOfMany();
    }
}