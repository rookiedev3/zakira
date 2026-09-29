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

    public function getImageUrlAttribute(): string
    {
        return Storage::url($this->image_path);
    }
}