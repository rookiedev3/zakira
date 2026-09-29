<?php
// app/Http/Controllers/ProductController.php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /* --------------------------------------------------------------
       INDEX – tabel produk + filter (search, merek, kategori, tipe, status)
    -------------------------------------------------------------- */
    public function index(Request $request)
    {
        $status = $request->query('status');

        $products = Product::with(['brand', 'categories', 'colors'])
            ->withCount(['colors', 'models'])
            ->withMin('prices', 'price')
            ->withMax('prices', 'price')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->query('search') . '%');
            })
            ->when($request->filled('brand'), function ($q) use ($request) {
                $q->where('brand_id', $request->query('brand'));
            })
            ->when($request->filled('category'), function ($q) use ($request) {
                $q->whereHas('categories', function ($c) use ($request) {
                    $c->where('categories.id', $request->query('category'));
                });
            })
            ->when(in_array($request->query('type'), ['ready', 'po'], true), function ($q) use ($request) {
                $q->where('product_type', $request->query('type'));
            })
            ->when(in_array($status, ['0', '1'], true), function ($q) use ($status) {
                $q->where('is_active', (bool) $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('products.index', [
            'products'        => $products,
            'brandOptions'    => Brand::orderBy('name')->get(['id', 'name']),
            'categoryOptions' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /* --------------------------------------------------------------
       CREATE – form Buat Produk
    -------------------------------------------------------------- */
    public function create()
    {
        return view('products.create', [
            'brands'     => Brand::active()->orderBy('name')->get(['id', 'name']),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /* --------------------------------------------------------------
       STORE – simpan produk + warna, model, ukuran, harga, gambar,
       FREE item, dan harga otomatis dalam satu transaksi
    -------------------------------------------------------------- */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules(), $this->messages(), $this->attributes());

        // Matriks harga harus lengkap: semua kombinasi model x ukuran terisi
        $modelCount = count($data['models']);
        $sizeCount  = count($data['sizes']);

        for ($m = 0; $m < $modelCount; $m++) {
            for ($s = 0; $s < $sizeCount; $s++) {
                if (! isset($data['prices'][$m][$s])) {
                    throw ValidationException::withMessages([
                        'prices' => 'Harga untuk semua kombinasi model dan ukuran wajib diisi.',
                    ]);
                }
            }
        }

        // Catat file yang sudah diupload supaya bisa dibersihkan jika transaksi gagal
        $stored = [];
        $put = function ($file, string $dir) use (&$stored) {
            $path = $file->store($dir, 'public');
            $stored[] = $path;

            return $path;
        };

        // Ambil id varian dari indeks yang dikirim form (kosong = berlaku semua)
        $pick = function (array $map, $index) {
            return ($index !== null && $index !== '' && isset($map[(int) $index]))
                ? $map[(int) $index]
                : null;
        };

        try {
            $product = DB::transaction(function () use ($request, $data, $put, $pick) {
                $product = Product::create([
                    'name'             => $data['name'],
                    'brand_id'         => $data['brand_id'],
                    'category_id'      => ! empty($data['category_ids']) ? $data['category_ids'][0] : null,
                    'description'      => $data['description'] ?? null,
                    'is_active'        => (bool) $data['is_active'],
                    'product_type'     => $data['product_type'],
                    'show_public'      => $request->boolean('show_public'),
                    'show_member'      => $request->boolean('show_member'),
                    'show_distributor' => $request->boolean('show_distributor'),
                    'weight_grams'     => $data['weight_grams'],
                    'product_note'     => $data['product_note'] ?? null,
                ]);

                $product->categories()->sync($data['category_ids'] ?? []);

                // Warna
                $colorIds = [];
                foreach ($data['colors'] as $i => $color) {
                    $hex = ! empty($color['hex_code'])
                        ? '#' . strtoupper(ltrim($color['hex_code'], '#'))
                        : null;

                    $colorIds[$i] = $product->colors()->create([
                        'name'     => $color['name'],
                        'hex_code' => $hex,
                        'image'    => $request->hasFile("colors.$i.image")
                            ? $put($request->file("colors.$i.image"), 'product-colors')
                            : null,
                    ])->id;
                }

                // Model
                $modelIds = [];
                foreach ($data['models'] as $i => $model) {
                    $modelIds[$i] = $product->models()->create([
                        'name'        => $model['name'],
                        'description' => $model['description'] ?? null,
                        'image'       => $request->hasFile("models.$i.image")
                            ? $put($request->file("models.$i.image"), 'product-models')
                            : null,
                    ])->id;
                }

                // Ukuran
                $sizeIds = [];
                foreach ($data['sizes'] as $i => $size) {
                    $sizeIds[$i] = $product->sizes()->create([
                        'size'         => $size['size'],
                        'is_available' => $request->boolean("sizes.$i.available"),
                    ])->id;
                }

                // Matriks harga
                foreach ($data['prices'] as $m => $row) {
                    foreach ($row as $s => $price) {
                        if (isset($modelIds[$m], $sizeIds[$s])) {
                            $product->prices()->create([
                                'product_model_id' => $modelIds[$m],
                                'product_size_id'  => $sizeIds[$s],
                                'price'            => (int) $price,
                            ]);
                        }
                    }
                }

                // Gambar produk umum (yang pertama jadi gambar utama)
                foreach ($request->file('general_images', []) as $n => $file) {
                    $path = $put($file, 'product-images');

                    $product->images()->create([
                        'path'       => $path,
                        'is_primary' => $n === 0,
                        'sort_order' => $n,
                    ]);

                    if ($n === 0) {
                        $product->update(['image' => $path]);
                    }
                }

                // FREE barang / bonus
                foreach ($data['free_items'] ?? [] as $item) {
                    $product->freeItems()->create([
                        'name'             => $item['name'],
                        'quantity'         => $item['quantity'],
                        'product_color_id' => $pick($colorIds, $item['color'] ?? null),
                        'product_model_id' => $pick($modelIds, $item['model'] ?? null),
                        'product_size_id'  => $pick($sizeIds, $item['size'] ?? null),
                    ]);
                }

                // Harga otomatis (tambah / potong)
                foreach ($data['price_rules'] ?? [] as $rule) {
                    $product->priceRules()->create([
                        'label'            => $rule['label'] ?? null,
                        'type'             => $rule['type'],
                        'amount'           => (int) $rule['amount'],
                        'product_color_id' => $pick($colorIds, $rule['color'] ?? null),
                        'product_model_id' => $pick($modelIds, $rule['model'] ?? null),
                        'product_size_id'  => $pick($sizeIds, $rule['size'] ?? null),
                    ]);
                }

                return $product;
            });
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($stored);

            throw $e;
        }

        return redirect()->route('products.index')
            ->with('success', "Produk {$product->name} berhasil dibuat.");
    }

    /* --------------------------------------------------------------
       TOGGLE STATUS (Aktifkan / Nonaktifkan)
    -------------------------------------------------------------- */
    public function toggleStatus($id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $product->update(['is_active' => ! $product->is_active]);

        return back()->with('success', $product->is_active
            ? "Produk {$product->name} diaktifkan."
            : "Produk {$product->name} dinonaktifkan.");
    }

    /* --------------------------------------------------------------
       DESTROY
    -------------------------------------------------------------- */
    public function destroy($id): RedirectResponse
    {
        $product = Product::with(['colors', 'models', 'images'])->findOrFail($id);

        $files = array_filter(array_merge(
            [$product->image],
            $product->colors->pluck('image')->all(),
            $product->models->pluck('image')->all(),
            $product->images->pluck('path')->all(),
        ));

        // Baris turunan (warna, model, harga, dst.) ikut terhapus lewat cascade
        $product->delete();

        Storage::disk('public')->delete($files);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    /* --------------------------------------------------------------
       Aturan validasi form Buat Produk
    -------------------------------------------------------------- */
    private function rules(): array
    {
        return [
            'name'             => 'required|string|max:255',
            'brand_id'         => 'required|exists:brands,id',
            'is_active'        => 'required|boolean',
            'product_type'     => 'required|in:ready,po',
            'show_public'      => 'nullable|boolean',
            'show_member'      => 'nullable|boolean',
            'show_distributor' => 'nullable|boolean',
            'weight_grams'     => 'required|integer|min:1',
            'product_note'     => 'nullable|string|max:1000',
            'description'      => 'nullable|string',
            'category_ids'     => 'nullable|array',
            'category_ids.*'   => 'exists:categories,id',

            'colors'              => 'required|array|min:1',
            'colors.*.name'       => 'required|string|max:100',
            'colors.*.hex_code'   => ['nullable', 'regex:/^#?([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})$/'],
            'colors.*.image'      => 'nullable|image|max:2048',

            'models'                => 'required|array|min:1',
            'models.*.name'         => 'required|string|max:100',
            'models.*.description'  => 'nullable|string|max:255',
            'models.*.image'        => 'nullable|image|max:2048',

            'sizes'             => 'required|array|min:1',
            'sizes.*.size'      => 'required|string|max:50',
            'sizes.*.available' => 'nullable|boolean',

            'prices'     => 'required|array',
            'prices.*'   => 'array',
            'prices.*.*' => 'required|integer|min:0',

            'general_images'   => 'nullable|array',
            'general_images.*' => 'image|max:4096',

            'free_items'            => 'nullable|array',
            'free_items.*.name'     => 'required|string|max:255',
            'free_items.*.quantity' => 'required|integer|min:1',
            'free_items.*.color'    => 'nullable|integer|min:0',
            'free_items.*.model'    => 'nullable|integer|min:0',
            'free_items.*.size'     => 'nullable|integer|min:0',

            'price_rules'          => 'nullable|array',
            'price_rules.*.type'   => 'required|in:add,cut',
            'price_rules.*.amount' => 'required|integer|min:1',
            'price_rules.*.label'  => 'nullable|string|max:255',
            'price_rules.*.color'  => 'nullable|integer|min:0',
            'price_rules.*.model'  => 'nullable|integer|min:0',
            'price_rules.*.size'   => 'nullable|integer|min:0',
        ];
    }

    private function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'integer'  => ':attribute harus berupa angka.',
            'image'    => ':attribute harus berupa gambar.',
            'exists'   => ':attribute tidak valid.',
            'in'       => ':attribute tidak valid.',
            'regex'    => 'Format :attribute tidak valid (contoh: #FF0000).',
            'min.array' => ':attribute minimal satu.',
        ];
    }

    private function attributes(): array
    {
        return [
            'name'                  => 'Nama produk',
            'brand_id'              => 'Merek',
            'is_active'             => 'Status',
            'product_type'          => 'Jenis produk',
            'weight_grams'          => 'Berat produk',
            'colors'                => 'Warna',
            'colors.*.name'         => 'Nama warna',
            'colors.*.hex_code'     => 'Kode hex',
            'colors.*.image'        => 'Gambar warna',
            'models'                => 'Model',
            'models.*.name'         => 'Nama model',
            'models.*.image'        => 'Gambar model',
            'sizes'                 => 'Ukuran',
            'sizes.*.size'          => 'Ukuran',
            'prices'                => 'Harga produk',
            'prices.*.*'            => 'Harga',
            'general_images.*'      => 'Gambar produk umum',
            'free_items.*.name'     => 'Nama FREE item',
            'free_items.*.quantity' => 'Jumlah FREE item',
            'price_rules.*.type'    => 'Jenis rule harga',
            'price_rules.*.amount'  => 'Nominal rule harga',
        ];
    }
}