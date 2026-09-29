<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Keunggulan - Zakira</title>
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
            <h1 class="text-2xl font-serif font-bold">Keunggulan</h1>
            @if (! $showCreateForm && ! $editingAdvantage)
                <a href="{{ route('advantages.index', ['form' => 'create']) }}"
                   class="bg-brand-600 text-white px-5 py-2.5 rounded-full font-semibold text-sm hover:bg-brand-700">
                    + Tambah Keunggulan
                </a>
            @endif
        </div>

        @if (session('success'))
            <div class="bg-green-50 text-green-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
        @endif

        {{-- ==== FORM TAMBAH ==== --}}
        @if ($showCreateForm)
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="font-semibold text-lg mb-4">Buat Keunggulan</h2>

                @if ($errors->any())
                    <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('advantages.store') }}" enctype="multipart/form-data" class="grid md:grid-cols-2 gap-4">
                    @csrf
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium">Judul</label>
                        <input type="text" name="title" value="{{ old('title') }}" placeholder="Masukkan judul keunggulan" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-medium">Deskripsi</label>
                        <textarea name="description" rows="3" placeholder="Masukkan deskripsi keunggulan"
                                  class="w-full border rounded-lg px-4 py-2 mt-1 required">{{ old('description') }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-medium">Gambar Keunggulan</label>
                        <input type="file" name="image" id="imageInput" accept="image/*" 
                               onchange="previewImage(this, 'previewCreate')"
                               class="w-full border rounded-lg px-4 py-2 mt-1 text-sm">
                        <p class="text-xs text-gray-400 mt-1">Maksimal ukuran file 12MB. Boleh dikosongkan.</p>

                        <div id="previewCreate" class="mt-3 hidden">
                            <p class="text-xs text-gray-500 mb-1">Pratinjau</p>
                            <img src="" alt="Pratinjau" class="w-40 h-40 object-cover rounded-lg border">
                        </div>
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
                            Buat Keunggulan
                        </button>
                        <a href="{{ route('advantages.index') }}" class="px-6 py-2.5 rounded-full font-semibold text-sm border">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        @endif

        {{-- ==== FORM EDIT ==== --}}
        @if ($editingAdvantage)
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="font-semibold text-lg mb-4">Edit Keunggulan</h2>

                @if ($errors->any())
                    <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('advantages.update', $editingAdvantage) }}" enctype="multipart/form-data" class="grid md:grid-cols-2 gap-4">
                    @csrf
                    @method('PUT')
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium">Judul</label>
                        <input type="text" name="title" value="{{ old('title', $editingAdvantage->title) }}" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-medium">Deskripsi</label>
                        <textarea name="description" rows="3" placeholder="Masukkan deskripsi keunggulan"
                                  class="w-full border rounded-lg px-4 py-2 mt-1">{{ old('description', $editingAdvantage->description) }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-sm font-medium">Gambar Keunggulan</label>
                        <input type="file" name="image" accept="image/*"
                               onchange="previewImage(this, 'previewEdit')"
                               class="w-full border rounded-lg px-4 py-2 mt-1 text-sm">
                        <p class="text-xs text-gray-400 mt-1">Maksimal ukuran file 12MB. Kosongkan kalau tidak ingin mengganti gambar.</p>

                        <div id="previewEdit" class="mt-3 hidden">
                            <p class="text-xs text-gray-500 mb-1">Pratinjau</p>
                            <img src="" alt="Pratinjau" class="w-40 h-40 object-cover rounded-lg border">
                        </div>

                        <div class="mt-3">
                            <p class="text-xs text-gray-500 mb-1">Gambar saat ini:</p>
                            @if ($editingAdvantage->image_url)
                                <img src="{{ $editingAdvantage->image_url }}" alt="{{ $editingAdvantage->title }}"
                                     class="w-40 h-40 object-cover rounded-lg border">
                            @else
                                <div class="w-40 h-40 flex items-center justify-center rounded-lg border bg-gray-50 text-xs text-gray-400 text-center px-2">
                                    Tanpa gambar
                                </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium">Urutan</label>
                        <input type="number" name="order" value="{{ old('order', $editingAdvantage->order) }}" min="0" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>
                    <div class="flex items-center">
                        <label class="flex items-center gap-2 text-sm mt-6">
                            <input type="checkbox" name="status" value="1" @checked($editingAdvantage->status === 'aktif') class="rounded border-gray-300">
                            Aktif
                        </label>
                    </div>

                    <div class="md:col-span-2 flex gap-3 mt-2">
                        <button class="bg-brand-600 text-white px-6 py-2.5 rounded-full font-semibold text-sm hover:bg-brand-700">
                            Perbarui Keunggulan
                        </button>
                        <a href="{{ route('advantages.index') }}" class="px-6 py-2.5 rounded-full font-semibold text-sm border">
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
                        <th class="px-4 py-3">Urutan</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($advantages as $advantage)
                        <tr>
                            <td class="px-4 py-3">
                                @if ($advantage->image_url)
                                    <img src="{{ $advantage->image_url }}" alt="{{ $advantage->title }}"
                                         class="w-16 h-16 object-cover rounded-lg border">
                                @else
                                    <div class="w-16 h-16 flex items-center justify-center rounded-lg border bg-gray-50 text-[10px] text-gray-400 text-center px-1 leading-tight">
                                        Tanpa gambar
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium">{{ $advantage->title }}</p>
                                <p class="text-xs text-gray-400">{{ Str::limit($advantage->description, 40) }}</p>
                            </td>
                            <td class="px-4 py-3">{{ $advantage->order }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $advantage->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($advantage->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('advantages.index', ['edit' => $advantage->id]) }}"
                                       class="text-brand-700 font-semibold text-xs">Edit</a>

                                    <form method="POST" action="{{ route('advantages.status', $advantage) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="text-amber-600 font-semibold text-xs">
                                            {{ $advantage->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('advantages.destroy', $advantage) }}"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus keunggulan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-500 font-semibold text-xs">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400">Tidak ada keunggulan yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $advantages->links() }}
        </div>
    </div>

    <script>
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const img = preview.querySelector('img');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    img.src = e.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.classList.add('hidden');
            }
        }
    </script>
</body>
</html>