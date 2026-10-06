<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/** Pesanan hasil checkout (terpisah dari model Order yang sudah ada). */
class CustomerOrder extends Model
{
    public const EDIT_WINDOW_HOURS = 72;

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

    // [VERSI LAIN - dinonaktifkan karena nama method sama dengan scopeOwnedBy di atas
    //  (versi di atas lebih lengkap: mencocokkan variasi 0812 / 62812 / +62812)]
    // Pesanan milik user (dicocokkan lewat email atau no. telp, sama seperti riwayat pesanan)
    // public function scopeOwnedBy(Builder $query, $user): Builder
    // {
    //     $phone = $user->detail?->phone;
    //
    //     return $query->where(function ($q) use ($user, $phone) {
    //         $q->where('email', $user->email);
    //         if (filled($phone)) {
    //             $q->orWhere('whatsapp_number', $phone);
    //         }
    //     });
    // }

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

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_code', 'code');
    }

    /* ---------- Masa edit pesanan ---------- */

    public function editDeadline(): Carbon
    {
        return $this->created_at->copy()->addHours(self::EDIT_WINDOW_HOURS);
    }

    public function editSecondsLeft(): int
    {
        return max(0, (int) floor(now()->diffInSeconds($this->editDeadline(), false)));
    }

    // Bukti transfer DP sudah dikirim (atau pembayaran sudah tercatat)
    // Bukti disimpan di tabel payment_confirmations (lihat ProfileController@storePayment)
    public function hasPaymentProof(): bool
    {
        return $this->paymentConfirmations()->exists()
            || $this->dp_paid_at
            || $this->remaining_paid_at;
    }

    // [VERSI LAIN - dinonaktifkan karena bukti tidak disimpan di kolom dp_proof]
    // public function hasPaymentProof(): bool
    // {
    //     return filled($this->dp_proof) || $this->dp_paid_at || $this->remaining_paid_at;
    // }

    // Alasan tidak bisa diedit, null = boleh diedit
    public function editBlockReason(): ?string
    {
        if ($this->status === 'cancelled') {
            return 'Pesanan sudah dibatalkan.';
        }
        if ($this->status !== 'pending') {
            return 'Pesanan sudah diproses, tidak bisa diedit.';
        }
        if ($this->hasPaymentProof()) {
            return 'Bukti transfer DP sudah dikirim, pesanan tidak bisa diedit lagi.';
        }
        if ($this->invoice()->exists()) {
            return 'Faktur sudah dibuat, pesanan tidak bisa diedit.';
        }
        if ($this->editSecondsLeft() <= 0) {
            return 'Masa pengeditan 72 jam sudah berakhir.';
        }
        return null;
    }

    public function isEditable(): bool
    {
        return $this->editBlockReason() === null;
    }
}