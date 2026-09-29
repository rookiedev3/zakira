<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function index(Request $request): View
    {
        $banners = Banner::orderBy('order')->orderByDesc('created_at')->paginate(10)->withQueryString();

        $editingBanner = $request->filled('edit')
            ? Banner::find($request->integer('edit'))
            : null;

        $showCreateForm = $request->query('form') === 'create';

        return view('banner.index', compact('banners', 'editingBanner', 'showCreateForm'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:slider,promo',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
            'url' => 'nullable|url|max:255',
            'order' => 'required|integer|min:0',
        ]);

        $data['image_path'] = $request->file('image')->store('banners', 'public');
        $data['status'] = $request->boolean('status') ? 'aktif' : 'nonaktif';
        unset($data['image']);

        Banner::create($data);

        return redirect()->route('banners.index')->with('success', 'Banner berhasil ditambahkan.');
    }

    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:slider,promo',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'url' => 'nullable|url|max:255',
            'order' => 'required|integer|min:0',
        ]);

        $data['status'] = $request->boolean('status') ? 'aktif' : 'nonaktif';

        if ($request->hasFile('image')) {
            // Hapus gambar lama biar tidak numpuk sampah di storage
            Storage::disk('public')->delete($banner->image_path);
            $data['image_path'] = $request->file('image')->store('banners', 'public');
        }

        unset($data['image']);

        $banner->update($data);

        return redirect()->route('banners.index')->with('success', 'Banner berhasil diperbarui.');
    }

    public function updateStatus(Banner $banner): RedirectResponse
    {
        $banner->update([
            'status' => $banner->status === 'aktif' ? 'nonaktif' : 'aktif',
        ]);

        $message = $banner->status === 'aktif' ? 'Banner diaktifkan kembali.' : 'Banner dinonaktifkan.';

        return redirect()->route('banners.index')->with('success', $message);
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        Storage::disk('public')->delete($banner->image_path);
        $banner->delete();

        return redirect()->route('banners.index')->with('success', 'Banner berhasil dihapus.');
    }
}