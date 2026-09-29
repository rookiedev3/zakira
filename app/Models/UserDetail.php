<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDetail extends Model
{
    protected $fillable = [
        'user_id',
        'phone',
        'address',
        'city',
        'province',
        'postal_code',
        'seller_id',
        'shipping_expedition',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}