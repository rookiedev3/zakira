<?php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Menampilkan daftar kategori.
     */
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    /**
     * Menampilkan form tambah kategori **beserta** daftar kategori yang sudah ada.
     */
    public function create()
    {
        // Ambil semua kategori supaya dapat ditampilkan di halaman create
        $categories = Category::all();
        return view('categories.create', compact('categories'));
    }

    /**
     * Menyimpan kategori baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        Category::create([
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? $request->is_active : true,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori Successfully ditambahkan.');
    }

    /**
     * Menampilkan form edit kategori.
     */
    public function edit($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return redirect()->route('categories.index')
                ->with('error', 'Kategori tidak ditemukan.');
        }

        // Tambahkan semua kategori untuk ditampilkan di partial _list
        $categories = Category::all();
        return view('categories.edit', compact('category', 'categories'));
    }

    /**
     * Memperbarui data kategori.
     */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return redirect()->route('categories.index')
                ->with('error', 'Kategori tidak ditemukan.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $category->update([
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? $request->is_active : true,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori Successfully diperbarui.');
    }

    /**
     * Mengubah status aktif / non‑aktif kategori.
     */
    public function toggleStatus($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return redirect()->route('categories.index')
                ->with('error', 'Kategori tidak ditemukan.');
        }

        $category->update([
            'is_active' => !$category->is_active,
        ]);

        // Jika status menjadi **aktif**, tampilkan pesan sukses.
        if ($category->is_active) {
            return redirect()->route('categories.index')
                ->with('success', 'Category status updated successfully.');
        }

        return redirect()->route('categories.index');
    }

    /**
     * Menghapus kategori.
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return redirect()->route('categories.index')
                ->with('error', 'Kategori tidak ditemukan.');
        }

        $category->delete();
        return redirect()->route('categories.index')
            ->with('success', 'Kategori Berhasil dihapus.');
    }

    /**
     * -----------------------------------------------------------------
     *  NEW: Mengembalikan semua kategori dalam format JSON.
     *  Berguna untuk API / populasi dropdown dinamis.
     * -----------------------------------------------------------------
     */
    public function list()
    {
        $categories = Category::select('id', 'name')->orderBy('name')->get();

        return response()->json($categories);
    }
}