<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banners = [
            [
                'title'        => 'Promo Utama Slider',
                'type'         => 'slider',
                'description'  => 'Dapatkan diskon menarik untuk semua produk minggu ini.',
                'image_path'   => 'banners/meme.jpeg',
                'url'          => '/promo/spesial',
                'order'        => 1,
                'status'       => 'aktif',
            ],
            [
                'title'        => 'Banner Promo Samping',
                'type'         => 'promo',
                'description'  => 'Penawaran khusus terbatas hingga akhir bulan.',
                'image_path'   => 'banners/meme.jpeg',
                'url'          => '/promo/terbatas',
                'order'        => 2,
                'status'       => 'aktif',
            ],
        ];

        foreach ($banners as $banner) {
            Banner::firstOrCreate(
                ['title' => $banner['title']],
                $banner
            );
        }
    }
}