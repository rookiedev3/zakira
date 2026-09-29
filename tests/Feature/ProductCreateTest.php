<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_product_create_page()
    {
        $response = $this->get(route('products.create'));
        $response->assertStatus(200);
        $response->assertSee('Buat Produk');
    }

    public function test_can_create_product_successfully_with_images()
    {
        Storage::fake('public');

        $brand = Brand::firstOrCreate(
            ['name' => 'Brand Test'],
            ['is_active' => true]
        );
        $category = Category::firstOrCreate(
            ['name' => 'Kategori Test'],
            ['is_active' => true]
        );

        $payload = [
            'name'             => 'Produk Gamis Syari',
            'brand_id'         => $brand->id,
            'is_active'        => 1,
            'product_type'     => 'ready',
            'show_public'      => 1,
            'show_member'      => 1,
            'show_distributor' => 0,
            'weight_grams'     => 450,
            'product_note'     => 'Catatan pengiriman',
            'description'      => 'Deskripsi gamis syari',
            'category_ids'     => [$category->id],
            'colors'           => [
                [
                    'name'     => 'Hitam',
                    'hex_code' => '#000000',
                    'image'    => UploadedFile::fake()->image('color_hitam.jpg'),
                ],
                [
                    'name'     => 'Maroon',
                    'hex_code' => '#800000',
                ],
            ],
            'models'           => [
                [
                    'name'        => 'Standard',
                    'description' => 'Model polos',
                    'image'       => UploadedFile::fake()->image('model_standard.jpg'),
                ],
                [
                    'name'        => 'Renda',
                    'description' => 'Model aksen renda',
                ],
            ],
            'sizes'            => [
                ['size' => 'M', 'available' => 1],
                ['size' => 'XL', 'available' => 1],
            ],
            'prices'           => [
                0 => [0 => 120000, 1 => 130000],
                1 => [0 => 140000, 1 => 150000],
            ],
            'general_images'   => [
                UploadedFile::fake()->image('product_cover.jpg'),
                UploadedFile::fake()->image('product_detail.jpg'),
            ],
            'free_items'       => [
                ['name' => 'Bros Cantik', 'quantity' => 1, 'color' => null, 'model' => null, 'size' => null],
            ],
            'price_rules'      => [
                ['type' => 'add', 'amount' => 15000, 'label' => 'Jumbo XL', 'color' => null, 'model' => null, 'size' => 1],
            ],
        ];

        $response = $this->post(route('products.store'), $payload);

        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'name' => 'Produk Gamis Syari',
            'brand_id' => $brand->id,
            'weight_grams' => 450,
        ]);

        $product = Product::where('name', 'Produk Gamis Syari')->first();
        $this->assertNotNull($product);
        $this->assertCount(2, $product->colors);
        $this->assertCount(2, $product->models);
        $this->assertCount(2, $product->sizes);
        $this->assertCount(4, $product->prices);
        $this->assertCount(2, $product->images);
        $this->assertCount(1, $product->freeItems);
        $this->assertCount(1, $product->priceRules);
        $this->assertCount(1, $product->categories);

        $this->assertNotNull($product->image);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_validation_fails_when_required_fields_missing()
    {
        $response = $this->post(route('products.store'), []);

        $response->assertSessionHasErrors(['name', 'brand_id', 'colors', 'models', 'sizes', 'prices']);
    }

    public function test_validation_fails_when_prices_incomplete()
    {
        $brand = Brand::firstOrCreate(
            ['name' => 'Brand Test'],
            ['is_active' => true]
        );

        $payload = [
            'name'         => 'Produk Gamis',
            'brand_id'     => $brand->id,
            'is_active'    => 1,
            'product_type' => 'ready',
            'weight_grams' => 500,
            'colors'       => [['name' => 'Hitam']],
            'models'       => [['name' => 'M1'], ['name' => 'M2']],
            'sizes'        => [['size' => 'S'], ['size' => 'M']],
            'prices'       => [
                0 => [0 => 100000], // baris 1, ukuran M tidak diisi
            ],
        ];

        $response = $this->post(route('products.store'), $payload);
        $response->assertSessionHasErrors(['prices']);
    }
}
