@extends('layouts.sidebar')

@section('title', 'Kelola Pengguna — Zakira Admin')

@php
    $inputClass = 'w-full border border-gray-300 rounded-lg appearance-none text-sm py-2 h-10 leading-[1.375rem] px-4 bg-white dark:bg-white/10 text-gray-900 placeholder-gray-400 dark:text-zinc-300 dark:placeholder-zinc-400 dark:border-white/10 focus:outline-none focus:border-[#8B5E3C] focus:ring-1 focus:ring-[#8B5E3C]';
    $selectClass = 'w-full appearance-none ps-4 pe-10 block h-10 py-2 text-sm leading-[1.375rem] rounded-lg border border-gray-300 bg-white dark:bg-white/10 text-gray-900 dark:text-zinc-300 dark:border-white/10 focus:outline-none focus:border-[#8B5E3C] focus:ring-1 focus:ring-[#8B5E3C]';
    $primaryBtn = 'relative items-center font-medium justify-center gap-2 whitespace-nowrap h-10 text-sm rounded-lg px-5 inline-flex bg-[#8B5E3C] hover:bg-[#7a5134] text-white shadow-sm transition-colors';
    $ghostBtn = 'items-center font-medium justify-center whitespace-nowrap h-10 text-sm rounded-lg px-5 inline-flex bg-gray-100 text-gray-700 hover:bg-gray-200 dark:text-white transition-colors';
    $labelClass = 'text-xs text-gray-500 dark:text-zinc-400';
@endphp

