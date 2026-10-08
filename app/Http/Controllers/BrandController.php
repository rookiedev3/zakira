<?php
// app/Http/Controllers/BrandController.php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    /* --------------------------------------------------------------
       Helper: data tabel (dipakai index, create, edit karena
       semuanya meng-include brands/_list)
    -------------------------------------------------------------- */
    private function brandList()
    {
        return Brand::withCount('products')
            ->orderBy('home_order')
            ->orderBy('id')
            ->paginate(10);
    }

    /* --------------------------------------------------------------
       INDEX – tabel brand
    -------------------------------------------------------------- */
    public function index()
    {
        return view('brands.index', [
            'brands' => $this->brandList(),
        ]);
    }

    /* --------------------------------------------------------------
       CREATE / STORE
    -------------------------------------------------------------- */
    public function create()
    {
        return view('brands.create', [
            'brands'    => $this->brandList(),
            'nextOrder' => Brand::count() + 1,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255|unique:brands,name',
            'dp_percentage' => 'nullable|numeric|min:0|max:100',
            'description'   => 'nullable|string|max:1000',
            'logo'          => 'nullable|image|max:2048',
            'home_order'    => 'nullable|integer|min:1|max:2147483647',
            'show_on_home'  => 'nullable|boolean',
            'is_active'     => 'nullable|boolean',
        ]);

        $data = [
            'name'          => $validated['name'],
            'dp_percentage' => isset($validated['dp_percentage']) && $validated['dp_percentage'] !== '' ? (float) $validated['dp_percentage'] : 30.00,
            'description'   => $validated['description'] ?? null,
            'home_order'    => 1, // sementara, ditentukan ulang oleh placeAt()
            'show_on_home'  => $request->boolean('show_on_home'),
            'is_active'     => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('brand-logos', 'public');
        }

        DB::transaction(function () use ($data, $validated) {
            $brand = Brand::create($data);

            // Kosong => taruh di urutan paling akhir
            $this->placeAt($brand, (int) ($validated['home_order'] ?? PHP_INT_MAX));
        });

        return redirect()->route('brands.index')
            ->with('success', 'Brand berhasil ditambahkan.');
    }

    /* --------------------------------------------------------------
       EDIT / UPDATE
    -------------------------------------------------------------- */
    public function edit($id)
    {
        return view('brands.edit', [
            'brand'  => Brand::findOrFail($id),
            'brands' => $this->brandList(),
        ]);
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $brand = Brand::findOrFail($id);

        $validated = $request->validate([
            'name'          => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'dp_percentage' => 'nullable|numeric|min:0|max:100',
            'description'   => 'nullable|string|max:1000',
            'logo'          => 'nullable|image|max:2048',
            'home_order'    => 'nullable|integer|min:1|max:2147483647',
            'show_on_home'  => 'nullable|boolean',
            'is_active'     => 'nullable|boolean',
        ]);

        $data = [
            'name'          => $validated['name'],
            'dp_percentage' => isset($validated['dp_percentage']) && $validated['dp_percentage'] !== '' ? (float) $validated['dp_percentage'] : 30.00,
            'description'   => $validated['description'] ?? null,
            'show_on_home'  => $request->boolean('show_on_home'),
            'is_active'     => $request->boolean('is_active'),
        ];

        // Ganti logo: hapus file lama setelah yang baru berhasil disimpan
        if ($request->hasFile('logo')) {
            $newLogo = $request->file('logo')->store('brand-logos', 'public');

            if ($brand->logo) {
                Storage::disk('public')->delete($brand->logo);
            }

            $data['logo'] = $newLogo;
        }

        DB::transaction(function () use ($brand, $data, $validated) {
            $brand->update($data);

            // Kosong => tetap di posisi sekarang
            $this->placeAt($brand, (int) ($validated['home_order'] ?? $brand->home_order));
        });

        return redirect()->route('brands.index')
            ->with('success', 'Brand berhasil diperbarui.');
    }

    /* --------------------------------------------------------------
       DESTROY
    -------------------------------------------------------------- */
    public function destroy($id): RedirectResponse
    {
        $brand = Brand::findOrFail($id);

        if ($brand->logo) {
            Storage::disk('public')->delete($brand->logo);
        }

        // products.brand_id memakai nullOnDelete, jadi produk tidak ikut terhapus
        DB::transaction(function () use ($brand) {
            $brand->delete();
            $this->normalizeOrder(); // tutup lubang urutan
        });

        return redirect()->route('brands.index')
            ->with('success', 'Brand berhasil dihapus.');
    }

    /* --------------------------------------------------------------
       TOGGLE "Tampil di Homepage" (tombol Tampil / Sembunyi)
    -------------------------------------------------------------- */
    public function toggleHome($id): RedirectResponse
    {
        $brand = Brand::findOrFail($id);
        $brand->update(['show_on_home' => ! $brand->show_on_home]);

        return back()->with('success', $brand->show_on_home
            ? "Brand {$brand->name} ditampilkan di homepage."
            : "Brand {$brand->name} disembunyikan dari homepage.");
    }

    /* --------------------------------------------------------------
       TOGGLE STATUS (Aktifkan / Nonaktifkan)
    -------------------------------------------------------------- */
    public function toggleStatus($id): RedirectResponse
    {
        $brand = Brand::findOrFail($id);
        $brand->update(['is_active' => ! $brand->is_active]);

        return back()->with('success', $brand->is_active
            ? "Brand {$brand->name} diaktifkan."
            : "Brand {$brand->name} dinonaktifkan.");
    }

    /* --------------------------------------------------------------
       NAIK / TURUN URUTAN
       Menukar posisi dengan brand tetangga. Seluruh urutan dirapikan
       jadi 1..n lebih dulu, supaya tetap jalan walau ada nilai
       home_order yang kembar (misalnya semua masih 0).
    -------------------------------------------------------------- */
    public function moveOrder($id, string $direction): RedirectResponse
    {
        $brand = Brand::findOrFail($id);
        $moved = false;

        DB::transaction(function () use ($brand, $direction, &$moved) {
            $ordered = Brand::orderBy('home_order')
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->all();

            $index  = array_search($brand->id, array_column($ordered, 'id'));
            $target = $direction === 'up' ? $index - 1 : $index + 1;

            if (isset($ordered[$target])) {
                [$ordered[$index], $ordered[$target]] = [$ordered[$target], $ordered[$index]];
                $moved = true;
            }

            $this->saveOrder($ordered);
        });

        return $moved
            ? back()->with('success', 'Urutan brand diperbarui.')
            : back()->with('success', $direction === 'up'
                ? 'Brand sudah berada di urutan paling atas.'
                : 'Brand sudah berada di urutan paling bawah.');
    }

    /* --------------------------------------------------------------
       Helper urutan (selalu 1..n, tanpa duplikat, tanpa lubang)
    -------------------------------------------------------------- */

    /**
     * Sisipkan brand di posisi tertentu (1-based), geser brand lain,
     * lalu rapikan jadi 1..n. Posisi di luar rentang otomatis dibatasi.
     */
    private function placeAt(Brand $brand, int $position): void
    {
        $others = Brand::where('id', '!=', $brand->id)
            ->orderBy('home_order')
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->all();

        $position = max(1, min($position, count($others) + 1));

        array_splice($others, $position - 1, 0, [$brand]);

        $this->saveOrder($others);
    }

    private function normalizeOrder(): void
    {
        $this->saveOrder(
            Brand::orderBy('home_order')->orderBy('id')->lockForUpdate()->get()->all()
        );
    }

    private function saveOrder(array $ordered): void
    {
        foreach ($ordered as $i => $item) {
            $newOrder = $i + 1;

            if ((int) $item->home_order !== $newOrder) {
                Brand::whereKey($item->id)->update(['home_order' => $newOrder]);
            }
        }
    }
}