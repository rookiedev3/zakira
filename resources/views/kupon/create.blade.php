@extends('layouts.sidebar')

@section('title', 'Buat Kupon Baru — Zakira Admin')

@section('content')
<div class="space-y-6">
    
    <!-- Header Halaman & Tombol Kembali -->
    <div class="flex items-center justify-between mb-6">
        <div class="font-medium text-zinc-800 dark:text-white text-2xl">Buat Kupon Baru</div>
        <a href="{{ route('kupon.index') }}" class="relative items-center font-medium justify-center gap-2 whitespace-nowrap h-10 text-sm rounded-lg ps-3 pe-4 inline-flex bg-transparent hover:bg-zinc-800/5 dark:hover:bg-white/15 text-zinc-800 dark:text-white transition">
            <svg class="shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                <path fill-rule="evenodd" d="M14 8a.75.75 0 0 1-.75.75H4.56l3.22 3.22a.75.75 0 1 1-1.06 1.06l-4.5-4.5a.75.75 0 0 1 0-1.06l4.5-4.5a.75.75 0 0 1 1.06 1.06L4.56 7.25h8.69A.75.75 0 0 1 14 8Z" clip-rule="evenodd"/>
            </svg>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Form Pembuatan Kupon -->
    <form wire:submit="save" class="space-y-8">
        
        <!-- Informasi Dasar -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-xs">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Kupon -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-zinc-800 dark:text-white">Nama Kupon</label>
                    <input type="text" wire:model="name" placeholder="Contoh: Diskon Lebaran 2026" class="w-full border rounded-lg appearance-none text-base sm:text-sm py-2 h-10 leading-[1.375rem] px-3 bg-white dark:bg-white/10 text-zinc-700 placeholder-zinc-400 dark:text-zinc-300 shadow-xs border-zinc-200 border-b-zinc-300/80 dark:border-white/10 focus:outline-none">
                    @error('name') <span class="text-sm font-medium text-red-500">{{ $message }}</span> @enderror
                </div>

                <!-- Kode Kupon -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-zinc-800 dark:text-white">Kode Kupon</label>
                    <div class="flex gap-2">
                        <input type="text" wire:model="code" placeholder="LEBARAN2026" class="w-full border rounded-lg appearance-none text-base sm:text-sm py-2 h-10 leading-[1.375rem] px-3 bg-white dark:bg-white/10 text-zinc-700 placeholder-zinc-400 dark:text-zinc-300 shadow-xs border-zinc-200 border-b-zinc-300/80 dark:border-white/10 focus:outline-none flex-1">
                        <button type="button" wire:click="generateCode" class="relative items-center font-medium justify-center gap-2 whitespace-nowrap h-10 text-sm rounded-lg px-4 inline-flex bg-white hover:bg-zinc-50 text-zinc-800 border border-zinc-200 shadow-xs transition">
                            <svg class="shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                                <path fill-rule="evenodd" d="M13.836 2.477a.75.75 0 0 1 .75.75v3.182a.75.75 0 0 1-.75.75h-3.182a.75.75 0 0 1 0-1.5h1.37l-.84-.841a4.5 4.5 0 0 0-7.08.932.75.75 0 0 1-1.3-.75 6 6 0 0 1 9.44-1.242l.842.84V3.227a.75.75 0 0 1 .75-.75Zm-.911 7.5A.75.75 0 0 1 13.199 11a6 6 0 0 1-9.44 1.241l-.84-.84v1.371a.75.75 0 0 1-1.5 0V9.591a.75.75 0 0 1 .75-.75H5.35a.75.75 0 0 1 0 1.5H3.98l.841.841a4.5 4.5 0 0 0 7.08-.932.75.75 0 0 1 1.025-.273Z" clip-rule="evenodd"/>
                            </svg>
                            <span>Generate</span>
                        </button>
                    </div>
                    @error('code') <span class="text-sm font-medium text-red-500">{{ $message }}</span> @enderror
                </div>

                <!-- Deskripsi -->
                <div class="md:col-span-2 space-y-2">
                    <label class="block text-sm font-medium text-zinc-800 dark:text-white">Deskripsi</label>
                    <textarea wire:model="description" placeholder="Deskripsi kupon (opsional)" rows="3" class="block p-3 w-full shadow-xs border rounded-lg bg-white dark:bg-white/10 resize-y text-base sm:text-sm text-zinc-700 placeholder-zinc-400 border-zinc-200 focus:outline-none"></textarea>
                    @error('description') <span class="text-sm font-medium text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Konfigurasi Diskon -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-xs">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Konfigurasi Diskon</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Tipe Diskon -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-zinc-800 dark:text-white">Tipe Diskon</label>
                    <select wire:model.live="type" class="appearance-none w-full px-3 pe-10 block h-10 py-2 text-base sm:text-sm rounded-lg shadow-xs border bg-white dark:bg-white/10 text-zinc-700 border-zinc-200 focus:outline-none">
                        <option value="percentage">Persentase (%)</option>
                        <option value="fixed">Nominal Tetap (Rp)</option>
                    </select>
                </div>

                <!-- Nilai Diskon -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-zinc-800 dark:text-white">Nilai Diskon</label>
                    <input type="number" wire:model="value" step="0.01" placeholder="10" class="w-full border rounded-lg appearance-none text-base sm:text-sm py-2 h-10 px-3 bg-white text-zinc-700 shadow-xs border-zinc-200 focus:outline-none">
                    <p class="text-sm text-zinc-500">Persentase diskon (0-100)</p>
                </div>

                <!-- Maksimal Diskon -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-zinc-800 dark:text-white">Maksimal Diskon</label>
                    <input type="number" wire:model="max_discount_amount" step="0.01" placeholder="100000" class="w-full border rounded-lg appearance-none text-base sm:text-sm py-2 h-10 px-3 bg-white text-zinc-700 shadow-xs border-zinc-200 focus:outline-none">
                    <p class="text-sm text-zinc-500">Batas maksimal diskon (opsional)</p>
                </div>
            </div>
        </div>

        <!-- Tanggal & Batasan Penggunaan -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-xs">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Tanggal & Batasan Penggunaan</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tanggal Mulai -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-zinc-800 dark:text-white">Tanggal Mulai</label>
                    <input type="datetime-local" wire:model="starts_at" class="w-full border rounded-lg appearance-none text-base sm:text-sm py-2 h-10 px-3 bg-white text-zinc-700 shadow-xs border-zinc-200 focus:outline-none">
                </div>

                <!-- Tanggal Kedaluwarsa -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-zinc-800 dark:text-white">Tanggal Kedaluwarsa</label>
                    <input type="datetime-local" wire:model="expires_at" class="w-full border rounded-lg appearance-none text-base sm:text-sm py-2 h-10 px-3 bg-white text-zinc-700 shadow-xs border-zinc-200 focus:outline-none">
                </div>

                <!-- Batas Total Penggunaan -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-zinc-800 dark:text-white">Batas Total Penggunaan</label>
                    <input type="number" wire:model="usage_limit" placeholder="100" class="w-full border rounded-lg appearance-none text-base sm:text-sm py-2 h-10 px-3 bg-white text-zinc-700 shadow-xs border-zinc-200 focus:outline-none">
                    <p class="text-sm text-zinc-500">Kosongkan untuk tidak terbatas</p>
                </div>

                <!-- Batas per Pelanggan -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-zinc-800 dark:text-white">Batas per Pelanggan</label>
                    <input type="number" wire:model="usage_limit_per_customer" placeholder="1" class="w-full border rounded-lg appearance-none text-base sm:text-sm py-2 h-10 px-3 bg-white text-zinc-700 shadow-xs border-zinc-200 focus:outline-none">
                    <p class="text-sm text-zinc-500">Berapa kali satu pelanggan bisa menggunakan kupon ini</p>
                </div>
            </div>
        </div>

        <!-- Target Customer -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-xs">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Target Voucher</h3>
                    <p class="text-sm text-gray-500 mt-1">Tentukan voucher ini dapat dipakai oleh Member, Non Member, atau semua pembeli.</p>
                </div>
                <span class="text-xs font-semibold rounded-full bg-blue-50 text-blue-700 px-3 py-1">Wajib dipilih</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <label class="cursor-pointer rounded-xl border p-4 transition border-blue-500 bg-blue-50/50 ring-1 ring-blue-200">
                    <input type="radio" wire:model.live="customer_scope" value="all" class="mr-2">
                    <span class="font-semibold text-gray-900">Semua Pembeli</span>
                    <p class="text-xs text-gray-500 mt-2">Berlaku untuk Member dan pembelian tanpa login.</p>
                </label>
                <label class="cursor-pointer rounded-xl border p-4 transition border-gray-200 hover:border-purple-300">
                    <input type="radio" wire:model.live="customer_scope" value="member" class="mr-2">
                    <span class="font-semibold text-gray-900">Khusus Member</span>
                    <p class="text-xs text-gray-500 mt-2">Hanya muncul dan dapat digunakan setelah member login.</p>
                </label>
                <label class="cursor-pointer rounded-xl border p-4 transition border-gray-200 hover:border-blue-300">
                    <input type="radio" wire:model.live="customer_scope" value="non_member" class="mr-2">
                    <span class="font-semibold text-gray-900">Khusus Non Member</span>
                    <p class="text-xs text-gray-500 mt-2">Hanya untuk Ready Stock yang checkout langsung tanpa login.</p>
                </label>
            </div>
        </div>

        <!-- Persyaratan -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-xs">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Persyaratan</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Minimal Pembelian -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-zinc-800 dark:text-white">Minimal Pembelian</label>
                    <input type="text" wire:model="minimum_amount" placeholder="Rp 50.000" class="w-full border rounded-lg appearance-none text-base sm:text-sm py-2 h-10 px-3 bg-white text-zinc-700 shadow-xs border-zinc-200 focus:outline-none">
                    <p class="text-sm text-zinc-500">Minimal total belanja untuk menggunakan kupon.</p>
                </div>

                <!-- Minimal Kuantitas -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-zinc-800 dark:text-white">Minimal Kuantitas</label>
                    <input type="number" wire:model="minimum_quantity" placeholder="2" class="w-full border rounded-lg appearance-none text-base sm:text-sm py-2 h-10 px-3 bg-white text-zinc-700 shadow-xs border-zinc-200 focus:outline-none">
                    <p class="text-sm text-zinc-500">Minimal jumlah item dalam keranjang</p>
                </div>
            </div>
        </div>

        <!-- Pengaturan Status & Aksi -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-xs space-y-4">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Pengaturan</h3>
            
            <div class="space-y-3">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" wire:model="active" class="w-4 h-4 rounded border-zinc-300 text-[var(--color-accent)] focus:ring-[var(--color-accent)]">
                    <span class="text-sm font-medium text-zinc-800">Aktifkan kupon</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" wire:model="new_customers_only" class="w-4 h-4 rounded border-zinc-300 text-[var(--color-accent)] focus:ring-[var(--color-accent)]">
                    <span class="text-sm font-medium text-zinc-800">Hanya untuk pelanggan baru</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" wire:model="stackable" class="w-4 h-4 rounded border-zinc-300 text-[var(--color-accent)] focus:ring-[var(--color-accent)]">
                    <span class="text-sm font-medium text-zinc-800">Dapat dikombinasi dengan kupon lain</span>
                </label>
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" wire:model="show_in_checkout" class="w-4 h-4 rounded border-zinc-300 text-[var(--color-accent)] focus:ring-[var(--color-accent)]">
                    <span class="text-sm font-medium text-zinc-800">Tampilkan di halaman checkout</span>
                </label>
            </div>
        </div>

        <!-- Tombol Aksi Bawah -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('kupon.index') }}" class="relative items-center font-medium justify-center gap-2 whitespace-nowrap h-10 text-sm rounded-lg px-4 inline-flex bg-transparent hover:bg-zinc-800/5 text-zinc-800 dark:text-white transition">
                Batal
            </a>
            <button type="submit" class="relative items-center font-medium justify-center gap-2 whitespace-nowrap h-10 text-sm rounded-lg px-4 inline-flex bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] text-[var(--color-accent-foreground)] border border-black/10 shadow-xs transition">
                <span>Simpan Kupon</span>
            </button>
        </div>

    </form>
</div>
@endsection