@extends('layouts.sidebar')

@section('title', 'Kelola Pengguna — Zakira Admin')

@php
    $inputClass = 'w-full border rounded-lg appearance-none text-base sm:text-sm py-2 h-10 leading-[1.375rem] px-3 bg-white dark:bg-white/10 text-zinc-700 placeholder-zinc-400 dark:text-zinc-300 dark:placeholder-zinc-400 shadow-xs border-zinc-200 border-b-zinc-300/80 dark:border-white/10 focus:outline-none';
    $selectClass = 'w-full appearance-none ps-3 pe-10 block h-10 py-2 text-base sm:text-sm leading-[1.375rem] rounded-lg shadow-xs border bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 border-zinc-200 border-b-zinc-300/80 dark:border-white/10 focus:outline-none';
    $primaryBtn = 'relative items-center font-medium justify-center gap-2 whitespace-nowrap h-10 text-sm rounded-lg px-4 inline-flex bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] text-[var(--color-accent-foreground)] border border-black/10 dark:border-0 shadow-xs transition';
    $ghostBtn = 'items-center font-medium justify-center whitespace-nowrap h-10 text-sm rounded-lg px-4 inline-flex border border-zinc-200 text-zinc-800 dark:text-white hover:bg-zinc-800/5 transition';
    $labelClass = 'text-xs text-gray-500 dark:text-zinc-400';
@endphp

