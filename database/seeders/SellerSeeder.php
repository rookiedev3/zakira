<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Seller;
use Illuminate\Database\Seeder;

class SellerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mengambil ID brand yang ada atau menggunakan ID 1 & 2 sebagai fallback
        $firstBrandId = Brand::first()?->id ?? 1;
        $secondBrandId = Brand::skip(1)->first()?->id ?? $firstBrandId;

        $sellers = [
            [
                'seller_id' => 'SLR-001',
                'name'      => 'Seller Utama',
                'brand_id'  => $firstBrandId,
            ],
            [
                'seller_id' => 'SLR-002',
                'name'      => 'Seller Cabang',
                'brand_id'  => $secondBrandId,
            ],
        ];

        foreach ($sellers as $seller) {
            Seller::firstOrCreate(
                ['seller_id' => $seller['seller_id']],
                $seller
            );
        }
    }
}