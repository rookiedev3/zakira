<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengaturan - Zakira</title>
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
<body class="bg-white text-gray-800">

    <div class="max-w-5xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-serif font-bold mb-1">Pengaturan</h1>
        <p class="text-gray-500 text-sm mb-6">Kelola profil dan akun anda</p>

        <hr class="mb-6">

        @if (session('success'))
            <div class="bg-green-50 text-green-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
        @endif

        <div class="grid md:grid-cols-[200px_1fr] gap-8">
            {{-- ==== SIDEBAR ==== --}}
            <div class="space-y-1">
                <a href="{{ route('settings.profile') }}"
                   class="block px-4 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('settings.profile') ? 'bg-brand-100 text-brand-700' : 'text-gray-500 hover:bg-gray-50' }}">
                    Profile
                </a>
                <a href="{{ route('settings.password') }}"
                   class="block px-4 py-2.5 rounded-lg text-sm font-medium {{ request()->routeIs('settings.password') ? 'bg-brand-100 text-brand-700' : 'text-gray-500 hover:bg-gray-50' }}">
                    Password
                </a>
            </div>

            {{-- ==== KONTEN ==== --}}
            <div>
                @yield('content')
            </div>
        </div>
    </div>

</body>
</html>