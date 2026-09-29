@extends('layouts.sidebar')

@section('title', 'Manajemen Seller — Zakira Admin')

@section('content')
<div class="space-y-6">
    
    <!-- Header Halaman -->
    <div class="font-medium text-zinc-800 dark:text-white text-2xl mb-2">
        Kelola Seller
    </div>

    <!-- Bar Filter, Pencarian & Tombol Aksi -->
    <div class="mt-6 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            
            <!-- Input Cari -->
            <div class="flex-1 max-w-md">
                <div class="w-full relative block group/input">
                    <div class="pointer-events-none absolute top-0 bottom-0 border-s border-transparent flex items-center justify-center text-xs text-zinc-400/75 dark:text-white/60 ps-3 start-0">
                        <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari ID Seller, nama..." class="w-full border rounded-lg appearance-none text-base sm:text-sm py-2 h-10 leading-[1.375rem] ps-10 pe-3 bg-white dark:bg-white/10 text-zinc-700 placeholder-zinc-400 dark:text-zinc-300 dark:placeholder-zinc-400 shadow-xs border-zinc-200 border-b-zinc-300/80 dark:border-white/10 focus:outline-none">
                </div>
            </div>

            <!-- Kumpulan Filter & Tombol Buat Seller -->
            <div class="flex items-center gap-3 flex-wrap">
                <!-- Filter Brand -->
                <select wire:model.live="brandFilter" class="appearance-none ps-3 pe-10 block h-10 py-2 text-base sm:text-sm leading-[1.375rem] rounded-lg shadow-xs border bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 border-zinc-200 border-b-zinc-300/80 dark:border-white/10 focus:outline-none">
                    <option value="">Semua Brand</option>
                    <option value="1">ZAKIRA</option>
                </select>

                <button type="button" class="relative items-center font-medium justify-center gap-2 whitespace-nowrap h-10 text-sm rounded-lg ps-3 pe-4 inline-flex bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] text-[var(--color-accent-foreground)] border border-black/10 dark:border-0 shadow-xs transition">
                    <svg class="shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z"/>
                    </svg>
                    <span>Tambah Seller</span>
                </button>
            </div>
        </div>

        <!-- Tabel Data Seller -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md border border-zinc-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase tracking-wider font-bold text-[10px]">
                        <tr>
                            <th class="px-6 py-3">ID Seller</th>
                            <th class="px-6 py-3">Nama</th>
                            <th class="px-6 py-3">Brand</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-zinc-700">
                        
                        <!-- Contoh Baris Data Seller -->
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-sm bg-gray-50/50 font-semibold text-zinc-800">
                                asdk123
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                AKN
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">ZAKIRA</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2 text-xs">
                                    <button type="button" class="h-8 px-3 inline-flex items-center rounded-md hover:bg-zinc-800/5 text-zinc-800 transition">Edit</button>
                                    <button type="button" class="h-8 px-3 inline-flex items-center rounded-md hover:bg-zinc-800/5 text-red-600 hover:text-red-800 transition">Hapus</button>
                                </div>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection