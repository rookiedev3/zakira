@extends('settings.layout')

@section('content')
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h2 class="font-semibold text-lg">Profil</h2>
        <p class="text-gray-500 text-sm mb-4">Perbarui nama dan alamat email Anda</p>

        <form method="POST" action="{{ route('settings.profile.update') }}" class="space-y-4 max-w-md">
            @csrf
            @method('PATCH')

            <div>
                <label class="text-sm font-medium">Nama</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full border rounded-lg px-4 py-2 mt-1">
            </div>
            <div>
                <label class="text-sm font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full border rounded-lg px-4 py-2 mt-1">
            </div>

            <button class="bg-brand-600 text-white px-6 py-2.5 rounded-full font-semibold text-sm hover:bg-brand-700">
                Simpan
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 border border-red-200">
        <h2 class="font-semibold text-lg text-red-600">Delete account</h2>
        <p class="text-gray-500 text-sm mb-4">Delete your account and all of its resources</p>

        <button type="button" onclick="document.getElementById('deleteAccountModal').classList.remove('hidden')"
                class="bg-red-600 text-white px-6 py-2.5 rounded-full font-semibold text-sm hover:bg-red-700">
            Delete account
        </button>
    </div>

    {{-- Modal konfirmasi hapus akun --}}
    <div id="deleteAccountModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center px-4 z-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-sm">
            <h3 class="font-semibold text-lg mb-2">Hapus Akun?</h3>
            <p class="text-sm text-gray-500 mb-4">Tindakan ini tidak bisa dibatalkan. Masukkan password Anda untuk konfirmasi.</p>

            <form method="POST" action="{{ route('settings.profile.destroy') }}">
                @csrf
                @method('DELETE')

                <input type="password" name="password" placeholder="Password Anda" required
                       class="w-full border rounded-lg px-4 py-2 mb-4">

                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-red-600 text-white py-2.5 rounded-full font-semibold text-sm hover:bg-red-700">
                        Ya, Hapus Akun
                    </button>
                    <button type="button" onclick="document.getElementById('deleteAccountModal').classList.add('hidden')"
                            class="flex-1 border py-2.5 rounded-full font-semibold text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection