<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerService extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone_number',
        'order',
        'status',
        'is_floating_whatsapp',
    ];

    protected $casts = [
        'is_floating_whatsapp' => 'boolean',
    ];

    /**
     * Format nomor telepon jadi link wa.me.
     * Contoh: 0812xxxx -> 62812xxxx
     */
    public function getWhatsappUrlAttribute(): string
    {
        $number = preg_replace('/\D/', '', $this->phone_number); // buang selain angka

        if (str_starts_with($number, '0')) {
            $number = '62' . substr($number, 1);
        }

        return 'https://wa.me/' . $number;
    }
}