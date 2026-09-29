<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Pengguna - Zakira</title>
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

    <div class="max-w-7xl mx-auto px-4 py-8">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-serif font-bold">Kelola Pengguna</h1>
                <p class="text-gray-500 text-sm">Total: {{ $users->total() }} pengguna</p>
            </div>
            @if (! $showCreateForm && ! $editingUser)
                <a href="{{ route('users.index', ['form' => 'create']) }}"
                   class="bg-brand-600 text-white px-5 py-2.5 rounded-full font-semibold text-sm hover:bg-brand-700">
                    + Tambah Pengguna
                </a>
            @endif
        </div>

        @if (session('success'))
            <div class="bg-green-50 text-green-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ session('error') }}</div>
        @endif

        {{-- ==== FORM TAMBAH PENGGUNA ==== --}}
        @if ($showCreateForm)
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="font-semibold text-lg mb-4">Buat Pengguna</h2>

                @if ($errors->any())
                    <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('users.store') }}" class="grid md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="text-sm font-medium">Nama</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>
                    <div>
                        <label class="text-sm font-medium">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>
                    <div>
                        <label class="text-sm font-medium">Password</label>
                        <input type="password" name="password" placeholder="Masukkan password" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>
                    <div>
                        <label class="text-sm font-medium">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" placeholder="Konfirmasi password" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>
                    <div>
                        <label class="text-sm font-medium">Role</label>
                        <select name="role" required class="w-full border rounded-lg px-4 py-2 mt-1">
                            <option value="">Pilih Role</option>
                            <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                            <option value="customer" @selected(old('role') === 'customer')>Customer</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium">Status</label>
                        <select name="status" class="w-full border rounded-lg px-4 py-2 mt-1">
                            <option value="aktif" selected>Aktif</option>
                            <option value="tidak_aktif">Tidak Aktif</option>
                            <option value="ditangguhkan">Ditangguhkan</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium">Tipe Customer Zakira</label>
                        <select name="customer_type" class="w-full border rounded-lg px-4 py-2 mt-1">
                            <option value="umum" selected>Umum / Non Member</option>
                            <option value="member">Member</option>
                            <option value="distributor">Distributor</option>
                        </select>
                    </div>
                    <div class="flex items-center">
                        <label class="flex items-center gap-2 text-sm mt-6">
                            <input type="checkbox" name="email_verified" value="1" class="rounded border-gray-300">
                            Email Terverifikasi
                        </label>
                    </div>

                    <div class="md:col-span-2 flex gap-3 mt-2">
                        <button class="bg-brand-600 text-white px-6 py-2.5 rounded-full font-semibold text-sm hover:bg-brand-700">
                            Buat Pengguna
                        </button>
                        <a href="{{ route('users.index') }}" class="px-6 py-2.5 rounded-full font-semibold text-sm border">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        @endif

        {{-- ==== FORM EDIT PENGGUNA ==== --}}
        @if ($editingUser)
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="font-semibold text-lg mb-4">Edit Pengguna</h2>

                @if ($errors->any())
                    <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('users.update', $editingUser) }}" class="grid md:grid-cols-2 gap-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="text-sm font-medium">Nama</label>
                        <input type="text" name="name" value="{{ old('name', $editingUser->name) }}" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>
                    <div>
                        <label class="text-sm font-medium">Email</label>
                        <input type="email" name="email" value="{{ old('email', $editingUser->email) }}" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>
                    <div>
                        <label class="text-sm font-medium">Role</label>
                        <select name="role" required class="w-full border rounded-lg px-4 py-2 mt-1">
                            <option value="admin" @selected($editingUser->role === 'admin')>Admin</option>
                            <option value="customer" @selected($editingUser->role === 'customer')>Customer</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium">Status</label>
                        <select name="status" class="w-full border rounded-lg px-4 py-2 mt-1">
                            <option value="aktif" @selected($editingUser->status === 'aktif')>Aktif</option>
                            <option value="tidak_aktif" @selected($editingUser->status === 'tidak_aktif')>Tidak Aktif</option>
                            <option value="ditangguhkan" @selected($editingUser->status === 'ditangguhkan')>Ditangguhkan</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium">Tipe Customer Zakira</label>
                        <select name="customer_type" class="w-full border rounded-lg px-4 py-2 mt-1">
                            <option value="umum" @selected($editingUser->customer_type === 'umum')>Umum / Non Member</option>
                            <option value="member" @selected($editingUser->customer_type === 'member')>Member</option>
                            <option value="distributor" @selected($editingUser->customer_type === 'distributor')>Distributor</option>
                        </select>
                    </div>
                    <div class="flex items-center">
                        <label class="flex items-center gap-2 text-sm mt-6">
                            <input type="checkbox" name="reset_password" value="1" class="rounded border-gray-300">
                            Reset Password (akan dikirim ke email pengguna)
                        </label>
                    </div>
                    <div class="flex items-center">
                        <label class="flex items-center gap-2 text-sm mt-6">
                            <input type="checkbox" name="email_verified" value="1" class="rounded border-gray-300"
                                   @checked($editingUser->email_verified_at)>
                            Email Terverifikasi
                        </label>
                    </div>

                    <div class="md:col-span-2 flex gap-3 mt-2">
                        <button class="bg-brand-600 text-white px-6 py-2.5 rounded-full font-semibold text-sm hover:bg-brand-700">
                            Perbarui Pengguna
                        </button>
                        <a href="{{ route('users.index') }}" class="px-6 py-2.5 rounded-full font-semibold text-sm border">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        @endif

        {{-- ==== FILTER ==== --}}
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" id="filterForm" class="grid md:grid-cols-4 gap-3 items-end">
        <div>
            <label class="text-xs text-gray-500">Cari Pengguna</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Nama, email..."
                   oninput="debouncedSubmit()"
                   class="w-full border rounded-lg px-3 py-2 mt-1 text-sm">
        </div>
        <div>
            <label class="text-xs text-gray-500">Status</label>
            <select name="status" onchange="document.getElementById('filterForm').submit()"
                    class="w-full border rounded-lg px-3 py-2 mt-1 text-sm">
                <option value="">Semua Status</option>
                <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                <option value="tidak_aktif" @selected(request('status') === 'tidak_aktif')>Tidak Aktif</option>
                <option value="ditangguhkan" @selected(request('status') === 'ditangguhkan')>Ditangguhkan</option>
            </select>
        </div>
        <div>
            <label class="text-xs text-gray-500">Role</label>
            <select name="role" onchange="document.getElementById('filterForm').submit()"
                    class="w-full border rounded-lg px-3 py-2 mt-1 text-sm">
                <option value="">Semua Role</option>
                <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                <option value="customer" @selected(request('role') === 'customer')>Customer</option>
            </select>
        </div>
        <div>
            <a href="{{ route('users.index') }}" class="inline-block px-4 py-2 rounded-lg text-sm font-semibold border">Reset Filter</a>
        </div>
    </form>
