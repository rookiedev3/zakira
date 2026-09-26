{{--
    resources/views/categories/index.blade.php
    Tailwind‑CSS murni – tabel full‑width, border luar tipis, divider baris tipis.
--}}
@extends('layouts.sidebar')

@section('title', 'Categories')

@section('content')
<div class="w-full space-y-6">

    {{-- --------------------------------------------------
         1️⃣ Header + tombol “Tambah Kategori”
         -------------------------------------------------- --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Kategori</h1>
        </div>

        <a href="{{ route('categories.create') }}"
           class="h-10 px-4 inline-flex items-center justify-center text-sm font-medium
                  bg-blue-600 hover:bg-blue-700 text-white rounded-lg
                  border border-blue-500/20 shadow-sm transition-colors">
            Tambah Kategori
        </a>
    </div>

    {{-- --------------------------------------------------
         2️⃣ Flash message (opsional)
         -------------------------------------------------- --}}
    @if (session('success'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- --------------------------------------------------
         3️⃣ Tabel kategori – full‑width, outer‑border tipis, divider tipis
         -------------------------------------------------- --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <!-- w-full + table-auto = lebar 100 % tanpa memaksa kolom melebar -->
            <table class="w-full table-auto border-collapse">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nama
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipe
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Deskripsi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Produk
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <!-- Garis tipis antar‑baris -->
                <tbody class="bg-white">
                    @forelse ($categories as $category)
                        <tr class="border-b border-gray-200 last:border-b-0 hover:bg-gray-50 transition-colors">
                            {{-- Nama --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $category->name }}
                            </td>

                            {{-- Tipe (badge) --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ $category->type ?? '-' }}
                                </span>
                            </td>

                            {{-- Deskripsi – wrap bila panjang sehingga tabel tidak memanjang --}}
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-normal break-words">
                                {{ $category->description ?? '-' }}
                            </td>

                            {{-- Produk (jumlah) --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $category->products->count() }}
                            </td>

                            {{-- Status badge --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($category->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi (Edit / Toggle / Delete) --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-1">
                                    <!-- Edit -->
                                    <a href="{{ route('categories.edit', $category->id) }}"
                                       class="h-8 inline-flex items-center px-3 text-sm text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                                        Edit
                                    </a>

                                    <!-- Aktifkan / Nonaktifkan -->
                                    <form action="{{ route('categories.toggle', $category->id) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="h-8 inline-flex items-center px-3 text-sm
                                                       {{ $category->is_active ? 'text-red-600 hover:text-red-700' : 'text-green-600 hover:text-green-700' }}
                                                       hover:bg-gray-100 rounded-md transition-colors">
                                            {{ $category->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <!-- Hapus -->
                                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST"
                                          class="inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="h-8 inline-flex items-center px-3 text-sm text-red-600 hover:text-red-700 hover:bg-gray-100 rounded-md transition-colors">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                Belum ada kategori yang tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection