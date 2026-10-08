<?php

namespace Tests\Feature;

use App\Http\Controllers\CartController;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductModel;
use App\Models\ProductSize;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandDpTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminUser(): User
    {
        return User::create([
            'name'     => 'Admin Test',
            'email'    => 'admin@example.com',
            'password' => bcrypt('password'),
            'role'     => 'admin',
            'status'   => 'aktif',
        ]);
    }

    public function test_can_view_brand_create_page_with_dp_percentage_field(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get(route('brands.create'));

        $response->assertStatus(200);
        $response->assertSee('Persentase DP (%)');
        $response->assertSee('name="dp_percentage"', false);
    }

    public function test_can_create_brand_with_custom_dp_percentage(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->post(route('brands.store'), [
            'name'          => 'Brand Hijab Mewah',
            'dp_percentage' => 45.5,
            'description'   => 'Deskripsi brand mewah',
        ]);

        $response->assertRedirect(route('brands.index'));

        $this->assertDatabaseHas('brands', [
            'name'          => 'Brand Hijab Mewah',
            'dp_percentage' => 45.5,
        ]);
    }

    public function test_creating_brand_without_dp_percentage_defaults_to_30(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->post(route('brands.store'), [
            'name' => 'Brand Standar',
        ]);

        $response->assertRedirect(route('brands.index'));

        $this->assertDatabaseHas('brands', [
            'name'          => 'Brand Standar',
            'dp_percentage' => 30.00,
        ]);
    }

    public function test_dp_percentage_validation_fails_for_invalid_range(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->post(route('brands.store'), [
            'name'          => 'Brand Invalid',
            'dp_percentage' => 150,
        ]);

        $response->assertSessionHasErrors('dp_percentage');
    }

    public function test_can_update_brand_dp_percentage(): void
    {
        $admin = $this->createAdminUser();
        $brand = Brand::create([
            'name'          => 'Brand Update Test',
            'dp_percentage' => 25,
            'home_order'    => 1,
        ]);

        $response = $this->actingAs($admin)->put(route('brands.update', $brand->id), [
            'name'          => 'Brand Update Test',
            'dp_percentage' => 50,
        ]);

        $response->assertRedirect(route('brands.index'));

        $this->assertDatabaseHas('brands', [
            'id'            => $brand->id,
            'dp_percentage' => 50,
        ]);
    }

    public function test_can_view_brand_edit_page_with_dp_percentage(): void
    {
        $admin = $this->createAdminUser();
        $brand = Brand::create([
            'name'          => 'Brand Edit Test',
            'dp_percentage' => 35,
            'home_order'    => 1,
        ]);

        $response = $this->actingAs($admin)->get(route('brands.edit', $brand->id));

        $response->assertStatus(200);
        $response->assertSee('Persentase DP (%)');
        $response->assertSee('value="35"', false);
    }

    public function test_cart_calculates_dp_from_product_brand(): void
    {
        $brand = Brand::create([
            'name'          => 'Brand Custom DP',
            'dp_percentage' => 40,
            'home_order'    => 1,
        ]);

        $product = Product::create([
            'name'         => 'Gamis Khimar',
            'brand_id'     => $brand->id,
            'product_type' => 'ready',
            'is_active'    => true,
            'weight_grams' => 500,
        ]);

        $model = ProductModel::create([
            'product_id' => $product->id,
            'name'       => 'Model A',
        ]);

        $size = ProductSize::create([
            'product_id' => $product->id,
            'size'       => 'All Size',
            'stock'      => 10,
        ]);

        $product->prices()->create([
            'product_model_id' => $model->id,
            'product_size_id'  => $size->id,
            'price'            => 200000,
        ]);

        session([
            'cart' => [
                'item_1' => [
                    'product_id' => $product->id,
                    'quantity'   => 1,
                    'model_id'   => $model->id,
                    'color_id'   => null,
                    'size_id'    => $size->id,
                ],
            ],
            'payment_method' => 'dp',
        ]);

        $cartController = app(CartController::class);
        $payload = $cartController->payload();

        $this->assertEquals(200000, $payload['total']);
        $this->assertEquals(40, $payload['dp_percent']);
        $this->assertEquals(80000, $payload['dp_amount']);
        $this->assertEquals('Rp 80.000', $payload['dp_formatted']);
        $this->assertEquals('Rp 120.000', $payload['remaining_formatted']);
    }
}
