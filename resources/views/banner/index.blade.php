@extends('layouts.sidebar')

@section('title', 'Banner — Zakira Admin')

@section('content')
<div>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Banner</h1>
        @if (! $showCreateForm && ! $editingBanner)
            <a href="{{ route('banners.index', ['form' => 'create']) }}"
               class="bg-[#8B5E3C] text-white px-5 py-2.5 rounded-lg font-medium text-sm shadow-sm hover:bg-[#7a5134] transition-colors">
                + Tambah Banner
            </a>
        @endif
    </div>

    @if (session('success'))
        <div class="bg-green-50 text-green-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
    @endif

    {{-- ==== FORM TAMBAH ==== --}}
    @if ($showCreateForm)
        <div class="bg-white rounded-xl shadow-[0_1px_4px_rgba(0,0,0,0.08)] p-6 mb-6">
            <h2 class="font-semibold text-lg text-gray-900 mb-4">Buat Banner</h2>

            @if ($errors->any())
                <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('banners.store') }}" enctype="multipart/form-data" class="grid md:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="text-sm font-medium text-gray-700">Judul</label>
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="Masukkan judul banner" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1 text-gray-900">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Tipe</label>
                    <select name="type" required class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1 text-gray-900">
                        <option value="slider" @selected(old('type', 'slider') === 'slider')>Slider</option>
                        <option value="promo" @selected(old('type') === 'promo')>Promo</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea name="description" rows="3" placeholder="Masukkan deskripsi banner"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1 text-gray-900">{{ old('description') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700">Gambar Banner</label>
                    <input type="file" name="image" accept="image/*" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1 text-sm text-gray-700">
                    <p class="text-xs text-gray-500 mt-1">
                        Gambar akan otomatis dipotong (cover) mengikuti rasio 21:7 (lebar/landscape) di halaman utama — gambar portrait tetap bisa diupload, tapi bagian tengahnya yang akan tampil. Untuk hasil terbaik, gunakan foto landscape (mendatar).
                    </p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Link URL (Opsional)</label>
                    <input type="url" name="url" value="{{ old('url') }}" placeholder="https://example.com"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1 text-gray-900">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Urutan</label>
                    <input type="number" name="order" value="{{ old('order', 0) }}" min="0" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1 text-gray-900">
                </div>
                <div class="flex items-center">
                    <label class="flex items-center gap-2 text-sm text-gray-700 mt-6">
                        <input type="checkbox" name="status" value="1" checked class="rounded border-gray-300 text-[#8B5E3C]">
                        Aktif
                    </label>
                </div>

                <div class="md:col-span-2 flex gap-3 mt-2">
                    <button class="bg-[#8B5E3C] text-white px-6 py-2.5 rounded-lg font-medium text-sm shadow-sm hover:bg-[#7a5134] transition-colors">
                        Buat Banner
                    </button>
                    <a href="{{ route('banners.index') }}" class="px-6 py-2.5 rounded-lg font-medium text-sm text-gray-700 border border-gray-300 hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    @endif

    {{-- ==== FORM EDIT ==== --}}
    @if ($editingBanner)
        <div class="bg-white rounded-xl shadow-[0_1px_4px_rgba(0,0,0,0.08)] p-6 mb-6">
            <h2 class="font-semibold text-lg text-gray-900 mb-4">Edit Banner</h2>

            @if ($errors->any())
                <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('banners.update', $editingBanner) }}" enctype="multipart/form-data" class="grid md:grid-cols-2 gap-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-sm font-medium text-gray-700">Judul</label>
                    <input type="text" name="title" value="{{ old('title', $editingBanner->title) }}" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1 text-gray-900">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Tipe</label>
                    <select name="type" required class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1 text-gray-900">
                        <option value="slider" @selected(old('type', $editingBanner->type) === 'slider')>Slider</option>
                        <option value="promo" @selected(old('type', $editingBanner->type) === 'promo')>Promo</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea name="description" rows="3" placeholder="Masukkan deskripsi banner"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1 text-gray-900">{{ old('description', $editingBanner->description) }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700">Gambar Banner</label>
                    <input type="file" name="image" accept="image/*"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1 text-sm text-gray-700">
                    <p class="text-xs text-gray-500 mt-1">
                        Gambar akan otomatis dipotong (cover) mengikuti rasio 21:7 (lebar/landscape) di halaman utama — gambar portrait tetap bisa diupload, tapi bagian tengahnya yang akan tampil. Untuk hasil terbaik, gunakan foto landscape (mendatar). Kosongkan kalau tidak ingin mengganti gambar.
                    </p>

                    <div class="mt-3">
                        <p class="text-xs text-gray-500 mb-1">Gambar saat ini:</p>
                        <img src="{{ $editingBanner->image_url }}" alt="{{ $editingBanner->title }}"
                             class="w-full max-w-md aspect-[21/7] object-cover rounded-md border border-gray-200">
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Link URL (Opsional)</label>
                    <input type="url" name="url" value="{{ old('url', $editingBanner->url) }}" placeholder="https://example.com"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1 text-gray-900">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Urutan</label>
                    <input type="number" name="order" value="{{ old('order', $editingBanner->order) }}" min="0" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1 text-gray-900">
                </div>
                <div class="flex items-center">
                    <label class="flex items-center gap-2 text-sm text-gray-700 mt-6">
                        <input type="checkbox" name="status" value="1" @checked($editingBanner->status === 'aktif') class="rounded border-gray-300 text-[#8B5E3C]">
                        Aktif
                    </label>
                </div>

                <div class="md:col-span-2 flex gap-3 mt-2">
                    <button class="bg-[#8B5E3C] text-white px-6 py-2.5 rounded-lg font-medium text-sm shadow-sm hover:bg-[#7a5134] transition-colors">
                        Perbarui Banner
                    </button>
                    <a href="{{ route('banners.index') }}" class="px-6 py-2.5 rounded-lg font-medium text-sm text-gray-700 border border-gray-300 hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    @endif

    {{-- ==== TABEL ==== --}}
    <div class="bg-white rounded-xl shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-left text-gray-500 border-b border-gray-200 uppercase tracking-wide text-xs">
                        <th class="px-6 py-3 font-medium">Gambar</th>
                        <th class="px-6 py-3 font-medium">Judul</th>
                        <th class="px-6 py-3 font-medium">Tipe</th>
                        <th class="px-6 py-3 font-medium">Urutan</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($banners as $banner)
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="px-6 py-4">
                                <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}"
                                     class="w-20 aspect-[21/7] object-cover rounded-md">
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $banner->title }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $banner->type === 'slider' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                    {{ ucfirst($banner->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $banner->order }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $banner->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($banner->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('banners.index', ['edit' => $banner->id]) }}"
                                       class="text-gray-900 text-sm">Edit</a>

                                    <form method="POST" action="{{ route('banners.status', $banner) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="text-gray-900 text-sm">
                                            {{ $banner->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('banners.destroy', $banner) }}"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus banner ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-gray-900 text-sm">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">Belum ada data banner.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination gaya "Showing 1 to 10 of 23 results" --}}
        <div class="px-4 py-4 border-t border-gray-200">
            {{ $banners->links('vendor.pagination.zakira') }}
        </div>
    </div>
</div>
@endsection