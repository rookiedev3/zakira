@extends('layouts.sidebar')

@section('title', 'Keunggulan — Zakira Admin')

@section('content')
<div>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Keunggulan</h1>
        @if (! $showCreateForm && ! $editingAdvantage)
            <a href="{{ route('advantages.index', ['form' => 'create']) }}"
               class="bg-[#8B5E3C] text-white px-5 py-2.5 rounded-lg font-medium text-sm shadow-sm hover:bg-[#7a5134] transition-colors">
                + Tambah Keunggulan
            </a>
        @endif
    </div>

    @if (session('success'))
        <div class="bg-green-50 text-green-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
    @endif

    {{-- ==== FORM TAMBAH ==== --}}
    @if ($showCreateForm)
        <div class="bg-white rounded-xl shadow-[0_1px_4px_rgba(0,0,0,0.08)] p-6 mb-6">
            <h2 class="font-semibold text-lg text-gray-900 mb-4">Buat Keunggulan</h2>

            @if ($errors->any())
                <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('advantages.store') }}" enctype="multipart/form-data" class="grid md:grid-cols-2 gap-4">
                @csrf
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700">Judul</label>
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="Masukkan judul keunggulan" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1 text-gray-900">
                </div>

                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea name="description" rows="3" placeholder="Masukkan deskripsi keunggulan"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1 text-gray-900 required">{{ old('description') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700">Gambar Keunggulan</label>
                    <input type="file" name="image" id="imageInput" accept="image/*"
                           onchange="previewImage(this, 'previewCreate')"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1 text-sm text-gray-700">
                    <p class="text-xs text-gray-500 mt-1">Maksimal ukuran file 12MB. Boleh dikosongkan.</p>

                    <div id="previewCreate" class="mt-3 hidden">
                        <p class="text-xs text-gray-500 mb-1">Pratinjau</p>
                        <img src="" alt="Pratinjau" class="w-40 h-40 object-cover rounded-md border border-gray-200">
                    </div>
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
                        Buat Keunggulan
                    </button>
                    <a href="{{ route('advantages.index') }}" class="px-6 py-2.5 rounded-lg font-medium text-sm text-gray-700 border border-gray-300 hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    @endif

    {{-- ==== FORM EDIT ==== --}}
    @if ($editingAdvantage)
        <div class="bg-white rounded-xl shadow-[0_1px_4px_rgba(0,0,0,0.08)] p-6 mb-6">
            <h2 class="font-semibold text-lg text-gray-900 mb-4">Edit Keunggulan</h2>

            @if ($errors->any())
                <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('advantages.update', $editingAdvantage) }}" enctype="multipart/form-data" class="grid md:grid-cols-2 gap-4">
                @csrf
                @method('PUT')
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700">Judul</label>
                    <input type="text" name="title" value="{{ old('title', $editingAdvantage->title) }}" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1 text-gray-900">
                </div>

                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea name="description" rows="3" placeholder="Masukkan deskripsi keunggulan"
                              class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1 text-gray-900">{{ old('description', $editingAdvantage->description) }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-gray-700">Gambar Keunggulan</label>
                    <input type="file" name="image" accept="image/*"
                           onchange="previewImage(this, 'previewEdit')"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1 text-sm text-gray-700">
                    <p class="text-xs text-gray-500 mt-1">Maksimal ukuran file 12MB. Kosongkan kalau tidak ingin mengganti gambar.</p>

                    <div id="previewEdit" class="mt-3 hidden">
                        <p class="text-xs text-gray-500 mb-1">Pratinjau</p>
                        <img src="" alt="Pratinjau" class="w-40 h-40 object-cover rounded-md border border-gray-200">
                    </div>

                    <div class="mt-3">
                        <p class="text-xs text-gray-500 mb-1">Gambar saat ini:</p>
                        @if ($editingAdvantage->image_url)
                            <img src="{{ $editingAdvantage->image_url }}" alt="{{ $editingAdvantage->title }}"
                                 class="w-40 h-40 object-cover rounded-md border border-gray-200">
                        @else
                            <div class="w-40 h-40 flex items-center justify-center rounded-md border border-gray-200 bg-gray-50 text-xs text-gray-500 text-center px-2">
                                Tanpa gambar
                            </div>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Urutan</label>
                    <input type="number" name="order" value="{{ old('order', $editingAdvantage->order) }}" min="0" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 mt-1 text-gray-900">
                </div>
                <div class="flex items-center">
                    <label class="flex items-center gap-2 text-sm text-gray-700 mt-6">
                        <input type="checkbox" name="status" value="1" @checked($editingAdvantage->status === 'aktif') class="rounded border-gray-300 text-[#8B5E3C]">
                        Aktif
                    </label>
                </div>

                <div class="md:col-span-2 flex gap-3 mt-2">
                    <button class="bg-[#8B5E3C] text-white px-6 py-2.5 rounded-lg font-medium text-sm shadow-sm hover:bg-[#7a5134] transition-colors">
                        Perbarui Keunggulan
                    </button>
                    <a href="{{ route('advantages.index') }}" class="px-6 py-2.5 rounded-lg font-medium text-sm text-gray-700 border border-gray-300 hover:bg-gray-50 transition-colors">
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
                        <th class="px-6 py-3 font-medium">Urutan</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($advantages as $advantage)
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="px-6 py-4">
                                @if ($advantage->image_url)
                                    <img src="{{ $advantage->image_url }}" alt="{{ $advantage->title }}"
                                         class="w-16 h-16 object-cover rounded-md">
                                @else
                                    <div class="w-16 h-16 flex items-center justify-center rounded-md bg-gray-100 text-[10px] text-gray-500 text-center px-1 leading-tight">
                                        Tanpa gambar
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-900">{{ $advantage->title }}</p>
                                <p class="text-xs text-gray-500">{{ Str::limit($advantage->description, 40) }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $advantage->order }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $advantage->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($advantage->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('advantages.index', ['edit' => $advantage->id]) }}"
                                       class="text-gray-900 text-sm">Edit</a>

                                    <form method="POST" action="{{ route('advantages.status', $advantage) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="text-gray-900 text-sm">
                                            {{ $advantage->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('advantages.destroy', $advantage) }}"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus keunggulan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-gray-900 text-sm">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada keunggulan yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination gaya "Showing 1 to 10 of 23 results" --}}
        <div class="px-4 py-4 border-t border-gray-200">
            {{ $advantages->links('vendor.pagination.zakira') }}
        </div>
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
@endsection