<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Advantage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image_path',
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

        // File upload lewat storage:link (contoh: advantages/xxx.jpg)
        return Storage::url($this->image_path);
    }
}