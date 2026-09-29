@extends('layouts.sidebar')

@section('title', 'Manajemen Kupon — Zakira Admin')

@section('content')
<div class="space-y-6">

    <div class="font-medium text-zinc-800 dark:text-white text-2xl mb-2">
        Manajemen Kupon
    </div>

    @if (session('success'))
        <div class="bg-green-50 text-green-700 text-sm rounded-lg px-4 py-3">{{ session('success') }}</div>
    @endif

    <div class="mt-6 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            <div class="flex-1 max-w-md">
                <div class="w-full relative block group/input">
                    <div class="pointer-events-none absolute top-0 bottom-0 border-s border-transparent flex items-center justify-center text-xs text-zinc-400/75 dark:text-white/60 ps-3 start-0">
                        <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <input type="text" id="kupon-search" placeholder="Cari kupon..." class="w-full border rounded-lg appearance-none text-base sm:text-sm py-2 h-10 leading-[1.375rem] ps-10 pe-3 bg-white dark:bg-white/10 text-zinc-700 placeholder-zinc-400 dark:text-zinc-300 dark:placeholder-zinc-400 shadow-xs border-zinc-200 border-b-zinc-300/80 dark:border-white/10 focus:outline-none">
                </div>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <select id="kupon-status-filter" class="appearance-none ps-3 pe-10 block h-10 py-2 text-base sm:text-sm leading-[1.375rem] rounded-lg shadow-xs border bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 border-zinc-200 border-b-zinc-300/80 dark:border-white/10 focus:outline-none">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                    <option value="expired">Kedaluwarsa</option>
                    <option value="used_up">Limit Tercapai</option>
                </select>

                <select id="kupon-type-filter" class="appearance-none ps-3 pe-10 block h-10 py-2 text-base sm:text-sm leading-[1.375rem] rounded-lg shadow-xs border bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 border-zinc-200 border-b-zinc-300/80 dark:border-white/10 focus:outline-none">
                    <option value="">Semua Tipe</option>
                    <option value="percentage">Persentase</option>
                    <option value="fixed">Nominal</option>
                </select>

                <select id="kupon-audience-filter" class="appearance-none ps-3 pe-10 block h-10 py-2 text-base sm:text-sm leading-[1.375rem] rounded-lg shadow-xs border bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 border-zinc-200 border-b-zinc-300/80 dark:border-white/10 focus:outline-none">
                    <option value="">Semua Target</option>
                    <option value="member">Khusus Member</option>
                    <option value="non_member">Non Member</option>
                    <option value="all">Semua Pembeli</option>
                </select>

                <a href="{{ route('kupon.create') }}" class="relative items-center font-medium justify-center gap-2 whitespace-nowrap h-10 text-sm rounded-lg ps-3 pe-4 inline-flex bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] text-[var(--color-accent-foreground)] border border-black/10 dark:border-0 shadow-xs transition">
                    <svg class="shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z"/>
                    </svg>
                    <span>Buat Kupon</span>
                </a>
            </div>
        </div>

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
                        @forelse ($coupons as $coupon)
                            @php
                                $statusColor = match ($coupon->status) {
                                    'active' => 'bg-green-100 text-green-800',
                                    'inactive' => 'bg-gray-100 text-gray-600',
                                    'expired' => 'bg-red-100 text-red-800',
                                    'used_up' => 'bg-amber-100 text-amber-800',
                                };
                                $usagePercent = $coupon->usage_limit
                                    ? min(100, round(($coupon->used_count / $coupon->usage_limit) * 100))
                                    : 0;
                            @endphp
                            <tr class="hover:bg-gray-50"
                                data-status="{{ $coupon->status }}"
                                data-type="{{ $coupon->type }}"
                                data-audience="{{ $coupon->customer_scope }}"
                                data-search="{{ strtolower($coupon->name . ' ' . $coupon->code) }}">
<td class="px-6 py-4">
    <div class="max-w-[200px]">
        <div class="font-medium text-gray-900 truncate" title="{{ $coupon->name }}">{{ $coupon->name }}</div>
        @if ($coupon->description)
            <div class="text-gray-400 text-[11px] mt-0.5 truncate" title="{{ $coupon->description }}">{{ $coupon->description }}</div>
        @endif
    </div>
</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded font-semibold text-zinc-800">{{ $coupon->code }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">{{ $coupon->discount_label }}</div>
                                    @if ($coupon->minimum_amount)
                                        <div class="text-gray-500 text-[11px]">Min: Rp {{ number_format($coupon->minimum_amount, 0, ',', '.') }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">{{ $coupon->audience_label }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                    @if ($coupon->expires_at)
                                        <div>{{ $coupon->expires_at->format('d/m/Y') }}</div>
                                        <div class="text-[10px] text-gray-400">{{ $coupon->expires_at->format('H:i') }}</div>
                                    @else
                                        <span class="text-gray-400">Tidak ada</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        <div class="font-medium text-zinc-800">{{ $coupon->used_count }} / {{ $coupon->usage_limit ?? '∞' }}</div>
                                        @if ($coupon->usage_limit)
                                            <div class="w-24 bg-gray-200 rounded-full h-1.5 mt-1 overflow-hidden">
                                                <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $usagePercent }}%"></div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                                        {{ $coupon->status_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2 text-xs">
                                        <a href="{{ route('kupon.edit', $coupon) }}" class="h-8 px-3 inline-flex items-center rounded-md hover:bg-zinc-800/5 text-zinc-800 transition">Edit</a>

                                        <form method="POST" action="{{ route('kupon.duplicate', $coupon) }}">
                                            @csrf
                                            <button type="submit" class="h-8 px-3 inline-flex items-center rounded-md hover:bg-zinc-800/5 text-green-600 hover:text-green-800 transition">Duplikasi</button>
                                        </form>

                                        <form method="POST" action="{{ route('kupon.toggle', $coupon) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="h-8 px-3 inline-flex items-center rounded-md hover:bg-zinc-800/5 text-amber-600 hover:text-amber-700 transition">
                                                {{ $coupon->active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('kupon.destroy', $coupon) }}" onsubmit="return confirm('Yakin hapus kupon {{ $coupon->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="h-8 px-3 inline-flex items-center rounded-md hover:bg-zinc-800/5 text-red-600 hover:text-red-800 transition">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-gray-400">Tidak ada kupon ditemukan.</td>
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