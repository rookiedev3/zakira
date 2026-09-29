<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductModel extends Model
{
    protected $fillable = [
        'product_id', 'name', 'description', 'image',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}