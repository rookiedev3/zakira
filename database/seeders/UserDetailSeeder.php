<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Seeder;

class UserDetailSeeder extends Seeder
{
    public function run(): void
    {
        // Data detail yang disesuaikan dengan email dari UserSeeder
        $userDetails = [
            'admin@gmail.com' => [
                'phone' => '081234567890',
                'address' => 'Jl. Admin No. 1, Komplek Perkantoran',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '12110',
                'seller_id' => 'ADM-001',
                'shipping_expedition' => 'JNE',
            ],
            'member@gmail.com' => [
                'phone' => '082198765432',
                'address' => 'Jl. Member Setia No. 45',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'postal_code' => '40111',
                'seller_id' => null,
                'shipping_expedition' => 'J&T',
            ],
            'customer@gmail.com' => [
                'phone' => '085712345678',
                'address' => 'Jl. Mawar No. 12',
                'city' => 'Surabaya',
                'province' => 'Jawa Timur',
                'postal_code' => '60111',
                'seller_id' => null,
                'shipping_expedition' => 'SiCepat',
            ],
            'zahwaayurmdhni@gmail.com' => [
                'phone' => '089654321098',
                'address' => 'Jl. Utama Admin No. 8',
                'city' => 'Yogyakarta',
                'province' => 'DI Yogyakarta',
                'postal_code' => '55281',
                'seller_id' => 'ADM-002',
                'shipping_expedition' => 'Pos Indonesia',
            ],
        ];

        foreach ($userDetails as $email => $detail) {
            $user = User::where('email', $email)->first();

            if ($user) {
                UserDetail::firstOrCreate(
                    ['user_id' => $user->id],
                    $detail
                );
            }
        }
    }
}