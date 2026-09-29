<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - Zakira</title>
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

    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">

            <div class="text-center mb-6">
                <a href="{{ route('home') }}" class="inline-block">
                    <img src="{{ asset('images/logo-zakira.png') }}" alt="Zakira Logo" class="h-16 mx-auto object-contain">
                </a>
                <p class="text-gray-500 text-sm mt-4">Buat password baru untuk akun Anda</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-900 mb-1.5">Alamat email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required autofocus
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-400 text-sm">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-900 mb-1.5">Password baru</label>
                    <input id="password" type="password" name="password" required
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-400 text-sm">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-900 mb-1.5">Konfirmasi password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-brand-400 text-sm">
                </div>

                <button type="submit"
                        class="w-full bg-[#8c6239] text-white py-3.5 rounded-xl font-medium hover:bg-[#78522e] transition shadow-sm text-sm">
                    Reset Password
                </button>
            </form>

        </div>
    </div>

</body>
</html>