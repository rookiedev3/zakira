<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Handle - Zakira</title>
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

    <div class="max-w-4xl mx-auto px-4 py-8">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-serif font-bold">Admin Handle</h1>
            @if (! $showCreateForm && ! $editingAdminHandle)
                <a href="{{ route('admin-handles.index', ['form' => 'create']) }}"
                   class="bg-brand-600 text-white px-5 py-2.5 rounded-full font-semibold text-sm hover:bg-brand-700">
                    + Tambah Admin Handle
                </a>
            @endif
        </div>

        @if (session('success'))
            <div class="bg-green-50 text-green-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
        @endif

        {{-- ==== FORM TAMBAH ==== --}}
        @if ($showCreateForm)
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="font-semibold text-lg mb-4">Buat Admin Handle</h2>

                @if ($errors->any())
                    <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('admin-handles.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="text-sm font-medium">Nama Admin Handle</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama admin handle" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>

                    <div class="flex gap-3">
                        <button class="bg-brand-600 text-white px-6 py-2.5 rounded-full font-semibold text-sm hover:bg-brand-700">
                            Buat Admin Handle
                        </button>
                        <a href="{{ route('admin-handles.index') }}" class="px-6 py-2.5 rounded-full font-semibold text-sm border">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        @endif

        {{-- ==== FORM EDIT ==== --}}
        @if ($editingAdminHandle)
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="font-semibold text-lg mb-4">Edit Admin Handle</h2>

                @if ($errors->any())
                    <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('admin-handles.update', $editingAdminHandle) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="text-sm font-medium">Nama Admin Handle</label>
                        <input type="text" name="name" value="{{ old('name', $editingAdminHandle->name) }}" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>

                    <div class="flex gap-3">
                        <button class="bg-brand-600 text-white px-6 py-2.5 rounded-full font-semibold text-sm hover:bg-brand-700">
                            Perbarui Admin Handle
                        </button>
                        <a href="{{ route('admin-handles.index') }}" class="px-6 py-2.5 rounded-full font-semibold text-sm border">
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
                        <th class="px-4 py-3">Tanggal Dibuat</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($adminHandles as $handle)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $handle->name }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $handle->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin-handles.index', ['edit' => $handle->id]) }}"
                                       class="text-brand-700 font-semibold text-xs">Edit</a>

                                    <form method="POST" action="{{ route('admin-handles.destroy', $handle) }}"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus admin handle ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-500 font-semibold text-xs">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-400">Belum ada admin handle.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $adminHandles->links() }}
        </div>
    </div>
</body>
</html>