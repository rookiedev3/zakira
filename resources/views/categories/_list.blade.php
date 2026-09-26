{{-- ------------------------------------------------------------
     Partial: tabel daftar kategori (sama persis dengan index)
------------------------------------------------------------ --}}
<div class="bg-white shadow rounded-lg overflow-hidden mt-8">
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
            @forelse ($categories as $category)
                <tr class="border-b border-gray-200 last:border-b-0 hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $category->name }}
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($category->type)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                {{ ucfirst($category->type) }}
                            </span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $category->description ?? '-' }}
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $category->products->count() ?? 0 }}
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($category->is_active)
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
                        <div class="flex items-center justify-end space-x-1">
                            <a href="{{ route('categories.edit', $category->id) }}"
                               class="h-8 inline-flex items-center px-3 text-sm text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                                Edit
                            </a>

                            <form action="{{ route('categories.toggle', $category->id) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="h-8 inline-flex items-center px-3 text-sm {{ $category->is_active ? 'text-red-600 hover:text-red-700' : 'text-green-600 hover:text-green-700' }} hover:bg-gray-100 rounded-md transition-colors">
                                    {{ $category->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>

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