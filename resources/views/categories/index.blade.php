{{--
    resources/views/categories/index.blade.php
    Tailwind CSS murni, tanpa Flux/Livewire. Mengikuti struktur sidebar layout dashboard.
--}}
@extends('layouts.sidebar')

@section('title', 'Kelola Kategori')

@section('content')

    {{-- Header & Tombol Tambah --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-medium text-zinc-800 mb-1">Kategori</h1>
            <p class="text-sm text-zinc-500">Kelola kategori produk Anda di sini</p>
        </div>
        <a href="{{ route('categories.create') }}" class="h-10 px-4 inline-flex items-center justify-center text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 rounded-lg shadow-sm transition-colors">
            Tambah Kategori
        </a>
    </div>

    {{-- Flash Message (Opsional jika ada session success) --}}
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabel Kategori --}}
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    {{-- Contoh Looping Data Kategori dari Controller --}}
                    @forelse ($categories as $category)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $category['name'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    {{ $category['type'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $category['description'] ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $category['products_count'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($category['is_active'])
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    {{-- Tombol Edit --}}
                                    <a href="{{ route('categories.edit', $category['id']) }}" class="h-8 px-3 inline-flex items-center text-sm font-medium text-zinc-700 hover:bg-zinc-100 rounded-md">
                                        Edit
                                    </a>

                                    {{-- Tombol Toggle Status (Aktif/Nonaktif) --}}
                                    <form action="{{ route('categories.toggle', $category['id']) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="h-8 px-3 inline-flex items-center text-sm font-medium {{ $category['is_active'] ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' }} hover:bg-zinc-100 rounded-md">
                                            {{ $category['is_active'] ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('categories.destroy', $category['id']) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="h-8 px-3 inline-flex items-center text-sm font-medium text-red-600 hover:text-red-800 hover:bg-zinc-100 rounded-md">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-zinc-500">
                                Belum ada kategori yang tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection