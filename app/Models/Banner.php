<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'description',
        'image_path',
        'url',
        'order',
        'status',
    ];

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        // File statis di public/ (contoh: images/meme.jpeg)
        if (file_exists(public_path($this->image_path))) {
            return asset($this->image_path);
        }

        // File upload lewat storage:link (contoh: banners/xxx.jpg)
        return asset('storage/' . $this->image_path);
    }
}