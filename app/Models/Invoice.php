<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $guarded = [];

    protected $casts = [
        'invoice_date' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(CustomerOrder::class, 'customer_order_id');
    }

    /** FKT-YYYYMMDD-000N (urutan per hari) */
    public static function nextNumber(): string
    {
        $prefix = 'FKT-' . now('Asia/Jakarta')->format('Ymd') . '-';
        $count  = static::where('invoice_number', 'like', $prefix . '%')->count() + 1;

        return $prefix . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}