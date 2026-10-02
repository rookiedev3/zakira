<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title'       => 'Promo Utama Slider',
                'type'        => 'slider',
                'description' => 'Dapatkan diskon menarik untuk semua produk minggu ini.',
                'image_path'  => 'images/meme.jpeg',
                'url'         => '/',
                'order'       => 1,
                'status'      => 'aktif',
            ],
            [
                'title'       => 'Banner Promo Samping',
                'type'        => 'promo',
                'description' => 'Penawaran khusus terbatas hingga akhir bulan.',
                'image_path'  => 'images/meme.jpeg',
                'url'         => '/',
                'order'       => 2,
                'status'      => 'aktif',
            ],
        ];

        foreach ($banners as $banner) {
            Banner::updateOrCreate(
                ['title' => $banner['title']],
                $banner
            );
        }
    }
}