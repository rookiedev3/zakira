<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customer Service - Zakira</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50: '#fdf6f0', 100: '#f8e9db', 400: '#c9995f', 600: '#9c6b3a', 700: '#7d5330' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800">

    <div class="max-w-6xl mx-auto px-4 py-8">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-serif font-bold">Customer Service</h1>
            @if (! $showCreateForm && ! $editingCustomerService)
                <a href="{{ route('customer-services.index', ['form' => 'create']) }}"
                   class="bg-brand-600 text-white px-5 py-2.5 rounded-full font-semibold text-sm hover:bg-brand-700">
                    + Tambah Customer Service
                </a>
            @endif
        </div>

        @if (session('success'))
            <div class="bg-green-50 text-green-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
        @endif

        {{-- ==== FORM TAMBAH ==== --}}
        @if ($showCreateForm)
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="font-semibold text-lg mb-4">Tambah Customer Service</h2>

                @if ($errors->any())
                    <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('customer-services.store') }}" class="grid md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="text-sm font-medium">Nama</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama customer service" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>
                    <div>
                        <label class="text-sm font-medium">Nomor Telepon</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number') }}" placeholder="0812xxxxxxx" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>
                    <div>
                        <label class="text-sm font-medium">Urutan</label>
                        <input type="number" name="order" value="{{ old('order', 0) }}" min="0" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>
                    <div class="flex items-center">
                        <label class="flex items-center gap-2 text-sm mt-6">
                            <input type="checkbox" name="status" value="aktif" checked class="rounded border-gray-300">
                            Aktif
                        </label>
                    </div>
                    <div class="md:col-span-2">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="is_floating_whatsapp" value="1" @checked(old('is_floating_whatsapp')) class="rounded border-gray-300">
                            Set ke Floating WhatsApp
                        </label>
                        <p class="text-xs text-gray-400 mt-1">Hanya satu nomor yang bisa diset sebagai floating WhatsApp</p>
                    </div>

                    <div class="md:col-span-2 flex gap-3 mt-2">
                        <button class="bg-brand-600 text-white px-6 py-2.5 rounded-full font-semibold text-sm hover:bg-brand-700">
                            Tambah Customer Service
                        </button>
                        <a href="{{ route('customer-services.index') }}" class="px-6 py-2.5 rounded-full font-semibold text-sm border">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        @endif

        {{-- ==== FORM EDIT ==== --}}
        @if ($editingCustomerService)
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="font-semibold text-lg mb-4">Edit Customer Service</h2>

                @if ($errors->any())
                    <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('customer-services.update', $editingCustomerService) }}" class="grid md:grid-cols-2 gap-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="text-sm font-medium">Nama</label>
                        <input type="text" name="name" value="{{ old('name', $editingCustomerService->name) }}" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>
                    <div>
                        <label class="text-sm font-medium">Nomor Telepon</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number', $editingCustomerService->phone_number) }}" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>
                    <div>
                        <label class="text-sm font-medium">Urutan</label>
                        <input type="number" name="order" value="{{ old('order', $editingCustomerService->order) }}" min="0" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>
                    <div class="flex items-center">
                        <label class="flex items-center gap-2 text-sm mt-6">
                            <input type="checkbox" name="status" value="aktif" @checked($editingCustomerService->status === 'aktif') class="rounded border-gray-300">
                            Aktif
                        </label>
                    </div>
                    <div class="md:col-span-2">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="is_floating_whatsapp" value="1" @checked(old('is_floating_whatsapp', $editingCustomerService->is_floating_whatsapp)) class="rounded border-gray-300">
                            Set ke Floating WhatsApp
                        </label>
                        <p class="text-xs text-gray-400 mt-1">Hanya satu nomor yang bisa diset sebagai floating WhatsApp</p>
                    </div>

                    <div class="md:col-span-2 flex gap-3 mt-2">
                        <button class="bg-brand-600 text-white px-6 py-2.5 rounded-full font-semibold text-sm hover:bg-brand-700">
                            Perbarui Customer Service
                        </button>
                        <a href="{{ route('customer-services.index') }}" class="px-6 py-2.5 rounded-full font-semibold text-sm border">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        @endif

        {{-- ==== TABEL ==== --}}
        <div class="bg-white rounded-xl shadow-sm overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Nomor Telepon</th>
                        <th class="px-4 py-3">Urutan</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Floating WhatsApp</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($customerServices as $cs)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $cs->name }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ $cs->whatsapp_url }}" target="_blank" rel="noopener"
                                   class="text-green-600 hover:underline">
                                    {{ $cs->phone_number }}
                                    <div class="text-xs text-green-500">WhatsApp</div>
                                </a>
                            </td>
                            <td class="px-4 py-3">{{ $cs->order }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $cs->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($cs->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($cs->is_floating_whatsapp)
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-brand-100 text-brand-700">Floating WhatsApp</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('customer-services.index', ['edit' => $cs->id]) }}"
                                       class="text-brand-700 font-semibold text-xs">Edit</a>

                                    <form method="POST" action="{{ route('customer-services.status', $cs) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="text-amber-600 font-semibold text-xs">
                                            {{ $cs->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('customer-services.destroy', $cs) }}"
                                          onsubmit="return confirm('Yakin hapus {{ $cs->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-500 font-semibold text-xs">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">Belum ada data customer service.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $customerServices->links() }}
        </div>
    </div>
</body>
</html>