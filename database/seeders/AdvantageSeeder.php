<?php

namespace Database\Seeders;

use App\Models\Advantage;
use Illuminate\Database\Seeder;

class AdvantageSeeder extends Seeder
{
    public function run(): void
    {
        $advantages = [
            [
                'title'       => 'Keunggulan 1',
                'description' => 'Kualitas produk terjamin dengan standar mutu terbaik dan pelayanan yang cepat.',
                'image_path'  => 'images/meme.jpeg',
                'order'       => 1,
                'status'      => 'aktif',
            ],
            [
                'title'       => 'Keunggulan 2',
                'description' => 'Harga kompetitif dengan dukungan garansi penuh untuk setiap pembelian.',
                'image_path'  => 'images/meme.jpeg',
                'order'       => 2,
                'status'      => 'aktif',
            ],
        ];

        foreach ($advantages as $advantage) {
            Advantage::updateOrCreate(
                ['title' => $advantage['title']],
                $advantage
            );
        }
    }
}