<?php

namespace Database\Seeders;

use App\Models\SocialMedia;
use Illuminate\Database\Seeder;

class SocialMediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $socialMedia = [
            [
                'platform'   => 'TikTok',
                'icon_class' => 'fab fa-tiktok',
                'color'      => '#000000',
                'url'        => 'https://www.tiktok.com/@zakira_official',
                'order'      => 1,
                'status'     => 'aktif',
            ],
            [
                'platform'   => 'Instagram',
                'icon_class' => 'fab fa-instagram',
                'color'      => '#E1306C',
                'url'        => 'https://www.instagram.com/zakira_official',
                'order'      => 2,
                'status'     => 'aktif',
            ],
        ];

        foreach ($socialMedia as $item) {
            SocialMedia::firstOrCreate(
                ['platform' => $item['platform']],
                $item
            );
        }
    }
}