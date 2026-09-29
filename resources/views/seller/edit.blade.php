{{-- Halaman Edit Seller: memakai layout sidebar lewat sellers.index (header, filter, dan tabel tetap tampil di bawah form) --}}
@extends('seller.index')

@section('title', 'Edit Seller — Zakira Admin')

@php
    // DUMMY: ganti dengan $seller dan $brands dari controller nanti
    $brands = [
        ['id' => 1, 'nama' => 'ZAKIRA'],
    ];
    $seller = ['id' => 1, 'seller_id' => 'asdk123', 'nama' => 'AKN', 'brand_id' => 1];
@endphp

@section('form')
<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-lg font-medium text-gray-900 mb-4">Edit Seller</h2>

    {{-- DUMMY: onsubmit dimatikan. Nanti ganti dengan action="{{ route('sellers.update', $seller) }}" method="POST" + @csrf + @method('PUT') --}}
    <form onsubmit="return false" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-800 mb-2">ID Seller</label>
            <input type="text" name="seller_id" value="{{ $seller['seller_id'] }}"
                   class="w-full border border-zinc-200 border-b-zinc-300/80 rounded-lg shadow-xs px-3 py-2 h-10 text-sm bg-white text-zinc-700 placeholder-zinc-400 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-800 mb-2">Nama</label>
            <input type="text" name="nama" value="{{ $seller['nama'] }}"
                   class="w-full border border-zinc-200 border-b-zinc-300/80 rounded-lg shadow-xs px-3 py-2 h-10 text-sm bg-white text-zinc-700 placeholder-zinc-400 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-800 mb-2">Brand</label>
            <div class="relative">
                <select name="brand_id"
                        class="w-full appearance-none pe-10 border border-zinc-200 border-b-zinc-300/80 rounded-lg shadow-xs px-3 py-2 h-10 text-sm bg-white text-zinc-700 focus:outline-none">
                    @foreach ($brands as $b)
                        <option value="{{ $b['id'] }}" @selected($seller['brand_id'] == $b['id'])>{{ $b['nama'] }}</option>
                    @endforeach
                </select>
                <svg class="pointer-events-none absolute end-3 top-1/2 -translate-y-1/2 size-4 text-zinc-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m7 15 5 5 5-5"></path><path d="m7 9 5-5 5 5"></path>
                </svg>
            </div>
        </div>

        <div class="flex items-center gap-2 pt-2">
            <button type="submit"
                    class="items-center font-medium justify-center whitespace-nowrap h-10 text-sm rounded-lg px-4 inline-flex bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] text-[var(--color-accent-foreground)] border border-black/10 dark:border-0 shadow-xs transition">
                Perbarui Seller
            </button>
            <a href="{{ route('seller.index') }}"
               class="items-center font-medium justify-center whitespace-nowrap h-10 text-sm rounded-lg px-4 inline-flex text-zinc-800 hover:bg-zinc-800/5 transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection