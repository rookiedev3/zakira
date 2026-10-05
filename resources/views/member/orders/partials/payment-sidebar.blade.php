{{--
    Kolom kanan halaman pembayaran (dipakai oleh pay-dp & pay-remaining).

    Variabel yang dibutuhkan:
      $bankAccounts : koleksi rekening (bank_name, account_number, account_name, is_active)
      $amount       : nominal yang harus ditransfer (int)
      $noteSuffix   : teks tambahan di catatan penting, contoh ' (DP 30.0%)' atau ''
      $proofLabel   : label input file, contoh 'Bukti Transfer DP'
      $formAction   : URL tujuan POST upload
--}}
@php
    $rp = fn ($n) => 'Rp ' . number_format((int) $n, 0, ',', '.');
@endphp

<div class="space-y-7">

    <!-- Informasi Rekening -->
    <div class="bg-white p-7 rounded-2xl border border-gray-100 shadow-sm">
        <h3 class="text-[1.35rem] font-semibold text-gray-900 mb-6">Informasi Rekening</h3>

        <div class="space-y-4">
            @forelse ($bankAccounts as $bank)
                <div class="border border-gray-200 rounded-xl p-5"
                     x-data="{ copied: false }">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-lg font-medium text-gray-900">{{ $bank->bank_name }}</span>
                        @if ($bank->is_active ?? true)
                            <span class="bg-[#dcfce7] text-[#016630] text-sm font-medium px-3 py-0.5 rounded-full">Aktif</span>
                        @endif
                    </div>

                    <div class="flex justify-between items-start gap-3 text-base">
                        <div class="space-y-1">
                            <div class="flex gap-6">
                                <span class="text-gray-600">No. Rekening:</span>
                                <span class="font-mono text-gray-900 tracking-wide">{{ $bank->account_number }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Atas Nama:</span>
                                <span class="font-medium text-gray-900">{{ $bank->account_name }}</span>
                            </div>
                        </div>

                        <button type="button"
                                @click="navigator.clipboard.writeText('{{ $bank->account_number }}'); copied = true; setTimeout(() => copied = false, 1500)"
                                class="text-[#8C6239] hover:text-[#724e2c] p-1 cursor-pointer"
                                title="Salin nomor rekening">
                            <svg x-show="!copied" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor"><path d="M7 3.5A1.5 1.5 0 0 1 8.5 2h3.879a1.5 1.5 0 0 1 1.06.44l3.122 3.12A1.5 1.5 0 0 1 17 6.622V12.5a1.5 1.5 0 0 1-1.5 1.5h-1v-3.379a3 3 0 0 0-.879-2.121L10.5 5.379A3 3 0 0 0 8.379 4.5H7v-1Z" /><path d="M4.5 6A1.5 1.5 0 0 0 3 7.5v9A1.5 1.5 0 0 0 4.5 18h7a1.5 1.5 0 0 0 1.5-1.5v-5.879a1.5 1.5 0 0 0-.44-1.06L9.44 6.439A1.5 1.5 0 0 0 8.378 6H4.5Z" /></svg>
                            <svg x-show="copied" x-cloak class="w-5 h-5 text-green-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" /></svg>
                        </button>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-base">Rekening belum tersedia. Silakan hubungi admin.</p>
            @endforelse
        </div>

        <div class="mt-5 bg-[#fef9c2] border border-yellow-200 text-[#894b00] rounded-xl p-4 text-base flex gap-3">
            <svg class="w-5 h-5 mt-1 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" /></svg>
            <p>
                <strong>Penting:</strong> Transfer tepat sebesar <strong>{{ $rp($amount) }}</strong>{{ $noteSuffix ?? '' }} untuk mempercepat verifikasi.
            </p>
        </div>
    </div>

    <!-- Upload Bukti Pembayaran -->
    <div class="bg-white p-7 rounded-2xl border border-gray-100 shadow-sm">
        <h3 class="text-[1.35rem] font-semibold text-gray-900 mb-6">Upload Bukti Pembayaran</h3>

        <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" x-data="{ fileName: '' }">
            @csrf

            <label for="payment_proof" class="block text-base font-medium text-gray-900 mb-3">{{ $proofLabel }}</label>

            <div class="flex items-center gap-4">
                <label for="payment_proof"
                       class="bg-[#f6ede4] hover:bg-[#efe0d0] text-[#8C6239] text-base font-medium px-5 py-3 rounded-lg cursor-pointer transition">
                    Pilih File
                </label>
                <span class="text-gray-600 text-base truncate" x-text="fileName || 'Belum ada file dipilih'"></span>
                <input id="payment_proof" type="file" name="payment_proof" accept="image/jpeg,image/png,image/gif" class="sr-only"
                       @change="fileName = $event.target.files[0]?.name ?? ''">
            </div>

            @error('payment_proof')
                <p class="text-red-600 text-sm mt-2">{{ $message }}</p>
            @enderror

            <p class="text-gray-500 text-sm mt-4 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd" /></svg>
                Format yang didukung: JPG, PNG, GIF. Maksimal 2MB.
            </p>

            <button type="submit"
                    class="w-full mt-6 bg-[#8C6239] hover:bg-[#724e2c] text-white text-lg font-medium py-4 rounded-xl transition cursor-pointer">
                Upload Bukti Pembayaran
            </button>
        </form>
    </div>

</div>