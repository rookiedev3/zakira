<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Menampilkan daftar kategori.
     */
    public function index()
    {
        // Data tiruan (hardcode) menyesuaikan tampilan sebelumnya
        $categories = [
            [
                'id' => 5,
                'name' => 'Almet1',
                'type' => 'Wanita',
                'description' => 'almet pnc1',
                'products_count' => 1,
                'is_active' => false,
            ],
            [
                'id' => 3,
                'name' => 'LUMINA',
                'type' => 'Wanita',
                'description' => null,
                'products_count' => 1,
                'is_active' => true,
            ],
            [
                'id' => 2,
                'name' => 'SAFIA',
                'type' => 'Wanita',
                'description' => null,
                'products_count' => 0,
                'is_active' => true,
            ],
            [
                'id' => 1,
                'name' => 'YURA KIMONO',
                'type' => 'Wanita',
                'description' => 'ABAYA ONLY',
                'products_count' => 1,
                'is_active' => true,
            ],
        ];

        return view('categories.index', compact('categories'));
    }

    /**
     * Menampilkan form tambah kategori.
     */
    public function create()
    {
        return view('categories.create'); // Buat view create jika diperlukan
    }

    /**
     * Menyimpan kategori baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        // Logic simpan data ke database (contoh: Category::create($request->all());)

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit kategori.
     */
    public function edit($id)
    {
        // Logic ambil data berdasarkan $id
        return view('categories.edit', compact('id'));
    }

    /**
     * Memperbarui data kategori.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        // Logic update data

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Mengubah status aktif / non-aktif kategori.
     */
    public function toggleStatus($id)
    {
        // Logic ubah status aktif/non-aktif di database

        return redirect()->route('categories.index')->with('success', 'Status kategori berhasil diubah.');
    }

    /**
     * Menghapus kategori.
     */
    public function destroy($id)
    {
        // Logic hapus data

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}