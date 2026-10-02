<?php

namespace Database\Seeders;

use App\Models\Advantage;
use Illuminate\Database\Seeder;

class AdvantageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $advantages = [
            [
                'title'       => 'Keunggulan 1',
                'description' => 'Kualitas produk terjamin dengan standar mutu terbaik dan pelayanan yang cepat.',
                'image_path'  => 'advantages/meme.jpeg',
                'order'       => 1,
                'status'      => 'aktif',
            ],
            [
                'title'       => 'Keunggulan 2',
                'description' => 'Harga kompetitif dengan dukungan garansi penuh untuk setiap pembelian.',
                'image_path'  => 'advantages/meme.jpeg',
                'order'       => 2,
                'status'      => 'aktif',
            ],
        ];

        foreach ($advantages as $advantage) {
            Advantage::firstOrCreate(
                ['title' => $advantage['title']],
                $advantage
            );
        }
    }
}