</div>

<script>
    let debounceTimer;
    function debouncedSubmit() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            document.getElementById('filterForm').submit();
        }, 500); // tunggu 500ms setelah user berhenti ngetik
    }
</script>

        {{-- ==== TABEL ==== --}}
        <div class="bg-white rounded-xl shadow-sm overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="px-4 py-3">Pengguna</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Tipe Customer</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Bergabung</th>
                        <th class="px-4 py-3">Terakhir Login</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($users as $user)
                        @php
                            $words = explode(' ', trim($user->name));
                            $initials = strtoupper(substr($words[0] ?? '', 0, 1) . substr($words[1] ?? '', 0, 1));
                            $statusColor = match ($user->status) {
                                'aktif' => 'bg-green-100 text-green-700',
                                'tidak_aktif' => 'bg-gray-100 text-gray-600',
                                'ditangguhkan' => 'bg-red-100 text-red-600',
                                default => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <tr>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-semibold text-xs">
                                        {{ $initials ?: '?' }}
                                    </div>
                                    <div>
                                        <p class="font-medium">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-400">ID: {{ $user->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                {{ $user->email }}
                                <div class="text-xs {{ $user->email_verified_at ? 'text-green-600' : 'text-amber-600' }}">
                                    {{ $user->email_verified_at ? 'Terverifikasi' : 'Belum Terverifikasi' }}
                                </div>
                            </td>
                            <td class="px-4 py-3 capitalize">{{ $user->role }}</td>
                            <td class="px-4 py-3 capitalize">{{ $user->customer_type ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                                    {{ ucwords(str_replace('_', ' ', $user->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-gray-500">
                                {{ $user->last_login_at ? $user->last_login_at->format('d M Y H:i') : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('users.index', ['edit' => $user->id]) }}"
                                       class="text-brand-700 font-semibold text-xs">Edit</a>

                                    <form method="POST" action="{{ route('users.status', $user) }}"
                                          onchange="this.submit()">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="border rounded text-xs px-1 py-1">
                                            <option value="aktif" @selected($user->status === 'aktif')>Aktif</option>
                                            <option value="tidak_aktif" @selected($user->status === 'tidak_aktif')>Tidak Aktif</option>
                                            <option value="ditangguhkan" @selected($user->status === 'ditangguhkan')>Ditangguhkan</option>
                                        </select>
                                    </form>

                                    <form method="POST" action="{{ route('users.destroy', $user) }}"
                                          onsubmit="return confirm('Yakin hapus pengguna {{ $user->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-500 font-semibold text-xs">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-400">Tidak ada pengguna ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </div>
</body>
</html>