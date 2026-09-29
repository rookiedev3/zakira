<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPrice extends Model
{
    protected $fillable = [
        'product_id', 'product_model_id', 'product_size_id', 'price',
    ];

    protected $casts = [
        'price' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productModel(): BelongsTo
    {
        return $this->belongsTo(ProductModel::class, 'product_model_id');
    }

    public function productSize(): BelongsTo
    {
        return $this->belongsTo(ProductSize::class, 'product_size_id');
    }
}