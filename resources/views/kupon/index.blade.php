@extends('layouts.sidebar')

@section('title', 'Manajemen Kupon — Zakira Admin')

@section('content')
<div class="space-y-6">

    <div class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white mb-2">
        Manajemen Kupon
    </div>

    @if (session('success'))
        <div class="bg-green-50 text-green-700 text-sm rounded-lg px-4 py-3 border border-green-200">{{ session('success') }}</div>
    @endif

    <div class="mt-6 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            <div class="flex-1 max-w-md">
                <div class="w-full relative block group/input">
                    <div class="pointer-events-none absolute top-0 bottom-0 border-s border-transparent flex items-center justify-center text-xs text-gray-400 dark:text-white/60 ps-3 start-0">
                        <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <input type="text" id="kupon-search" placeholder="Cari kupon..." class="w-full border border-gray-300 rounded-lg appearance-none text-sm py-2 h-10 leading-[1.375rem] ps-10 pe-4 bg-white dark:bg-white/10 text-gray-900 placeholder-gray-400 dark:text-zinc-300 dark:placeholder-zinc-400 dark:border-white/10 focus:outline-none focus:border-[#8B5E3C] focus:ring-1 focus:ring-[#8B5E3C]">
                </div>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <select id="kupon-status-filter" class="block h-10 py-2 px-4 text-sm leading-[1.375rem] rounded-lg border border-gray-300 bg-white dark:bg-white/10 text-gray-900 dark:text-zinc-300 dark:border-white/10 focus:outline-none focus:border-[#8B5E3C] focus:ring-1 focus:ring-[#8B5E3C]">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="upcoming">Belum Aktif</option>
                    <option value="inactive">Nonaktif</option>
                    <option value="expired">Kedaluwarsa</option>
                    <option value="used_up">Limit Tercapai</option>
                </select>

                <select id="kupon-type-filter" class="block h-10 py-2 px-4 text-sm leading-[1.375rem] rounded-lg border border-gray-300 bg-white dark:bg-white/10 text-gray-900 dark:text-zinc-300 dark:border-white/10 focus:outline-none focus:border-[#8B5E3C] focus:ring-1 focus:ring-[#8B5E3C]">
                    <option value="">Semua Tipe</option>
                    <option value="percentage">Persentase</option>
                    <option value="fixed">Nominal</option>
                </select>

                <select id="kupon-audience-filter" class="block h-10 py-2 px-4 text-sm leading-[1.375rem] rounded-lg border border-gray-300 bg-white dark:bg-white/10 text-gray-900 dark:text-zinc-300 dark:border-white/10 focus:outline-none focus:border-[#8B5E3C] focus:ring-1 focus:ring-[#8B5E3C]">
                    <option value="">Semua Target</option>
                    <option value="member">Khusus Member</option>
                    <option value="non_member">Non Member</option>
                    <option value="all">Semua Pembeli</option>
                </select>

                <a href="{{ route('kupon.create') }}" class="relative items-center font-medium justify-center gap-2 whitespace-nowrap h-10 text-sm rounded-lg ps-4 pe-5 inline-flex bg-[#8B5E3C] hover:bg-[#7a5134] text-white shadow-sm transition-colors">
                    <svg class="shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z"/>
                    </svg>
                    <span>Buat Kupon</span>
                </a>
            </div>
        </div>

        <div class="bg-white shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden rounded-xl">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                    <thead class="bg-slate-50 text-gray-500 uppercase tracking-wide font-medium text-xs">
                        <tr>
                            <th class="px-6 py-3 font-medium">Nama Kupon</th>
                            <th class="px-6 py-3 font-medium">Kode</th>
                            <th class="px-6 py-3 font-medium">Diskon</th>
                            <th class="px-6 py-3 font-medium">Target</th>
                            <th class="px-6 py-3 font-medium">Periode</th>
                            <th class="px-6 py-3 font-medium">Penggunaan</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                            <th class="px-6 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-gray-900">
                        @forelse ($coupons as $coupon)
                            @php
                                $statusColor = match ($coupon->status) {
                                    'active' => 'bg-green-100 text-green-800',
                                    'upcoming' => 'bg-blue-100 text-blue-800',
                                    'inactive' => 'bg-gray-100 text-gray-600',
                                    'expired' => 'bg-red-100 text-red-800',
                                    'used_up' => 'bg-amber-100 text-amber-800',
                                    default => 'bg-gray-100 text-gray-600',
                                };
                                $usagePercent = $coupon->usage_limit
                                    ? min(100, round(($coupon->used_count / $coupon->usage_limit) * 100))
                                    : 0;
                            @endphp
                            <tr class="hover:bg-gray-50/60 transition-colors"
                                data-status="{{ $coupon->status }}"
                                data-type="{{ $coupon->type }}"
                                data-audience="{{ $coupon->customer_scope }}"
                                data-search="{{ strtolower($coupon->name . ' ' . $coupon->code) }}">
                                <td class="px-6 py-4">
                                    <div class="max-w-[200px]">
                                        <div class="text-sm font-medium text-gray-900 truncate" title="{{ $coupon->name }}">{{ $coupon->name }}</div>
                                        @if ($coupon->description)
                                            <div class="text-gray-500 text-xs mt-0.5 truncate" title="{{ $coupon->description }}">{{ $coupon->description }}</div>
                                        @endif
                                    </div>
                                </td>
                                {{-- Kolom Kode: tampilan asli --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded font-semibold text-zinc-800">{{ $coupon->code }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        @if ($coupon->type === 'percentage')
                                            {{ number_format($coupon->value, 2, '.', '') }}%
                                            @if ($coupon->max_discount_amount)
                                                (max Rp {{ number_format($coupon->max_discount_amount, 0, ',', '.') }})
                                            @endif
                                        @else
                                            Rp {{ number_format($coupon->value, 0, ',', '.') }}
                                        @endif
                                    </div>
                                    @if ($coupon->minimum_amount)
                                        <div class="text-gray-500 text-xs">Min: Rp {{ number_format($coupon->minimum_amount, 0, ',', '.') }}</div>
                                    @endif
                                </td>
                                {{-- Kolom Target: tampilan asli --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">{{ $coupon->audience_label }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                    <div class="text-xs">
                                        Mulai:
                                        {{ $coupon->starts_at ? $coupon->starts_at->format('d/m/Y H:i') : 'Langsung' }}
                                    </div>
                                    <div class="text-xs">
                                        Berakhir:
                                        {{ $coupon->expires_at ? $coupon->expires_at->format('d/m/Y H:i') : 'Tidak ada' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900">{{ $coupon->used_count }} / {{ $coupon->usage_limit ?? '∞' }}</div>
                                        @if ($coupon->usage_limit)
                                            <div class="w-24 bg-gray-200 rounded-full h-1.5 mt-1 overflow-hidden">
                                                <div class="bg-[#8B5E3C] h-1.5 rounded-full" style="width: {{ $usagePercent }}%"></div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                {{-- Kolom Status: tampilan asli --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                                        {{ $coupon->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <div class="flex items-center justify-end space-x-2 text-sm">
                                        <a href="{{ route('kupon.edit', $coupon) }}" class="h-8 px-3 inline-flex items-center rounded-md hover:bg-zinc-800/5 text-gray-900 transition-colors">Edit</a>

                                        <form method="POST" action="{{ route('kupon.duplicate', $coupon) }}">
                                            @csrf
                                            <button type="submit" class="h-8 px-3 inline-flex items-center rounded-md hover:bg-zinc-800/5 text-gray-900 transition-colors">Duplikasi</button>
                                        </form>

                                        <form method="POST" action="{{ route('kupon.toggle', $coupon) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="h-8 px-3 inline-flex items-center rounded-md hover:bg-zinc-800/5 text-gray-900 transition-colors">
                                                {{ $coupon->active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('kupon.destroy', $coupon) }}" onsubmit="return confirm('Yakin hapus kupon {{ $coupon->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="h-8 px-3 inline-flex items-center rounded-md hover:bg-zinc-800/5 text-gray-900 transition-colors">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada kupon ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
    (function () {
        if (window.__kuponPageInit) return;
        window.__kuponPageInit = true;

        function applyFilter() {
            var q = (document.getElementById('kupon-search')?.value || '').toLowerCase();
            var status = document.getElementById('kupon-status-filter')?.value || '';
            var type = document.getElementById('kupon-type-filter')?.value || '';
            var audience = document.getElementById('kupon-audience-filter')?.value || '';

            document.querySelectorAll('[data-search]').forEach(function (row) {
                var okText = q === '' || row.dataset.search.includes(q);
                var okStatus = status === '' || row.dataset.status === status;
                var okType = type === '' || row.dataset.type === type;
                var okAudience = audience === '' || row.dataset.audience === audience;
                row.style.display = (okText && okStatus && okType && okAudience) ? '' : 'none';
            });
        }

        document.addEventListener('input', function (e) {
            if (e.target.id === 'kupon-search') applyFilter();
        });
        document.addEventListener('change', function (e) {
            if (['kupon-status-filter', 'kupon-type-filter', 'kupon-audience-filter'].includes(e.target.id)) applyFilter();
        });
    })();
</script>
@endsection