{{-- 
    resources/views/brands/_list.blade.php
    Tabel daftar brand yang dipakai bersama (index & create)
--}}
<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full table-auto border-collapse">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Logo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Brand</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Homepage</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Urutan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>

            <tbody class="bg-white">
                @forelse ($brands as $brand)
                    <tr class="border-b border-gray-200 last:border-b-0 hover:bg-gray-50 transition-colors">
                        {{-- Logo --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center w-16 h-16 rounded-lg border border-gray-200 bg-white overflow-hidden">
                                @if ($brand->logo && Storage::disk('public')->exists($brand->logo))
                                    <img src="{{ asset('storage/' . $brand->logo) }}"
                                         alt="{{ $brand->name }} logo"
                                         class="w-full h-full object-contain">
                                @else
                                    <span class="text-xs text-gray-400">Tanpa logo</span>
                                @endif
                            </div>
                        </td>

                        {{-- Nama & Deskripsi --}}
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-900">{{ $brand->name }}</div>
                            <div class="mt-1 max-w-xs text-xs text-gray-500 break-words">
                                {{ $brand->description ?? '-' }}
                            </div>
                        </td>

                        {{-- Produk --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $brand->products_count ?? $brand->products->count() }}
                        </td>

                        {{-- Tampil di Home --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <form action="{{ route('brands.toggleHome', $brand->id) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold transition-colors
                                               {{ $brand->show_on_home ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                    {{ $brand->show_on_home ? 'Tampil' : 'Sembunyi' }}
                                </button>
                            </form>
                        </td>

                        {{-- Urutan (Naik/Turun) --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <span class="min-w-7 text-center text-sm font-semibold text-gray-700">
                                    {{ $brand->home_order }}
                                </span>

                                <form action="{{ route('brands.moveOrder', ['id' => $brand->id, 'direction' => 'up']) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" title="Naik"
                                        class="rounded border border-gray-300 px-2 py-1 text-xs hover:bg-gray-50 transition-colors">
                                        ↑
                                    </button>
                                </form>

                                <form action="{{ route('brands.moveOrder', ['id' => $brand->id, 'direction' => 'down']) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" title="Turun"
                                        class="rounded border border-gray-300 px-2 py-1 text-xs hover:bg-gray-50 transition-colors">
                                        ↓
                                    </button>
                                </form>
                            </div>
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($brand->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                    Nonaktif
                                </span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-1">
                                <a href="{{ route('brands.edit', $brand->id) }}"
                                    class="h-8 inline-flex items-center px-3 text-sm text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                                    Edit
                                </a>

                                <form action="{{ route('brands.toggleStatus', $brand->id) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="h-8 inline-flex items-center px-3 text-sm hover:bg-gray-100 rounded-md transition-colors
                                                   {{ $brand->is_active ? 'text-red-600 hover:text-red-700' : 'text-green-600 hover:text-green-700' }}">
                                        {{ $brand->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>

                                <form action="{{ route('brands.destroy', $brand->id) }}" method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Hapus brand {{ $brand->name }}?');">
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
                        <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">
                            Belum ada brand yang tersedia.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if (method_exists($brands, 'links'))
    <div class="mt-4">
        {{ $brands->links() }}
    </div>
@endif
