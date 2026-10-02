@extends('layouts.app')

@section('title', 'Akun Saya — Zakira Moslem Hijab Identity')

@section('content')
<div class="zakira-container">
<!-- Wrapper x-data: kontrol semua modal. Modal otomatis terbuka lagi kalau validasi gagal -->
<div class="px-4 py-12 w-full"
     x-data="{
        openEdit: {{ $errors->hasAny(['name','email','phone','address','city','province','postal_code','seller_id','shipping_expedition']) ? 'true' : 'false' }},
        openPass: {{ $errors->hasAny(['current_password','password']) ? 'true' : 'false' }},
        openDetail: false
     }">

    <!-- Judul Halaman -->
    <div class="mb-10">
        <h1 class="text-4xl font-semibold text-gray-900">Akun Saya</h1>
        <p class="text-gray-600 text-lg mt-3">Kelola profil dan riwayat pesanan Anda</p>
    </div>

    <!-- Flash message sukses (error validasi tampil di bawah masing-masing input) -->
    @if (session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm p-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Layout Grid Utama (Kiri: Profil, Kanan: Riwayat Pesanan) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

        <!-- KOLOM KIRI: PROFIL SAYA & STATISTIK -->
        <div class="lg:col-span-1 space-y-7">

            <!-- Card Informasi Profil -->
            <div class="bg-white p-7 rounded-2xl border border-gray-100 shadow-sm relative z-20">
                <div class="flex justify-between items-center mb-7">
                    <h3 class="text-[1.35rem] font-semibold text-gray-900">Profil Saya</h3>

                    <div class="flex items-center gap-8 text-base relative z-30">
                        <button type="button" @click="openEdit = true" class="text-gray-800 cursor-pointer hover:underline bg-transparent border-none p-0 focus:outline-none">
                            Profil
                        </button>
                        <button type="button" @click="openPass = true" class="text-gray-800 cursor-pointer hover:underline bg-transparent border-none p-0 focus:outline-none">
                            Password
                        </button>
                    </div>
                </div>

                <!-- Avatar Inisial -->
                <div class="flex flex-col items-center text-center pb-7 border-b border-gray-200">
                    <div class="w-24 h-24 bg-[#d1d5dc] text-[#364153] rounded-full flex items-center justify-center font-bold text-3xl mb-5">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <h4 class="font-medium text-gray-900 text-xl">{{ $user->name }}</h4>
                    <p class="text-gray-600 text-lg mt-1">{{ $user->email }}</p>
                </div>

                <!-- Detail Data Diri -->
                <div class="pt-6 pb-2 space-y-5">
                    <div>
                        <span class="text-gray-600 block text-base">Nama:</span>
                        <span class="font-medium text-gray-900 text-lg">{{ $user->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 block text-base">Email:</span>
                        <span class="font-medium text-gray-900 text-lg">{{ $user->email }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 block text-base">Telepon:</span>
                        <span class="font-medium text-gray-900 text-lg">{{ $user->detail?->phone ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 block text-base">Alamat:</span>
                        <span class="font-medium text-gray-900 text-lg">{{ $user->detail?->address ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 block text-base">Kota:</span>
                        <span class="font-medium text-gray-900 text-lg">{{ $user->detail?->city ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 block text-base">Provinsi:</span>
                        <span class="font-medium text-gray-900 text-lg">{{ $user->detail?->province ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 block text-base">Kode Pos:</span>
                        <span class="font-medium text-gray-900 text-lg">{{ $user->detail?->postal_code ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 block text-base">ID Seller:</span>
                        <span class="font-medium text-gray-900 text-lg">{{ $user->detail?->seller_id ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 block text-base">Ekspedisi Favorit:</span>
                        <span class="font-medium text-gray-900 text-lg">{{ $user->detail?->shipping_expedition ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 block text-base">Bergabung:</span>
                        <span class="font-medium text-gray-900 text-lg">{{ $user->created_at?->format('d M Y') ?? '-' }}</span>
                    </div>
                </div>

                <!-- Tombol Logout -->
                <div class="pt-6 border-t border-gray-200 mt-4">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-[#fb2c36] hover:bg-[#e7000b] text-white text-base font-medium py-3 rounded-lg transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>

            <!-- Card Statistik Pesanan (masih contoh statis) -->
            <div class="bg-white p-7 rounded-2xl border border-gray-100 shadow-sm text-base">
                <h3 class="text-[1.35rem] font-semibold text-gray-900 mb-4">Statistik Pesanan</h3>
                <div class="flex justify-between py-3">
                    <span class="text-gray-600">Total Pesanan:</span>
                    <span class="text-gray-900 font-medium">1</span>
                </div>
                <div class="flex justify-between py-3">
                    <span class="text-gray-600">Pesanan Aktif:</span>
                    <span class="text-gray-900 font-medium">1</span>
                </div>
                <div class="flex justify-between py-3">
                    <span class="text-gray-600">Pesanan Selesai:</span>
                    <span class="text-gray-900 font-medium">0</span>
                </div>
                <div class="flex justify-between items-center pt-4 mt-2 border-t border-gray-200">
                    <span class="text-gray-600">Total Belanja:</span>
                    <span class="font-medium text-[#00a63e] text-lg">Rp 500.000</span>
                </div>
            </div>

        </div>

        <!-- KOLOM KANAN: RIWAYAT PESANAN (masih contoh statis) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <h3 class="text-[1.35rem] font-semibold text-gray-900 px-7 py-5 border-b border-gray-200">Riwayat Pesanan</h3>

                <div class="bg-gray-50 px-7 py-6 space-y-5">

                    <!-- Header Pesanan -->
                    <div class="flex justify-between items-center gap-3">
                        <span class="text-xl font-medium text-gray-900">Order #ORD260922170018I42</span>
                        <span class="bg-[#fef9c2] text-[#894b00] text-base font-medium px-4 py-1.5 rounded-full">Menunggu</span>
                    </div>

                    <!-- Baris 1: Tanggal, Items, Total, Pembayaran -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-4 gap-y-5 text-base">
                        <div>
                            <span class="text-gray-600 block">Tanggal:</span>
                            <span class="text-gray-500">22 Sep 2026</span>
                        </div>
                        <div>
                            <span class="text-gray-600 block">Items:</span>
                            <span class="text-gray-500">2 item(s)</span>
                        </div>
                        <div>
                            <span class="text-gray-600 block">Total:</span>
                            <span class="font-semibold text-gray-900">Rp 500.000</span>
                        </div>
                        <div>
                            <span class="text-gray-600 block">Pembayaran:</span>
                            <span class="inline-block mt-1 bg-[#dcfce7] text-[#016630] text-sm font-medium px-3 py-0.5 rounded-full">Lunas</span>
                        </div>
                    </div>

                    <!-- Baris 2: Metode Bayar, DP Status, Sisa Bayar, Faktur -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-4 gap-y-5 text-base">
                        <div>
                            <span class="text-gray-600 block">Metode Bayar:</span>
                            <span class="inline-flex items-center gap-1.5 mt-1 bg-[#dbeafe] text-[#193cb8] text-sm font-medium px-3 py-1 rounded-full">
                                <i class="fas fa-credit-card text-xs"></i> Down Payment
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-600 block">DP Status:</span>
                            <span class="inline-flex items-center gap-1.5 mt-1 bg-[#dcfce7] text-[#016630] text-sm font-medium px-3 py-1 rounded-full">
                                <i class="fas fa-circle-check text-[#008236]"></i> DP Lunas
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-600 block">Sisa Bayar:</span>
                            <span class="inline-flex items-center gap-1.5 mt-1 bg-[#dcfce7] text-[#016630] text-sm font-medium px-3 py-1 rounded-full">
                                <i class="fas fa-circle-check text-[#008236]"></i> Lunas
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-600 block">Faktur:</span>
                            <a href="#" class="inline-flex items-center gap-2 mt-1 bg-[#dbeafe] text-[#1447e6] text-sm px-3 py-1.5 rounded-2xl hover:underline">
                                <i class="fas fa-file-pdf"></i> FKT-20260924-0001 (PDF)
                            </a>
                            <span class="text-sm text-gray-500 block mt-1">24 Sep 2026 10:18</span>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex flex-wrap items-center gap-3 pt-1">
                        <button type="button" @click="openDetail = true" class="bg-[#8C6239] hover:bg-[#724e2c] text-white text-base font-medium px-6 py-2.5 rounded-md transition flex items-center gap-2 cursor-pointer">
                            <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" /><path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd" /></svg>
                            Lihat Detail
                        </button>
                        <button type="button" class="bg-gray-100 hover:bg-gray-200 text-gray-800 text-base font-medium px-6 py-2.5 rounded-md transition flex items-center gap-2 cursor-pointer">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                            Download PDF
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- ================= MODAL EDIT PROFIL ================= -->
    <div x-show="openEdit" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" style="display: none;" x-transition.opacity>
        <div @click.outside="openEdit = false" class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-gray-200">

            <div class="flex justify-between items-center p-6 border-b border-gray-100 sticky top-0 bg-white z-10">
                <h3 class="font-bold text-lg text-gray-900">Edit Profil</h3>
                <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-100 transition cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 text-xs">
                <form action="{{ route('member.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Nama</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-1 text-sm {{ $errors->has('name') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-amber-800' }}">
                            @error('name') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-1 text-sm {{ $errors->has('email') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-amber-800' }}">
                            @error('email') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->detail?->phone) }}"
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-1 text-sm {{ $errors->has('phone') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-amber-800' }}">
                            @error('phone') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Alamat</label>
                            <textarea name="address" rows="3"
                                      class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-1 text-sm {{ $errors->has('address') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-amber-800' }}">{{ old('address', $user->detail?->address) }}</textarea>
                            @error('address') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">Kota / Kabupaten</label>
                                <input type="text" name="city" value="{{ old('city', $user->detail?->city) }}"
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-1 text-sm {{ $errors->has('city') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-amber-800' }}">
                                @error('city') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">Provinsi</label>
                                <input type="text" name="province" value="{{ old('province', $user->detail?->province) }}"
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-1 text-sm {{ $errors->has('province') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-amber-800' }}">
                                @error('province') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">Kode Pos</label>
                                <input type="text" name="postal_code" value="{{ old('postal_code', $user->detail?->postal_code) }}"
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-1 text-sm {{ $errors->has('postal_code') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-amber-800' }}">
                                @error('postal_code') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block font-semibold text-gray-700 mb-1">ID Seller</label>
                                <input type="text" name="seller_id" value="{{ old('seller_id', $user->detail?->seller_id) }}"
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-1 text-sm {{ $errors->has('seller_id') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-amber-800' }}">
                                @error('seller_id') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Ekspedisi Favorit</label>
                            <select name="shipping_expedition"
                                    class="w-full px-3 py-2 border rounded-lg bg-white focus:outline-none focus:ring-1 text-sm {{ $errors->has('shipping_expedition') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-amber-800' }}">
                                <option value="">- Pilih -</option>
                                @foreach (['JNE', 'J&T', 'SiCepat'] as $exp)
                                    <option value="{{ $exp }}" @selected(old('shipping_expedition', $user->detail?->shipping_expedition) === $exp)>
                                        {{ $exp }}
                                    </option>
                                @endforeach
                            </select>
                            @error('shipping_expedition') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-3 rounded-lg text-xs leading-relaxed">
                            Data ini otomatis masuk ke Checkout. Kota, provinsi dan kode pos juga dipakai untuk menghitung ongkir.
                        </div>
                    </div>

                    <div class="pt-6 mt-6 border-t border-gray-100 flex justify-end gap-3">
                        <button type="button" @click="openEdit = false" class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg font-medium transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-[#8C6239] hover:bg-[#724e2c] text-white rounded-lg font-medium transition cursor-pointer">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- ================= MODAL UBAH PASSWORD ================= -->
    <div x-show="openPass" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" style="display: none;" x-transition.opacity>
        <div @click.outside="openPass = false" class="bg-white rounded-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-gray-200">

            <div class="flex justify-between items-center p-6 border-b border-gray-100 sticky top-0 bg-white z-10">
                <h3 class="font-bold text-lg text-gray-900">Ubah Password</h3>
                <button type="button" @click="openPass = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-100 transition cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 text-xs">
                <form action="{{ route('member.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Password Saat Ini</label>
                            <input type="password" name="current_password"
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-1 text-sm {{ $errors->has('current_password') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-amber-800' }}">
                            @error('current_password') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Password Baru</label>
                            <input type="password" name="password"
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-1 text-sm {{ $errors->has('password') ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-amber-800' }}">
                            @error('password') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block font-semibold text-gray-700 mb-1">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-amber-800 text-sm">
                        </div>
                    </div>

                    <div class="pt-6 mt-6 border-t border-gray-100 flex justify-end items-center gap-3">
                        <button type="button" @click="openPass = false" class="text-sm font-medium text-gray-700 hover:underline cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 bg-[#8C6239] hover:bg-[#724e2c] text-white text-xs rounded-lg font-medium transition cursor-pointer">
                            Ubah Password
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- ================= MODAL DETAIL PESANAN ================= -->
    <div x-show="openDetail" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 flex items-center justify-center p-4" style="display: none;" x-transition.opacity>
        <div @click.outside="openDetail = false" class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-gray-200">

            <div class="flex justify-between items-center p-6 border-b border-gray-100 sticky top-0 bg-white z-10">
                <h3 class="font-bold text-lg text-gray-900">Detail Pesanan #ORD260922170018I42</h3>
                <button type="button" @click="openDetail = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-100 transition cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 space-y-6 text-xs">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                    <div class="space-y-2">
                        <h4 class="font-bold text-gray-800 text-sm mb-3">Informasi Pesanan</h4>
                        <div class="flex justify-between"><span class="text-gray-500">Order ID:</span> <span class="font-medium text-gray-800">ORD260922170018I42</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Tanggal:</span> <span class="font-medium text-gray-800">22 Sep 2026 17:00</span></div>
                        <div class="flex justify-between items-center"><span class="text-gray-500">Status:</span> <span class="bg-amber-100 text-amber-800 font-semibold px-2.5 py-0.5 rounded-full text-[10px]">Menunggu</span></div>
                        <div class="flex justify-between items-center"><span class="text-gray-500">Pembayaran:</span> <span class="bg-emerald-100 text-emerald-800 font-semibold px-2 py-0.5 rounded text-[10px]">Lunas</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Total:</span> <span class="font-bold text-emerald-700 text-sm">Rp 500.000</span></div>
                    </div>

                    <!-- Informasi pengiriman: diambil dari data user -->
                    <div class="space-y-2">
                        <h4 class="font-bold text-gray-800 text-sm mb-3">Informasi Pengiriman</h4>
                        <div class="flex justify-between"><span class="text-gray-500">Nama:</span> <span class="font-medium text-gray-800">{{ $user->name }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Email:</span> <span class="font-medium text-gray-800">{{ $user->email }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">WhatsApp:</span> <span class="font-medium text-gray-800">{{ $user->detail?->phone ?? '-' }}</span></div>
                        <div>
                            <span class="text-gray-500 block mb-1">Alamat:</span>
                            <p class="font-medium text-gray-800 leading-relaxed">
                                {{ $user->detail?->address ?? '-' }}<br>
                                {{ $user->detail?->city ?? '-' }}, {{ $user->detail?->province ?? '-' }} {{ $user->detail?->postal_code }}<br>
                                Indonesia
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <h4 class="font-bold text-gray-800 text-sm">Item Pesanan</h4>
                    <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-xl shadow-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 shrink-0">📷</div>
                            <div>
                                <h5 class="font-semibold text-gray-800">Baju Koko</h5>
                                <p class="text-[11px] text-gray-500">Model: Pro | Warna: Hitam | Ukuran: df</p>
                                <p class="text-[11px] text-gray-500">Rp 100.000 × 1</p>
                            </div>
                        </div>
                        <span class="font-bold text-gray-800 text-sm">Rp 100.000</span>
                    </div>
                </div>
            </div>

            <div class="p-4 border-t border-gray-100 flex justify-end bg-gray-50 rounded-b-2xl">
                <button type="button" @click="openDetail = false" class="bg-gray-800 hover:bg-gray-900 text-white text-xs font-semibold px-5 py-2 rounded-lg transition cursor-pointer">
                    Tutup
                </button>
            </div>

        </div>
    </div>

</div>
</div>
@endsection