<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Banner - Zakira</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50: '#fdf6f0', 100: '#f8e9db', 400: '#c9995f', 600: '#9c6b3a', 700: '#7d5330' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800">

    <div class="max-w-6xl mx-auto px-4 py-8">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-serif font-bold">Banner</h1>
            @if (! $showCreateForm && ! $editingBanner)
                <a href="{{ route('banners.index', ['form' => 'create']) }}"
                   class="bg-brand-600 text-white px-5 py-2.5 rounded-full font-semibold text-sm hover:bg-brand-700">
                    + Tambah Banner
                </a>
            @endif
        </div>

        @if (session('success'))
            <div class="bg-green-50 text-green-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
        @endif

        {{-- ==== FORM TAMBAH ==== --}}
        @if ($showCreateForm)
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="font-semibold text-lg mb-4">Buat Banner</h2>

                @if ($errors->any())
                    <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('banners.store') }}" enctype="multipart/form-data" class="grid md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="text-sm font-medium">Judul</label>
                        <input type="text" name="title" value="{{ old('title') }}" placeholder="Masukkan judul banner" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>
                    <div>
                        <label class="text-sm font-medium">Tipe</label>
                        <select name="type" required class="w-full border rounded-lg px-4 py-2 mt-1">
                            <option value="slider" @selected(old('type', 'slider') === 'slider')>Slider</option>
                            <option value="promo" @selected(old('type') === 'promo')>Promo</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-medium">Deskripsi</label>
                        <textarea name="description" rows="3" placeholder="Masukkan deskripsi banner"
                                  class="w-full border rounded-lg px-4 py-2 mt-1">{{ old('description') }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-medium">Gambar Banner</label>
                        <input type="file" name="image" accept="image/*" required
                               class="w-full border rounded-lg px-4 py-2 mt-1 text-sm">
                        <p class="text-xs text-gray-400 mt-1">
                            Gambar akan otomatis dipotong (cover) mengikuti rasio 21:7 (lebar/landscape) di halaman utama — gambar portrait tetap bisa diupload, tapi bagian tengahnya yang akan tampil. Untuk hasil terbaik, gunakan foto landscape (mendatar).
                        </p>
                    </div>

                    <div>
                        <label class="text-sm font-medium">Link URL (Opsional)</label>
                        <input type="url" name="url" value="{{ old('url') }}" placeholder="https://example.com"
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>
                    <div>
                        <label class="text-sm font-medium">Urutan</label>
                        <input type="number" name="order" value="{{ old('order', 0) }}" min="0" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>
                    <div class="flex items-center">
                        <label class="flex items-center gap-2 text-sm mt-6">
                            <input type="checkbox" name="status" value="1" checked class="rounded border-gray-300">
                            Aktif
                        </label>
                    </div>

                    <div class="md:col-span-2 flex gap-3 mt-2">
                        <button class="bg-brand-600 text-white px-6 py-2.5 rounded-full font-semibold text-sm hover:bg-brand-700">
                            Buat Banner
                        </button>
                        <a href="{{ route('banners.index') }}" class="px-6 py-2.5 rounded-full font-semibold text-sm border">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        @endif

        {{-- ==== FORM EDIT ==== --}}
        @if ($editingBanner)
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="font-semibold text-lg mb-4">Edit Banner</h2>

                @if ($errors->any())
                    <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('banners.update', $editingBanner) }}" enctype="multipart/form-data" class="grid md:grid-cols-2 gap-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="text-sm font-medium">Judul</label>
                        <input type="text" name="title" value="{{ old('title', $editingBanner->title) }}" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>
                    <div>
                        <label class="text-sm font-medium">Tipe</label>
                        <select name="type" required class="w-full border rounded-lg px-4 py-2 mt-1">
                            <option value="slider" @selected(old('type', $editingBanner->type) === 'slider')>Slider</option>
                            <option value="promo" @selected(old('type', $editingBanner->type) === 'promo')>Promo</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-medium">Deskripsi</label>
                        <textarea name="description" rows="3" placeholder="Masukkan deskripsi banner"
                                  class="w-full border rounded-lg px-4 py-2 mt-1">{{ old('description', $editingBanner->description) }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-medium">Gambar Banner</label>
                        <input type="file" name="image" accept="image/*"
                               class="w-full border rounded-lg px-4 py-2 mt-1 text-sm">
                        <p class="text-xs text-gray-400 mt-1">
                            Gambar akan otomatis dipotong (cover) mengikuti rasio 21:7 (lebar/landscape) di halaman utama — gambar portrait tetap bisa diupload, tapi bagian tengahnya yang akan tampil. Untuk hasil terbaik, gunakan foto landscape (mendatar). Kosongkan kalau tidak ingin mengganti gambar.
                        </p>

                        <div class="mt-3">
                            <p class="text-xs text-gray-500 mb-1">Gambar saat ini:</p>
                            <img src="{{ $editingBanner->image_url }}" alt="{{ $editingBanner->title }}"
                                 class="w-full max-w-md aspect-[21/7] object-cover rounded-lg border">
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium">Link URL (Opsional)</label>
                        <input type="url" name="url" value="{{ old('url', $editingBanner->url) }}" placeholder="https://example.com"
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>
                    <div>
                        <label class="text-sm font-medium">Urutan</label>
                        <input type="number" name="order" value="{{ old('order', $editingBanner->order) }}" min="0" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>
                    <div class="flex items-center">
                        <label class="flex items-center gap-2 text-sm mt-6">
                            <input type="checkbox" name="status" value="1" @checked($editingBanner->status === 'aktif') class="rounded border-gray-300">
                            Aktif
                        </label>
                    </div>

                    <div class="md:col-span-2 flex gap-3 mt-2">
                        <button class="bg-brand-600 text-white px-6 py-2.5 rounded-full font-semibold text-sm hover:bg-brand-700">
                            Perbarui Banner
                        </button>
                        <a href="{{ route('banners.index') }}" class="px-6 py-2.5 rounded-full font-semibold text-sm border">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        @endif

        {{-- ==== TABEL ==== --}}
        <div class="bg-white rounded-xl shadow-sm overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="px-4 py-3">Gambar</th>
                        <th class="px-4 py-3">Judul</th>
                        <th class="px-4 py-3">Tipe</th>
                        <th class="px-4 py-3">Urutan</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($banners as $banner)
                        <tr>
                            <td class="px-4 py-3">
                                <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}"
                                     class="w-24 aspect-[21/7] object-cover rounded-lg border">
                            </td>
                            <td class="px-4 py-3 font-medium">{{ $banner->title }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $banner->type === 'slider' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ ucfirst($banner->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $banner->order }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $banner->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($banner->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('banners.index', ['edit' => $banner->id]) }}"
                                       class="text-brand-700 font-semibold text-xs">Edit</a>

                                    <form method="POST" action="{{ route('banners.status', $banner) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="text-amber-600 font-semibold text-xs">
                                            {{ $banner->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('banners.destroy', $banner) }}"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus banner ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-500 font-semibold text-xs">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">Belum ada data banner.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $banners->links() }}
        </div>
    </div>
</body>
</html>