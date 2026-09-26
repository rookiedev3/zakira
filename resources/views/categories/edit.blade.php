{{-- resources/views/categories/edit.blade.php --}}
@extends('layouts.sidebar')

@section('title', 'Edit Kategori')

@section('content')
<div class="[grid-area:main] p-6 lg:p-8 [[data‑flux‑container]_&]:px-0" data‑flux‑main="">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Edit Kategori</h1>

        <a href="{{ route('categories.index') }}"
           class="h-10 px-4 inline-flex items-center justify-center text-sm font-medium
                  bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg
                  border border-gray-400/20 shadow-sm transition-colors">
            Kembali
        </a>
    </div>

    {{-- Form Edit Kategori (layout sama persis dengan create) --}}
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-medium mb-4">Perbarui Kategori</h2>

        <form action="{{ route('categories.update', $category->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Nama --}}
            <div>
                <ui-field data‑flux‑field>
                    <ui-label data‑flux‑label>Nama Kategori</ui-label>
                    <div data‑flux‑input>
                        <input type="text"
                               name="name"
                               value="{{ old('name', $category->name) }}"
                               class="w-full border rounded-lg py-2 px-3"
                               placeholder="Masukkan nama kategori"
                               required>
                    </div>
                </ui-field>
                @error('name')
                    <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>

            {{-- Tipe (opsional) --}}
            <div>
                <ui-field data‑flux‑field>
                    <ui-label data‑flux‑label>Tipe</ui-label>
                    <select name="type" class="w-full border rounded-lg py-2 px-3">
                        <option value="" {{ old('type', $category->type) == '' ? 'selected' : '' }}>Pilih tipe (opsional)</option>
                        <option value="men"       {{ old('type', $category->type) == 'men'       ? 'selected' : '' }}>Pria</option>
                        <option value="women"     {{ old('type', $category->type) == 'women'     ? 'selected' : '' }}>Wanita</option>
                        <option value="unisex"    {{ old('type', $category->type) == 'unisex'    ? 'selected' : '' }}>Unisex</option>
                        <option value="accessories" {{ old('type', $category->type) == 'accessories' ? 'selected' : '' }}>Aksesori</option>
                        <option value="shoes"     {{ old('type', $category->type) == 'shoes'     ? 'selected' : '' }}>Sepatu</option>
                    </select>
                </ui-field>
                @error('type')
                    <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>

            {{-- Deskripsi (opsional) --}}
            <div>
                <ui-field data‑flux‑field>
                    <ui-label data‑flux‑label>Deskripsi</ui-label>
                    <textarea name="description"
                              rows="3"
                              class="w-full border rounded-lg py-2 px-3"
                              placeholder="Masukkan deskripsi kategori">{{ old('description', $category->description) }}</textarea>
                </ui-field>
                @error('description')
                    <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>

            {{-- Status aktif (opsional) --}}
            <div class="flex items-center space-x-2">
                <label class="inline-flex items-center">
                    <input type="checkbox"
                           name="is_active"
                           value="1"
                           {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                           class="form-checkbox">
                    <span class="ml-2 text-sm">Aktif</span>
                </label>
            </div>

            {{-- Tombol aksi --}}
            <div class="flex space-x-2">
                <button type="submit"
                        class="h-10 px-4 inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    Simpan Perubahan
                </button>

                <a href="{{ route('categories.index') }}"
                   class="h-10 px-4 inline-flex items-center justify-center bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg">
                    Batal
                </a>
            </div>
        </form>
    </div>

    {{-- ---------------------------------------------------------
         Daftar Kategori (sama persis dengan index/create)
    ---------------------------------------------------------- --}}
    <h2 class="text-lg font-medium mt-10 mb-4">Daftar Kategori</h2>
    @include('categories._list')

    {{-- -------------------------------------------------------------
         Script‑script yang diperlukan (sama dengan file create)
    ---------------------------------------------------------------- --}}
    <script src="{{ asset('Categories_files/flux.min.js') }}" data‑navigate‑once></script>
    <script src="{{ asset('Categories_files/livewire.min.js') }}"
            data‑csrf="{{ csrf_token() }}"
            data‑update‑uri="/livewire/update"
            data‑navigate‑once="true"></script>
@endsection