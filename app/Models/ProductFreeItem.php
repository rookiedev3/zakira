<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductFreeItem extends Model
{
    protected $fillable = [
        'product_id', 'name', 'quantity',
        'product_color_id', 'product_model_id', 'product_size_id',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}