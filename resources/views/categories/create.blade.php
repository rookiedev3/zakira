{{-- resources/views/categories/create.blade.php --}}
@extends('layouts.sidebar')

@section('title', 'Tambah Kategori')

@section('content')
<div class="[grid-area:main] p-6 lg:p-8 [[data‑flux‑container]_&]:px-0" data‑flux‑main="">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Tambah Kategori</h1>

        <a href="{{ route('categories.index') }}"
           class="h-10 px-4 inline-flex items-center justify-center text-sm font-medium
                  text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
            Kembali
        </a>
    </div>

    {{-- Form Tambah Kategori (tidak memerlukan $categories) --}}
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-medium mb-4">Buat Kategori Baru</h2>

        <form action="{{ route('categories.store') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Nama --}}
            <div>
                <ui-field data‑flux‑field>
                    <ui-label data‑flux‑label>Nama Kategori</ui-label>
                    <div data‑flux‑input>
                        <input type="text" name="name"
                               value="{{ old('name') }}"
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
                        <option value="" selected>Pilih tipe (opsional)</option>
                        <option value="men"       {{ old('type') == 'men'       ? 'selected' : '' }}>Pria</option>
                        <option value="women"     {{ old('type') == 'women'     ? 'selected' : '' }}>Wanita</option>
                        <option value="unisex"    {{ old('type') == 'unisex'    ? 'selected' : '' }}>Unisex</option>
                        <option value="accessories" {{ old('type') == 'accessories' ? 'selected' : '' }}>Aksesori</option>
                        <option value="shoes"     {{ old('type') == 'shoes'     ? 'selected' : '' }}>Sepatu</option>
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
                    <textarea name="description" rows="3"
                              class="w-full border rounded-lg py-2 px-3"
                              placeholder="Masukkan deskripsi kategori">{{ old('description') }}</textarea>
                </ui-field>
                @error('description')
                    <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>

            {{-- Status aktif (opsional) --}}
            <div class="flex items-center space-x-2">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="form-checkbox">
                    <span class="ml-2 text-sm">Aktif</span>
                </label>
            </div>

            {{-- Tombol aksi --}}
            <div class="flex space-x-2">
                <button type="submit"
                        class="h-10 px-4 inline-flex items-center justify-center bg-[#9C5B34] hover:bg-[#834A27] text-white rounded-lg transition-colors">
                    Simpan Kategori
                </button>

                <a href="{{ route('categories.index') }}"
                   class="h-10 px-4 inline-flex items-center justify-center text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

{{-- ---------------------------------------------------------
     Daftar Kategori (sama persis dengan tampilan index)
---------------------------------------------------------- --}}
<h2 class="text-lg font-medium mt-10 mb-4">Daftar Kategori</h2>

@include('categories._list')

{{-- Script yang diperlukan (sama dengan file index/create lainnya) --}}
<script src="{{ asset('Categories_files/flux.min.js') }}" data‑navigate‑once></script>
<script src="{{ asset('Categories_files/livewire.min.js') }}"
        data‑csrf="{{ csrf_token() }}"
        data‑update‑uri="/livewire/update"
        data‑navigate‑once="true"></script>
@endsection