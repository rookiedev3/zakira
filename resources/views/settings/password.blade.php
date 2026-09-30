@extends('settings.layout')

@section('settings')
    <div class="bg-white rounded-xl shadow-sm border border-zinc-200 p-6">
        <h2 class="font-semibold text-lg">Update password</h2>
        <p class="text-gray-500 text-sm mb-4">Untuk menjaga akun tetap aman, gunakan password yang kuat, panjang, dan acak.</p>

        <form method="POST" action="{{ route('settings.password.update') }}" class="space-y-4 max-w-md">
            @csrf
            @method('PUT')

            <div>
                <label class="text-sm font-medium">Password saat ini</label>
                <input type="password" name="current_password" required
                       class="w-full border rounded-lg px-4 py-2 mt-1">
            </div>
            <div>
                <label class="text-sm font-medium">Password baru</label>
                <input type="password" name="password" required
                       class="w-full border rounded-lg px-4 py-2 mt-1">
            </div>
            <div>
                <label class="text-sm font-medium">Konfirmasi password</label>
                <input type="password" name="password_confirmation" required
                       class="w-full border rounded-lg px-4 py-2 mt-1">
            </div>

            <button class="bg-[#9c6b3a] text-white px-6 py-2.5 rounded-full font-semibold text-sm hover:bg-[#7d5330]">
                Simpan
            </button>
        </form>
    </div>
@endsection