@section('content')
<div class="space-y-6">

    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white mb-1">
                Kelola Pengguna
            </div>
            <p class="text-sm text-gray-500">Total: {{ $users->total() }} pengguna</p>
        </div>

        {{-- Tombol tetap muncul saat form edit terbuka, hanya hilang saat form tambah aktif --}}
        @if (! $showCreateForm)
            <a href="{{ route('users.index', ['form' => 'create']) }}" class="{{ $primaryBtn }} ps-4">
                <svg class="shrink-0 w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                    <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z"/>
                </svg>
                <span>Tambah Pengguna</span>
            </a>
        @endif
    </div>

    @if (session('success'))
        <div class="bg-green-50 text-green-700 text-sm rounded-lg px-4 py-3 border border-green-200">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 border border-red-200">{{ session('error') }}</div>
    @endif

    {{-- ==== FORM TAMBAH PENGGUNA ==== --}}
    @if ($showCreateForm)
        <div class="bg-white shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden rounded-xl p-6">
            <h2 class="font-semibold text-gray-900 text-lg mb-4">Buat Pengguna</h2>

            @if ($errors->any())
                <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('users.store') }}" class="grid md:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required
                           class="{{ $inputClass }} mt-1">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email" required
                           class="{{ $inputClass }} mt-1">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" placeholder="Masukkan password" required
                           class="{{ $inputClass }} mt-1">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" placeholder="Konfirmasi password" required
                           class="{{ $inputClass }} mt-1">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Role</label>
                    <select name="role" required class="{{ $selectClass }} mt-1">
                        <option value="">Pilih Role</option>
                        <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                        <option value="customer" @selected(old('role') === 'customer')>Customer</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Status</label>
                    <select name="status" class="{{ $selectClass }} mt-1">
                        <option value="aktif" selected>Aktif</option>
                        <option value="tidak_aktif">Tidak Aktif</option>
                        <option value="ditangguhkan">Ditangguhkan</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Tipe Customer Zakira</label>
                    <select name="customer_type" class="{{ $selectClass }} mt-1">
                        <option value="umum" selected>Umum / Non Member</option>
                        <option value="member">Member</option>
                        <option value="distributor">Distributor</option>
                    </select>
                </div>
                <div class="flex items-center">
                    <label class="flex items-center gap-2 text-sm mt-6 text-gray-700">
                        <input type="checkbox" name="email_verified" value="1" class="rounded border-gray-300 text-[#8B5E3C]">
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
        <div class="bg-white shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden rounded-xl p-6">
            <h2 class="font-semibold text-gray-900 text-lg mb-4">Edit Pengguna</h2>

            @if ($errors->any())
                <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('users.update', $editingUser) }}" class="grid md:grid-cols-2 gap-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-sm font-medium text-gray-700">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $editingUser->name) }}" required
                           class="{{ $inputClass }} mt-1">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" value="{{ old('email', $editingUser->email) }}" required
                           class="{{ $inputClass }} mt-1">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Role</label>
                    <select name="role" required class="{{ $selectClass }} mt-1">
                        <option value="admin" @selected($editingUser->role === 'admin')>Admin</option>
                        <option value="customer" @selected($editingUser->role === 'customer')>Customer</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Status</label>
                    <select name="status" class="{{ $selectClass }} mt-1">
                        <option value="aktif" @selected($editingUser->status === 'aktif')>Aktif</option>
                        <option value="tidak_aktif" @selected($editingUser->status === 'tidak_aktif')>Tidak Aktif</option>
                        <option value="ditangguhkan" @selected($editingUser->status === 'ditangguhkan')>Ditangguhkan</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">Tipe Customer Zakira</label>
                    <select name="customer_type" class="{{ $selectClass }} mt-1">
                        <option value="umum" @selected($editingUser->customer_type === 'umum')>Umum / Non Member</option>
                        <option value="member" @selected($editingUser->customer_type === 'member')>Member</option>
                        <option value="distributor" @selected($editingUser->customer_type === 'distributor')>Distributor</option>
                    </select>
                </div>
                <div class="flex items-center">
                    <label class="flex items-center gap-2 text-sm mt-6 text-gray-700">
                        <input type="checkbox" name="reset_password" value="1" class="rounded border-gray-300 text-[#8B5E3C]">
                        Reset Password (akan dikirim ke email pengguna)
                    </label>
                </div>
                <div class="flex items-center">
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="email_verified" value="1" class="rounded border-gray-300 text-[#8B5E3C]"
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
    <form method="GET" id="filterForm" class="bg-white rounded-xl shadow-[0_1px_4px_rgba(0,0,0,0.08)] p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 items-end">

        <!-- Input Cari -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Cari Pengguna</label>
            <div class="w-full relative block group/input">
                <div class="pointer-events-none absolute top-0 bottom-0 border-s border-transparent flex items-center justify-center text-xs text-gray-400 dark:text-white/60 ps-3 start-0">
                    <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, email..."
                       oninput="debouncedSubmit()"
                       class="w-full border border-gray-300 rounded-lg appearance-none text-sm py-2 h-10 leading-[1.375rem] ps-10 pe-4 bg-white dark:bg-white/10 text-gray-900 placeholder-gray-400 dark:text-zinc-300 dark:placeholder-zinc-400 dark:border-white/10 focus:outline-none focus:border-[#8B5E3C] focus:ring-1 focus:ring-[#8B5E3C]">
            </div>
        </div>

        <!-- Filter Status -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Status</label>
            <select name="status" onchange="document.getElementById('filterForm').submit()"
                    class="w-full block h-10 py-2 px-4 text-sm leading-[1.375rem] rounded-lg border border-gray-300 bg-white dark:bg-white/10 text-gray-900 dark:text-zinc-300 dark:border-white/10 focus:outline-none focus:border-[#8B5E3C] focus:ring-1 focus:ring-[#8B5E3C]">
                <option value="">Semua Status</option>
                <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                <option value="tidak_aktif" @selected(request('status') === 'tidak_aktif')>Tidak Aktif</option>
                <option value="ditangguhkan" @selected(request('status') === 'ditangguhkan')>Ditangguhkan</option>
            </select>
        </div>

        <!-- Filter Role -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Role</label>
            <select name="role" onchange="document.getElementById('filterForm').submit()"
                    class="w-full block h-10 py-2 px-4 text-sm leading-[1.375rem] rounded-lg border border-gray-300 bg-white dark:bg-white/10 text-gray-900 dark:text-zinc-300 dark:border-white/10 focus:outline-none focus:border-[#8B5E3C] focus:ring-1 focus:ring-[#8B5E3C]">
                <option value="">Semua Role</option>
                <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                <option value="customer" @selected(request('role') === 'customer')>Customer</option>
            </select>
        </div>

        <!-- Reset -->
        <div>
            <a href="{{ route('users.index') }}" class="{{ $ghostBtn }} w-full">Reset Filter</a>
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
    <div class="bg-white shadow-[0_1px_4px_rgba(0,0,0,0.08)] overflow-hidden rounded-xl">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                <thead class="bg-slate-50 text-gray-500 uppercase tracking-wide font-medium text-xs">
                    <tr>
                        <th class="px-6 py-3 font-medium">Pengguna</th>
                        <th class="px-6 py-3 font-medium">Email</th>
                        <th class="px-6 py-3 font-medium">Role</th>
                        <th class="px-6 py-3 font-medium">Tipe Customer</th>
                        <th class="px-6 py-3 font-medium">Status</th>
                        <th class="px-6 py-3 font-medium">Bergabung</th>
                        <th class="px-6 py-3 font-medium">Terakhir Login</th>
                        <th class="px-6 py-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 text-gray-900">
                    @forelse ($users as $user)
                        @php
                            $words = explode(' ', trim($user->name));
                            $initials = strtoupper(substr($words[0] ?? '', 0, 1) . substr($words[1] ?? '', 0, 1));
                            $statusColor = match ($user->status) {
                                'aktif' => 'bg-green-100 text-green-700',
                                'tidak_aktif' => 'bg-gray-100 text-gray-600',
                                'ditangguhkan' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-600',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-medium text-sm">
                                        {{ $initials ?: '?' }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500">ID: {{ $user->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                <div class="text-xs {{ $user->email_verified_at ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $user->email_verified_at ? 'Terverifikasi' : 'Belum Terverifikasi' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium capitalize bg-blue-100 text-blue-700">{{ $user->role }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-medium capitalize bg-[#f7f0ea] text-[#6f4d3b]">{{ $user->customer_type ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full {{ $statusColor }}">
                                    {{ ucwords(str_replace('_', ' ', $user->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->last_login_at ? $user->last_login_at->format('d M Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <div class="flex items-center justify-end space-x-2 text-sm">
                                    <a href="{{ route('users.index', ['edit' => $user->id]) }}"
                                       class="h-8 px-3 inline-flex items-center rounded-md hover:bg-zinc-800/5 text-gray-900 transition-colors">Edit</a>

                                    <form method="POST" action="{{ route('users.status', $user) }}" onchange="this.submit()">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="h-8 border-0 rounded-md text-sm px-2 bg-transparent text-gray-900 cursor-pointer hover:bg-zinc-800/5 focus:outline-none">
                                            <option value="aktif" @selected($user->status === 'aktif')>Aktif</option>
                                            <option value="tidak_aktif" @selected($user->status === 'tidak_aktif')>Tidak Aktif</option>
                                            <option value="ditangguhkan" @selected($user->status === 'ditangguhkan')>Ditangguhkan</option>
                                        </select>
                                    </form>

                                    <form method="POST" action="{{ route('users.destroy', $user) }}"
                                          onsubmit="return confirm('Yakin hapus pengguna {{ $user->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="h-8 px-3 inline-flex items-center rounded-md hover:bg-zinc-800/5 text-gray-900 transition-colors">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-sm text-gray-500">Tidak ada pengguna ditemukan.</td>
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