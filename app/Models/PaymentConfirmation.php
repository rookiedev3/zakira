<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentConfirmation extends Model
{
    protected $fillable = [
        'order_id', 'bank_name', 'account_name', 'amount',
        'transfer_date', 'proof_path', 'note', 'status',
    ];

    protected $casts = [
        'transfer_date' => 'date',
    ];

    public function order()
    {
        return $this->belongsTo(CustomerOrder::class, 'order_id');
    }
}