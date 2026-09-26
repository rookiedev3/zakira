<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model dummy hanya untuk men‑support relasi Category → Product.
 * Jika di kemudian hari Anda menambahkan tabel `products`,
 * cukup lengkapi properti `$fillable` atau migrasi yang sesuai.
 */
class Product extends Model
{
    use HasFactory;

    // Misalnya tabel bernama `products` (default)
    protected $guarded = [];   // memungkinkan mass‑assignment sederhana
}