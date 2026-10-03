<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            UserDetailSeeder::class,
            CsSeeder::class,
            BrandSeeder::class,
            SellerSeeder::class, // Dijalankan setelah BrandSeeder karena butuh brand_id
            CategorySeeder::class,
            BannerSeeder::class,
            AdvantageSeeder::class,
            SocialMediaSeeder::class,
            AdminHandleSeeder::class,
            BankAccountSeeder::class,
            ProductSeeder::class,
        ]);
    }
}