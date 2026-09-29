@extends('layouts.sidebar')

@section('title', 'Manajemen Kupon — Zakira Admin')

@section('content')
<div class="space-y-6">
    
    <!-- Header Halaman -->
    <div class="font-medium text-zinc-800 dark:text-white text-2xl mb-2">
        Manajemen Kupon
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
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari kupon..." class="w-full border rounded-lg appearance-none text-base sm:text-sm py-2 h-10 leading-[1.375rem] ps-10 pe-3 bg-white dark:bg-white/10 text-zinc-700 placeholder-zinc-400 dark:text-zinc-300 dark:placeholder-zinc-400 shadow-xs border-zinc-200 border-b-zinc-300/80 dark:border-white/10 focus:outline-none">
                </div>
            </div>

            <!-- Kumpulan Filter & Tombol Buat Kupon -->
            <div class="flex items-center gap-3 flex-wrap">
                <!-- Filter Status -->
                <select wire:model.live="statusFilter" class="appearance-none ps-3 pe-10 block h-10 py-2 text-base sm:text-sm leading-[1.375rem] rounded-lg shadow-xs border bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 border-zinc-200 border-b-zinc-300/80 dark:border-white/10 focus:outline-none">
                    <option value="all">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                    <option value="expired">Kedaluwarsa</option>
                    <option value="used_up">Limit Tercapai</option>
                </select>

                <!-- Filter Tipe -->
                <select wire:model.live="typeFilter" class="appearance-none ps-3 pe-10 block h-10 py-2 text-base sm:text-sm leading-[1.375rem] rounded-lg shadow-xs border bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 border-zinc-200 border-b-zinc-300/80 dark:border-white/10 focus:outline-none">
                    <option value="all">Semua Tipe</option>
                    <option value="percentage">Persentase</option>
                    <option value="fixed">Nominal</option>
                </select>

                <!-- Filter Target -->
                <select wire:model.live="audienceFilter" class="appearance-none ps-3 pe-10 block h-10 py-2 text-base sm:text-sm leading-[1.375rem] rounded-lg shadow-xs border bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 border-zinc-200 border-b-zinc-300/80 dark:border-white/10 focus:outline-none">
                    <option value="all">Semua Target</option>
                    <option value="member">Khusus Member</option>
                    <option value="non_member">Semua Pembeli</option>
                </select>

                <a href="{{ route('kupon.create') }}" class="relative items-center font-medium justify-center gap-2 whitespace-nowrap h-10 text-sm rounded-lg ps-3 pe-4 inline-flex bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] text-[var(--color-accent-foreground)] border border-black/10 dark:border-0 shadow-xs transition">
    <svg class="shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
        <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z"/>
    </svg>
    <span>Buat Kupon</span>
</a>
            </div>
        </div>

        <!-- Tabel Data Kupon -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md border border-zinc-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase tracking-wider font-bold text-[10px]">
                        <tr>
                            <th class="px-6 py-3">Nama Kupon</th>
                            <th class="px-6 py-3">Kode</th>
                            <th class="px-6 py-3">Diskon</th>
                            <th class="px-6 py-3">Target</th>
                            <th class="px-6 py-3">Kedaluwarsa</th>
                            <th class="px-6 py-3">Penggunaan</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-zinc-700">
                        
                        <!-- Contoh Baris Data 1 -->
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">Raya</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded font-semibold text-zinc-800">SLUWTM6E</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">Rp 10.000</div>
                                <div class="text-gray-500 text-[11px]">Min: Rp 50.000</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">Semua</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                <div>18/09/2026</div>
                                <div class="text-[10px] text-gray-400">16:06</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <div class="font-medium text-zinc-800">1 / 1000</div>
                                    <div class="w-24 bg-gray-200 rounded-full h-1.5 mt-1 overflow-hidden">
                                        <div class="bg-blue-600 h-1.5 rounded-full" style="width: 10%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Kedaluwarsa
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2 text-xs">
                                    <a href="{{ route('kupon.edit') }}" class="h-8 px-3 inline-flex items-center rounded-md hover:bg-zinc-800/5 text-zinc-800 transition">Edit</a>
                                    <button type="button" class="h-8 px-3 inline-flex items-center rounded-md hover:bg-zinc-800/5 text-green-600 hover:text-green-800 transition">Duplikasi</button>
                                    <button type="button" class="h-8 px-3 inline-flex items-center rounded-md hover:bg-zinc-800/5 text-amber-600 hover:text-amber-700 transition">Nonaktifkan</button>
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