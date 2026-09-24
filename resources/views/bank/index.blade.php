<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Informasi Bank - Zakira</title>
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

    <div class="max-w-5xl mx-auto px-4 py-8">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-serif font-bold">Informasi Bank</h1>
            @if (! $showCreateForm && ! $editingBankAccount)
                <a href="{{ route('banks.index', ['form' => 'create']) }}"
                   class="bg-brand-600 text-white px-5 py-2.5 rounded-full font-semibold text-sm hover:bg-brand-700">
                    + Tambah Bank
                </a>
            @endif
        </div>

        @if (session('success'))
            <div class="bg-green-50 text-green-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
        @endif

        {{-- ==== FORM TAMBAH BANK ==== --}}
        @if ($showCreateForm)
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="font-semibold text-lg mb-4">Tambah Informasi Bank</h2>

                @if ($errors->any())
                    <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('banks.store') }}" class="grid md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="text-sm font-medium">Nama Bank</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name') }}" placeholder="Masukkan nama bank" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>
                    <div>
                        <label class="text-sm font-medium">Nomor Rekening</label>
                        <input type="text" name="account_number" value="{{ old('account_number') }}" placeholder="Masukkan nomor rekening" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium">Nama Pemilik Rekening</label>
                        <input type="text" name="account_holder_name" value="{{ old('account_holder_name') }}" placeholder="Masukkan nama pemilik rekening" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>

                    <div class="md:col-span-2 flex gap-3 mt-2">
                        <button class="bg-brand-600 text-white px-6 py-2.5 rounded-full font-semibold text-sm hover:bg-brand-700">
                            Tambah Bank
                        </button>
                        <a href="{{ route('banks.index') }}" class="px-6 py-2.5 rounded-full font-semibold text-sm border">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        @endif

        {{-- ==== FORM EDIT BANK ==== --}}
        @if ($editingBankAccount)
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="font-semibold text-lg mb-4">Edit Informasi Bank</h2>

                @if ($errors->any())
                    <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('banks.update', $editingBankAccount) }}" class="grid md:grid-cols-2 gap-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="text-sm font-medium">Nama Bank</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', $editingBankAccount->bank_name) }}" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>
                    <div>
                        <label class="text-sm font-medium">Nomor Rekening</label>
                        <input type="text" name="account_number" value="{{ old('account_number', $editingBankAccount->account_number) }}" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-sm font-medium">Nama Pemilik Rekening</label>
                        <input type="text" name="account_holder_name" value="{{ old('account_holder_name', $editingBankAccount->account_holder_name) }}" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>

                    <div class="md:col-span-2 flex gap-3 mt-2">
                        <button class="bg-brand-600 text-white px-6 py-2.5 rounded-full font-semibold text-sm hover:bg-brand-700">
                            Perbarui Bank
                        </button>
                        <a href="{{ route('banks.index') }}" class="px-6 py-2.5 rounded-full font-semibold text-sm border">
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
                        <th class="px-4 py-3">Nama Bank</th>
                        <th class="px-4 py-3">Nomor Rekening</th>
                        <th class="px-4 py-3">Nama Pemilik</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($bankAccounts as $bank)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $bank->bank_name }}</td>
                            <td class="px-4 py-3">{{ $bank->account_number }}</td>
                            <td class="px-4 py-3">{{ $bank->account_holder_name }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $bank->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($bank->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('banks.index', ['edit' => $bank->id]) }}"
                                       class="text-brand-700 font-semibold text-xs">Edit</a>

                                    <form method="POST" action="{{ route('banks.status', $bank) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="text-amber-600 font-semibold text-xs">
                                            {{ $bank->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('banks.destroy', $bank) }}"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus bank ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-500 font-semibold text-xs">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada data bank.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $bankAccounts->links() }}
        </div>
    </div>
</body>
</html>