@section('content')
<div class="space-y-6">

    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="font-medium text-zinc-800 dark:text-white text-2xl mb-1">
                Kelola Pengguna
            </div>
            <p class="text-sm text-zinc-500">Total: {{ $users->total() }} pengguna</p>
        </div>

        @if (! $showCreateForm && ! $editingUser)
            <a href="{{ route('users.index', ['form' => 'create']) }}" class="{{ $primaryBtn }} ps-3">
                <svg class="shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                    <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z"/>
                </svg>
                <span>Tambah Pengguna</span>
            </a>
        @endif
    </div>

    @if (session('success'))
        <div class="bg-green-50 text-green-700 text-sm rounded-lg px-4 py-3 border border-green-100">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 border border-red-100">{{ session('error') }}</div>
    @endif

    {{-- ==== FORM TAMBAH PENGGUNA ==== --}}
    @if ($showCreateForm)
        <div class="bg-white shadow overflow-hidden sm:rounded-md border border-zinc-200 p-6">
            <h2 class="font-medium text-zinc-800 text-lg mb-4">Buat Pengguna</h2>

            @if ($errors->any())
                <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('users.store') }}" class="grid md:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="text-sm font-medium text-zinc-700">Nama</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required
                           class="{{ $inputClass }} mt-1">
                </div>
                <div>
                    <label class="text-sm font-medium text-zinc-700">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email" required
                           class="{{ $inputClass }} mt-1">
                </div>
                <div>
                    <label class="text-sm font-medium text-zinc-700">Password</label>
                    <input type="password" name="password" placeholder="Masukkan password" required
                           class="{{ $inputClass }} mt-1">
                </div>
                <div>
                    <label class="text-sm font-medium text-zinc-700">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" placeholder="Konfirmasi password" required
                           class="{{ $inputClass }} mt-1">
                </div>
                <div>
                    <label class="text-sm font-medium text-zinc-700">Role</label>
                    <select name="role" required class="{{ $selectClass }} mt-1">
                        <option value="">Pilih Role</option>
                        <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                        <option value="customer" @selected(old('role') === 'customer')>Customer</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-zinc-700">Status</label>
                    <select name="status" class="{{ $selectClass }} mt-1">
                        <option value="aktif" selected>Aktif</option>
                        <option value="tidak_aktif">Tidak Aktif</option>
                        <option value="ditangguhkan">Ditangguhkan</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-zinc-700">Tipe Customer Zakira</label>
                    <select name="customer_type" class="{{ $selectClass }} mt-1">
                        <option value="umum" selected>Umum / Non Member</option>
                        <option value="member">Member</option>
                        <option value="distributor">Distributor</option>
                    </select>
                </div>
                <div class="flex items-center">
                    <label class="flex items-center gap-2 text-sm mt-6 text-zinc-700">
                        <input type="checkbox" name="email_verified" value="1" class="rounded border-gray-300">
                        Email Terverifikasi
                    </label>
                </div>

                <div class="md:col-span-2 flex gap-3 mt-2">
                    <button type="submit" class="{{ $primaryBtn }}">Buat Pengguna</button>
                    <a href="{{ route('users.index') }}" class="{{ $ghostBtn }}">Batal</a>
                </div>
            </form>
        </div>
    @endif

    {{-- ==== FORM EDIT PENGGUNA ==== --}}
    @if ($editingUser)
        <div class="bg-white shadow overflow-hidden sm:rounded-md border border-zinc-200 p-6">
            <h2 class="font-medium text-zinc-800 text-lg mb-4">Edit Pengguna</h2>

            @if ($errors->any())
                <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('users.update', $editingUser) }}" class="grid md:grid-cols-2 gap-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-sm font-medium text-zinc-700">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $editingUser->name) }}" required
                           class="{{ $inputClass }} mt-1">
                </div>
                <div>
                    <label class="text-sm font-medium text-zinc-700">Email</label>
                    <input type="email" name="email" value="{{ old('email', $editingUser->email) }}" required
                           class="{{ $inputClass }} mt-1">
                </div>
                <div>
                    <label class="text-sm font-medium text-zinc-700">Role</label>
                    <select name="role" required class="{{ $selectClass }} mt-1">
                        <option value="admin" @selected($editingUser->role === 'admin')>Admin</option>
                        <option value="customer" @selected($editingUser->role === 'customer')>Customer</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-zinc-700">Status</label>
                    <select name="status" class="{{ $selectClass }} mt-1">
                        <option value="aktif" @selected($editingUser->status === 'aktif')>Aktif</option>
                        <option value="tidak_aktif" @selected($editingUser->status === 'tidak_aktif')>Tidak Aktif</option>
                        <option value="ditangguhkan" @selected($editingUser->status === 'ditangguhkan')>Ditangguhkan</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-zinc-700">Tipe Customer Zakira</label>
                    <select name="customer_type" class="{{ $selectClass }} mt-1">
                        <option value="umum" @selected($editingUser->customer_type === 'umum')>Umum / Non Member</option>
                        <option value="member" @selected($editingUser->customer_type === 'member')>Member</option>
                        <option value="distributor" @selected($editingUser->customer_type === 'distributor')>Distributor</option>
                    </select>
                </div>
                <div class="flex items-center">
                    <label class="flex items-center gap-2 text-sm mt-6 text-zinc-700">
                        <input type="checkbox" name="reset_password" value="1" class="rounded border-gray-300">
                        Reset Password (akan dikirim ke email pengguna)
                    </label>
                </div>
                <div class="flex items-center">
                    <label class="flex items-center gap-2 text-sm text-zinc-700">
                        <input type="checkbox" name="email_verified" value="1" class="rounded border-gray-300"
                               @checked($editingUser->email_verified_at)>
                        Email Terverifikasi
                    </label>
                </div>

                <div class="md:col-span-2 flex gap-3 mt-2">
                    <button type="submit" class="{{ $primaryBtn }}">Perbarui Pengguna</button>
                    <a href="{{ route('users.index') }}" class="{{ $ghostBtn }}">Batal</a>
                </div>
            </form>
        </div>
    @endif

    {{-- ==== FILTER ==== --}}
    <form method="GET" id="filterForm" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        <!-- Input Cari -->
        <div class="flex-1 max-w-md">
            <div class="w-full relative block group/input">
                <div class="pointer-events-none absolute top-0 bottom-0 border-s border-transparent flex items-center justify-center text-xs text-zinc-400/75 dark:text-white/60 ps-3 start-0">
                    <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, email..."
                       oninput="debouncedSubmit()"
                       class="w-full border rounded-lg appearance-none text-base sm:text-sm py-2 h-10 leading-[1.375rem] ps-10 pe-3 bg-white dark:bg-white/10 text-zinc-700 placeholder-zinc-400 dark:text-zinc-300 dark:placeholder-zinc-400 shadow-xs border-zinc-200 border-b-zinc-300/80 dark:border-white/10 focus:outline-none">
            </div>
        </div>

        <!-- Kumpulan Filter -->
        <div class="flex items-center gap-3 flex-wrap">
            <select name="status" onchange="document.getElementById('filterForm').submit()"
                    class="appearance-none ps-3 pe-10 block h-10 py-2 text-base sm:text-sm leading-[1.375rem] rounded-lg shadow-xs border bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 border-zinc-200 border-b-zinc-300/80 dark:border-white/10 focus:outline-none">
                <option value="">Semua Status</option>
                <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                <option value="tidak_aktif" @selected(request('status') === 'tidak_aktif')>Tidak Aktif</option>
                <option value="ditangguhkan" @selected(request('status') === 'ditangguhkan')>Ditangguhkan</option>
            </select>

            <select name="role" onchange="document.getElementById('filterForm').submit()"
                    class="appearance-none ps-3 pe-10 block h-10 py-2 text-base sm:text-sm leading-[1.375rem] rounded-lg shadow-xs border bg-white dark:bg-white/10 text-zinc-700 dark:text-zinc-300 border-zinc-200 border-b-zinc-300/80 dark:border-white/10 focus:outline-none">
                <option value="">Semua Role</option>
                <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                <option value="customer" @selected(request('role') === 'customer')>Customer</option>
            </select>

            <a href="{{ route('users.index') }}" class="{{ $ghostBtn }}">Reset Filter</a>
        </div>
    </form>

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
    <div class="bg-white shadow overflow-hidden sm:rounded-md border border-zinc-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                <thead class="bg-gray-50 text-gray-500 uppercase tracking-wider font-bold text-[10px]">
                    <tr>
                        <th class="px-6 py-3">Pengguna</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3">Tipe Customer</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Bergabung</th>
                        <th class="px-6 py-3">Terakhir Login</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-zinc-700">
                    @forelse ($users as $user)
                        @php
                            $words = explode(' ', trim($user->name));
                            $initials = strtoupper(substr($words[0] ?? '', 0, 1) . substr($words[1] ?? '', 0, 1));
                            $statusColor = match ($user->status) {
                                'aktif' => 'bg-green-100 text-green-800',
                                'tidak_aktif' => 'bg-gray-100 text-gray-700',
                                'ditangguhkan' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-700',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gray-100 text-zinc-700 flex items-center justify-center font-semibold text-xs">
                                        {{ $initials ?: '?' }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                        <p class="text-[11px] text-gray-400">ID: {{ $user->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>{{ $user->email }}</div>
                                <div class="text-[11px] {{ $user->email_verified_at ? 'text-green-600' : 'text-amber-600' }}">
                                    {{ $user->email_verified_at ? 'Terverifikasi' : 'Belum Terverifikasi' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap capitalize">{{ $user->role }}</td>
                            <td class="px-6 py-4 whitespace-nowrap capitalize">{{ $user->customer_type ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                                    {{ ucwords(str_replace('_', ' ', $user->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                {{ $user->last_login_at ? $user->last_login_at->format('d M Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2 text-xs">
                                    <a href="{{ route('users.index', ['edit' => $user->id]) }}"
                                       class="h-8 px-3 inline-flex items-center rounded-md hover:bg-zinc-800/5 text-zinc-800 transition">Edit</a>

                                    <form method="POST" action="{{ route('users.status', $user) }}" onchange="this.submit()">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="h-8 border border-zinc-200 rounded-md text-xs px-2 bg-white text-zinc-700 focus:outline-none">
                                            <option value="aktif" @selected($user->status === 'aktif')>Aktif</option>
                                            <option value="tidak_aktif" @selected($user->status === 'tidak_aktif')>Tidak Aktif</option>
                                            <option value="ditangguhkan" @selected($user->status === 'ditangguhkan')>Ditangguhkan</option>
                                        </select>
                                    </form>

                                    <form method="POST" action="{{ route('users.destroy', $user) }}"
                                          onsubmit="return confirm('Yakin hapus pengguna {{ $user->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="h-8 px-3 inline-flex items-center rounded-md hover:bg-zinc-800/5 text-red-600 hover:text-red-800 transition">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-400">Tidak ada pengguna ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        {{ $users->links() }}
    </div>

</div>
@endsection