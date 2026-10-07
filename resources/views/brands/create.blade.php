{{-- resources/views/brands/create.blade.php --}}
@extends('layouts.sidebar')

@section('title', 'Tambah Brand')

@section('content')
<div class="w-full space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Tambah Brand</h1>
            <p class="mt-1 text-sm text-gray-500">
                Kelola logo brand sekaligus carousel Brand Pilihan di halaman depan.
            </p>
        </div>

        <a href="{{ route('brands.index') }}"
           class="h-10 px-4 inline-flex items-center justify-center text-sm font-medium
                  bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg
                  border border-gray-400/20 shadow-sm transition-colors">
            Kembali
        </a>
    </div>

    {{-- Flash message --}}
    @if (session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            Data belum bisa disimpan. Periksa kembali isian di bawah.
        </div>
    @endif

    {{-- Form --}}
    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
        <h2 class="mb-5 text-lg font-semibold text-gray-900">Buat Brand Baru</h2>

        <form action="{{ route('brands.store') }}" method="POST" enctype="multipart/form-data"
              class="grid gap-5 lg:grid-cols-2">
            @csrf

            {{-- Nama --}}
            <div class="lg:col-span-2">
                <label for="name" class="block text-sm font-medium text-zinc-800 mb-2">Nama Brand</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                       placeholder="Contoh: Zakira"
                       class="w-full border rounded-lg py-2 px-3 text-sm shadow-xs border-zinc-200
                              focus:border-[#b98e6d] focus:ring-1 focus:ring-[#b98e6d] outline-none">
                @error('name')
                    <div class="mt-2 text-sm font-medium text-red-500">{{ $message }}</div>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div class="lg:col-span-2">
                <label for="description" class="block text-sm font-medium text-zinc-800 mb-2">Deskripsi</label>
                <textarea id="description" name="description" rows="3" placeholder="Deskripsi singkat brand"
                          class="w-full border rounded-lg p-3 text-sm shadow-xs border-zinc-200
                                 focus:border-[#b98e6d] focus:ring-1 focus:ring-[#b98e6d] outline-none resize-y">{{ old('description') }}</textarea>
                @error('description')
                    <div class="mt-2 text-sm font-medium text-red-500">{{ $message }}</div>
                @enderror
            </div>

            {{-- Logo + preview --}}
            <div>
                <label for="logo" class="block text-sm font-medium text-zinc-800 mb-2">Logo Brand</label>
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-24 shrink-0 items-center justify-center overflow-hidden rounded-lg border border-gray-200 bg-white p-2">
                        <img id="logo-preview" src="" alt="Preview logo" class="hidden h-full w-full object-contain">
                        <span id="logo-placeholder" class="text-xs text-gray-400">Preview</span>
                    </div>

                    <input type="file" id="logo" name="logo" accept="image/*"
                           class="block w-full rounded-lg border border-gray-200 bg-white p-2 text-sm text-gray-600
                                  file:mr-3 file:rounded-md file:border-0 file:bg-[#f3e9df] file:px-4 file:py-2
                                  file:font-semibold file:text-[#6f4d3b]">
                </div>
                <p class="mt-1 text-xs text-gray-500">Disarankan PNG transparan / JPG, bentuk logo horizontal atau square.</p>
                @error('logo')
                    <div class="mt-2 text-sm font-medium text-red-500">{{ $message }}</div>
                @enderror
            </div>

            {{-- Urutan homepage --}}
            <div>
                <label for="home_order" class="block text-sm font-medium text-zinc-800 mb-2">Urutan di Homepage</label>
                <input type="number" id="home_order" name="home_order" min="1" step="1"
                       value="{{ old('home_order', $nextOrder ?? 1) }}"
                       class="w-full border rounded-lg py-2 px-3 text-sm shadow-xs border-zinc-200
                              focus:border-[#b98e6d] focus:ring-1 focus:ring-[#b98e6d] outline-none">
                <p class="mt-1 text-xs text-gray-500">
                    Mulai dari 1. Jika urutan sudah dipakai, brand lain otomatis bergeser ke bawah.
                </p>
                @error('home_order')
                    <div class="mt-2 text-sm font-medium text-red-500">{{ $message }}</div>
                @enderror
            </div>

            {{-- Tampil di Homepage --}}
            <div>
                <input type="hidden" name="show_on_home" value="0">
                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 p-4">
                    <input type="checkbox" name="show_on_home" value="1"
                           {{ old('show_on_home', true) ? 'checked' : '' }}
                           class="h-5 w-5 rounded border-gray-300 text-[#6f4d3b] focus:ring-[#b98e6d]">
                    <div>
                        <div class="font-medium text-gray-900">Tampilkan di Homepage</div>
                        <div class="text-xs text-gray-500">Brand masuk carousel selama status brand juga Aktif.</div>
                    </div>
                </label>
                @error('show_on_home')
                    <div class="mt-2 text-sm font-medium text-red-500">{{ $message }}</div>
                @enderror
            </div>

            {{-- Status aktif --}}
            <div>
                <input type="hidden" name="is_active" value="0">
                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 p-4">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="h-5 w-5 rounded border-gray-300 text-[#6f4d3b] focus:ring-[#b98e6d]">
                    <div>
                        <div class="font-medium text-gray-900">Brand Aktif</div>
                        <div class="text-xs text-gray-500">Brand nonaktif tidak akan tampil di halaman depan.</div>
                    </div>
                </label>
                @error('is_active')
                    <div class="mt-2 text-sm font-medium text-red-500">{{ $message }}</div>
                @enderror
            </div>

            {{-- Tombol --}}
            <div class="flex gap-2 lg:col-span-2">
                <button type="submit"
                        class="h-10 px-4 inline-flex items-center justify-center text-sm font-medium
                               bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    Simpan Brand
                </button>

                <a href="{{ route('brands.index') }}"
                   class="h-10 px-4 inline-flex items-center justify-center text-sm font-medium rounded-lg
                          bg-transparent hover:bg-zinc-800/5 text-zinc-800 border border-zinc-200 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>

    {{-- Daftar brand --}}
    <div>
        <h2 class="text-lg font-medium mb-4 text-gray-900">Daftar Brand</h2>
        @include('brands._list')
    </div>
</div>

<script>
    // Preview logo sebelum diupload
    document.getElementById('logo').addEventListener('change', function (e) {
        const file = e.target.files[0];
        const img = document.getElementById('logo-preview');
        const placeholder = document.getElementById('logo-placeholder');

        if (!file) {
            img.classList.add('hidden');
            placeholder.classList.remove('hidden');
            return;
        }

        img.src = URL.createObjectURL(file);
        img.classList.remove('hidden');
        placeholder.classList.add('hidden');
    });
</script>
@endsection