<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    /* ---------- Riwayat pesanan di halaman Akun Saya ---------- */

    // Pesanan milik user = email sama ATAU nomor WhatsApp sama dengan telepon di profilnya
    public function scopeOwnedBy(Builder $query, $user): Builder
    {
        $phones = self::phoneVariants($user->detail?->phone);

        return $query->where(function (Builder $w) use ($user, $phones) {
            $w->where('email', $user->email);

            if ($phones) {
                $w->orWhereIn('whatsapp_number', $phones);
            }
        });
    }

    // Variasi penulisan nomor: 0812..., 62812..., +62812...
    public static function phoneVariants(?string $phone): array
    {
        $digits = preg_replace('/\D/', '', (string) $phone);
        if ($digits === '') {
            return [];
        }

        $core = preg_replace('/^(62|0)/', '', $digits);

        return ['0' . $core, '62' . $core, '+62' . $core];
    }
}