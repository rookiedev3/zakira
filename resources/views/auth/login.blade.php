<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Zakira</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fdf6f0',
                            100: '#f8e9db',
                            400: '#c9995f',
                            600: '#9c6b3a',
                            700: '#7d5330',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-white text-gray-800">

    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">

            <!-- Logo & Header -->
            <div class="text-center mb-6">
    <a href="{{ route('home') }}" class="inline-block">
        <!-- Ubah h-10 menjadi h-14 atau h-16 untuk memperbesar ukuran logo -->
        <img src="{{ asset('images/logo-zakira.png') }}" alt="Zakira Logo" class="h-16 mx-auto object-contain">
    </a>
    <h2 class="text-gray-600 text-base mt-6 font-normal">Login khusus Member Zakira</h2>
</div>
            <!-- Error Messages -->
            @if (session('error'))
                <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Form Card (Tanpa border box putih tebal, menyatu dengan halaman) -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-900 mb-1.5">Email address</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="email@example.com"
                           class="w-full border border-amber-800/60 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-400 placeholder:text-gray-400 text-sm shadow-sm">
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label for="password" class="block text-sm font-medium text-gray-900">Password</label>
                        <!-- Opsional jika ada route password.request, jika tidak biarkan teks biasa -->
                        <a href="#" class="text-xs text-amber-800 hover:underline">Lupa password?</a>
                    </div>
                    <div class="relative">
                        <input id="password" type="password" name="password" required placeholder="Password"
                               class="w-full border border-gray-300 rounded-xl px-4 py-3 pr-12 focus:outline-none focus:ring-2 focus:ring-brand-400 placeholder:text-gray-400 text-sm">
                        <button type="button" onclick="togglePassword()"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <!-- Icon Mata (SVG) untuk tombol Lihat/Sembunyikan -->
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span id="togglePasswordText" class="hidden">Lihat</span>
                        </button>
                    </div>
                </div>

                <div class="flex items-center pt-1">
                    <label class="flex items-center gap-2.5 text-sm text-gray-800 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-amber-800 focus:ring-brand-400">
                        Ingat saya
                    </label>
                </div>

                <div>
                    <button type="submit"
                            class="w-full bg-[#8c6239] text-white py-3.5 rounded-xl font-medium hover:bg-[#78522e] transition shadow-sm text-sm">
                        Login Member
                    </button>
                </div>
            </form>

            <!-- Informasi Kontak / Aktivasi Member di Bawah -->
            <div class="text-center mt-6">
                <p class="text-xs text-gray-500">
                    Belum menjadi member? Silakan hubungi admin Zakira <br>untuk aktivasi akun member.
                </p>
                
            </div>

        </div>
    </div>

    <!-- Script fungsionalitas asli tetap dipertahankan -->
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const label = document.getElementById('togglePasswordText');
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            label.textContent = isPassword ? 'Sembunyikan' : 'Lihat';
        }
    </script>
</body>
</html>