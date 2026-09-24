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
<body class="bg-brand-50 text-gray-800">

    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">

            <div class="text-center mb-6">
                <a href="{{ route('home') }}" class="inline-block text-2xl font-serif font-bold text-brand-700">Zakira</a>
                <p class="text-gray-500 text-sm mt-1">Moslem Hijab Identity</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-8">
                <h1 class="text-xl font-serif font-bold mb-1">Login Member Zakira</h1>
                <p class="text-gray-500 text-sm mb-6">
                    Login khusus member Zakira. Pembelian Ready Stock dapat dilakukan tanpa login.
                </p>

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

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="text-sm font-medium">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full border rounded-lg px-4 py-2.5 mt-1 focus:outline-none focus:ring-2 focus:ring-brand-400">
                    </div>

                    <div>
                        <label for="password" class="text-sm font-medium">Password</label>
                        <div class="relative mt-1">
                            <input id="password" type="password" name="password" required
                                   class="w-full border rounded-lg px-4 py-2.5 pr-12 focus:outline-none focus:ring-2 focus:ring-brand-400">
                            <button type="button" onclick="togglePassword()"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-medium">
                                <span id="togglePasswordText">Lihat</span>
                            </button>
                        </div>
                    </div>

                    <label class="flex items-center gap-2 text-sm text-gray-600">
                        <input type="checkbox" name="remember" class="rounded border-gray-300">
                        Ingat saya
                    </label>

                    <button type="submit"
                            class="w-full bg-brand-600 text-white py-3 rounded-full font-semibold hover:bg-brand-700 transition">
                        Masuk
                    </button>
                </form>
            </div>

            <p class="text-center text-sm text-gray-500 mt-6">
                <a href="{{ route('home') }}" class="hover:underline">&larr; Kembali ke Beranda</a>
            </p>
        </div>
    </div>

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