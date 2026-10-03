<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::firstOrCreate(['name' => 'Default Category']);

        $products = [
            [
                'name'         => 'Baju Koko Premium Zakira',
                'description'  => 'Baju koko bahan katun premium, adem dan nyaman dipakai harian maupun acara.',
                'product_type' => 'ready',
                'weight_grams' => 400,
                'product_note' => 'Bahan tidak luntur, jahitan rapi.',
                'colors'       => [
                    ['name' => 'Putih', 'hex_code' => '#FFFFFF'],
                    ['name' => 'Hitam', 'hex_code' => '#000000'],
                    ['name' => 'Navy',  'hex_code' => '#1F2A44'],
                ],
                'models'       => [
                    ['name' => 'Lengan Panjang', 'description' => 'Kerah shanghai'],
                    ['name' => 'Lengan Pendek',  'description' => 'Kerah shanghai'],
                ],
                'sizes'        => ['M', 'L', 'XL', 'XXL'],
                // harga per [model][ukuran]
                'prices'       => [
                    'Lengan Panjang' => ['M' => 185000, 'L' => 185000, 'XL' => 195000, 'XXL' => 205000],
                    'Lengan Pendek'  => ['M' => 165000, 'L' => 165000, 'XL' => 175000, 'XXL' => 185000],
                ],
                'free_items'   => [
                    ['name' => 'Peci Hitam', 'quantity' => 1, 'model' => 'Lengan Panjang'],
                ],
                'price_rules'  => [
                    ['label' => 'Tambahan warna Navy', 'type' => 'add', 'amount' => 10000, 'color' => 'Navy'],
                ],
            ],
            [
                'name'         => 'Hijab Pashmina Plisket',
                'description'  => 'Pashmina plisket tidak perlu disetrika, jatuh dan ringan.',
                'product_type' => 'ready',
                'weight_grams' => 150,
                'product_note' => null,
                'colors'       => [
                    ['name' => 'Dusty Pink', 'hex_code' => '#D8A7A7'],
                    ['name' => 'Mocca',      'hex_code' => '#8B6B4F'],
                    ['name' => 'Sage',       'hex_code' => '#9CAF88'],
                ],
                'models'       => [
                    ['name' => 'Pashmina Plisket', 'description' => null],
                ],
                'sizes'        => ['All Size'],
                'prices'       => [
                    'Pashmina Plisket' => ['All Size' => 79000],
                ],
                'free_items'   => [],
                'price_rules'  => [],
            ],
            [
                'name'         => "Gamis Syar'i Zakira",
                'description'  => "Gamis syar'i bahan wolfis, tersedia pre-order.",
                'product_type' => 'po',
                'weight_grams' => 700,
                'product_note' => 'Estimasi produksi 14 hari kerja.',
                'colors'       => [
                    ['name' => 'Hitam',  'hex_code' => '#000000'],
                    ['name' => 'Maroon', 'hex_code' => '#6D1F2C'],
                ],
                'models'       => [
                    ['name' => 'Polos',     'description' => null],
                    ['name' => 'Bordir',    'description' => 'Bordir di dada dan lengan'],
                ],
                'sizes'        => ['S', 'M', 'L', 'XL'],
                'prices'       => [
                    'Polos'  => ['S' => 245000, 'M' => 245000, 'L' => 255000, 'XL' => 265000],
                    'Bordir' => ['S' => 285000, 'M' => 285000, 'L' => 295000, 'XL' => 305000],
                ],
                'free_items'   => [
                    ['name' => 'Pashmina Instan', 'quantity' => 1],
                ],
                'price_rules'  => [
                    ['label' => 'Potongan ukuran S', 'type' => 'cut', 'amount' => 5000, 'size' => 'S'],
                ],
            ],
        ];

        DB::transaction(function () use ($products, $category) {
            foreach ($products as $data) {
                // Lewati jika produk dengan nama yang sama sudah ada (aman dijalankan berulang)
                if (DB::table('products')->where('name', $data['name'])->exists()) {
                    continue;
                }

                $now = now();

                $productId = DB::table('products')->insertGetId([
                    'name'             => $data['name'],
                    'description'      => $data['description'],
                    'category_id'      => $category->id,
                    'is_active'        => true,
                    'product_type'     => $data['product_type'],
                    'weight_grams'     => $data['weight_grams'],
                    'product_note'     => $data['product_note'],
                    'show_public'      => $data['product_type'] === 'ready', // PO hanya untuk member
                    'show_member'      => true,
                    'show_distributor' => false,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]);

                // Pivot kategori
                DB::table('product_categories')->insert([
                    'product_id'  => $productId,
                    'category_id' => $category->id,
                ]);

                // Warna
                $colorIds = [];
                foreach ($data['colors'] as $color) {
                    $colorIds[$color['name']] = DB::table('product_colors')->insertGetId([
                        'product_id' => $productId,
                        'name'       => $color['name'],
                        'hex_code'   => $color['hex_code'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                // Model
                $modelIds = [];
                foreach ($data['models'] as $model) {
                    $modelIds[$model['name']] = DB::table('product_models')->insertGetId([
                        'product_id'  => $productId,
                        'name'        => $model['name'],
                        'description' => $model['description'],
                        'created_at'  => $now,
                        'updated_at'  => $now,
                    ]);
                }

                // Ukuran
                $sizeIds = [];
                foreach ($data['sizes'] as $size) {
                    $sizeIds[$size] = DB::table('product_sizes')->insertGetId([
                        'product_id'   => $productId,
                        'size'         => $size,
                        'is_available' => true,
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ]);
                }

                // Matriks harga (model x ukuran)
                foreach ($data['prices'] as $modelName => $sizePrices) {
                    foreach ($sizePrices as $sizeName => $price) {
                        DB::table('product_prices')->insert([
                            'product_id'       => $productId,
                            'product_model_id' => $modelIds[$modelName],
                            'product_size_id'  => $sizeIds[$sizeName],
                            'price'            => $price,
                            'created_at'       => $now,
                            'updated_at'       => $now,
                        ]);
                    }
                }

                // Free item / bonus
                foreach ($data['free_items'] as $item) {
                    DB::table('product_free_items')->insert([
                        'product_id'       => $productId,
                        'name'             => $item['name'],
                        'quantity'         => $item['quantity'],
                        'product_color_id' => isset($item['color']) ? $colorIds[$item['color']] : null,
                        'product_model_id' => isset($item['model']) ? $modelIds[$item['model']] : null,
                        'product_size_id'  => isset($item['size'])  ? $sizeIds[$item['size']]   : null,
                        'created_at'       => $now,
                        'updated_at'       => $now,
                    ]);
                }

                // Aturan harga otomatis (tambah / potong)
                foreach ($data['price_rules'] as $rule) {
                    DB::table('product_price_rules')->insert([
                        'product_id'       => $productId,
                        'label'            => $rule['label'],
                        'type'             => $rule['type'],
                        'amount'           => $rule['amount'],
                        'product_color_id' => isset($rule['color']) ? $colorIds[$rule['color']] : null,
                        'product_model_id' => isset($rule['model']) ? $modelIds[$rule['model']] : null,
                        'product_size_id'  => isset($rule['size'])  ? $sizeIds[$rule['size']]   : null,
                        'created_at'       => $now,
                        'updated_at'       => $now,
                    ]);
                }
            }
        });
    }
}