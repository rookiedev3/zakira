{{--
resources/views/settings/layout.blade.php

Layout Pengaturan yang DITEMPELKAN ke layouts.sidebar
(sidebar admin, header mobile, dan dropdown profil tetap ada).

Cara pakai di halaman pengaturan (profile.blade.php, password.blade.php, dst):

@extends('layouts.settings')

@section('settings')
    ... isi form ...
@endsection
--}}
@extends('layouts.sidebar')

@section('title', 'Pengaturan - Zakira')

@section('content')
    <div class="w-full max-w-5xl">
        <h1 class="text-2xl font-serif font-bold mb-1">Pengaturan</h1>
        <p class="text-zinc-500 text-sm mb-6">Kelola profil dan akun anda</p>

        <hr class="mb-6 border-zinc-200">

        @if (session('success'))
            <div class="bg-green-50 text-green-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
        @endif

        <div class="grid md:grid-cols-[200px_1fr] gap-6 md:gap-8">

            {{-- ==== MENU PENGATURAN ==== --}}
            <div class="flex md:block gap-1 md:space-y-1 overflow-x-auto">
                <a href="{{ route('settings.profile') }}"
                    class="block shrink-0 px-4 py-2.5 rounded-lg text-sm font-medium
                        {{ request()->routeIs('settings.profile') ? 'bg-[#f8e9db] text-[#7d5330]' : 'text-zinc-500 hover:bg-zinc-50' }}">
                    Profile
                </a>
                <a href="{{ route('settings.password') }}"
                    class="block shrink-0 px-4 py-2.5 rounded-lg text-sm font-medium
                        {{ request()->routeIs('settings.password') ? 'bg-[#f8e9db] text-[#7d5330]' : 'text-zinc-500 hover:bg-zinc-50' }}">
                    Password
                </a>
            </div>

            {{-- ==== KONTEN PENGATURAN ==== --}}
            <div class="min-w-0">
                @yield('settings')
            </div>
        </div>
    </div>
@endsection