<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Halaman Tidak Ditemukan - Zakira</title>
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
        <div class="w-full max-w-md text-center">

            <!-- Kode error -->
            <p class="text-7xl font-semibold text-brand-600 tracking-tight">404</p>

            <h1 class="text-xl font-semibold text-gray-900 mt-4">Halaman Tidak Ditemukan</h1>
            <p class="text-gray-600 text-sm leading-relaxed mt-3">
                Maaf, halaman yang Anda cari tidak dapat ditemukan. Halaman mungkin telah dipindahkan, dihapus, atau tidak pernah ada.
            </p>

            <!-- Tombol -->
            <div class="mt-8 space-y-3">
                <button type="button" onclick="goBack()"
                        class="w-full bg-[#8c6239] text-white py-3.5 rounded-xl font-medium hover:bg-[#78522e] transition shadow-sm text-sm cursor-pointer">
                    Kembali ke Halaman Sebelumnya
                </button>

                <a href="{{ route('home') }}"
                   class="block w-full border border-amber-800/60 text-amber-800 py-3.5 rounded-xl font-medium hover:bg-brand-50 transition text-sm">
                    Ke Beranda
                </a>
            </div>

        </div>
    </div>

    <script>
        // Kembali ke halaman sebelumnya; kalau tidak ada riwayat, ke beranda
        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = "{{ route('home') }}";
            }
        }
    </script>
</body>
</html>