@extends('layouts.sidebar')

@section('title', 'Informasi Bank')

@section('content')
@php
    $inputClass = 'w-full border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500';
    $labelClass = 'block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2';
    $thClass = 'px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide';
    $cardClass = 'bg-white dark:bg-zinc-900 shadow-[0_1px_4px_rgba(0,0,0,0.08)] rounded-xl dark:border dark:border-zinc-700';
    $primaryBtn = 'inline-flex items-center justify-center h-10 px-5 text-sm font-medium rounded-lg bg-[#8B5E3C] hover:bg-[#7a5134] text-white shadow-sm transition-colors';
    $secondaryBtn = 'inline-flex items-center justify-center h-10 px-6 text-sm font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-zinc-700 dark:text-white dark:hover:bg-zinc-800 transition-colors';
    $ghostBtnClass = 'relative items-center font-normal justify-center h-8 text-sm rounded-md px-3 inline-flex bg-transparent text-gray-900 dark:text-white hover:bg-zinc-800/5 dark:hover:bg-white/15 transition-colors';
    $errorBoxClass = 'rounded-lg bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 mb-4 dark:bg-red-900/20 dark:border-red-800 dark:text-red-300';

    // Nomor rekening: hanya angka (6-20 digit). oninput membuang karakter selain angka.
    $accountNumberAttrs = 'inputmode="numeric" maxlength="20" pattern="[0-9]{6,20}" title="Nomor rekening harus berupa angka saja (6-20 digit)" oninput="this.value = this.value.replace(/\D/g, \'\')"';

    // ---- MODE DEMO: aktif kalau controller tidak mengirim $bankAccounts ----
    $demo = ! isset($bankAccounts);
    $demoAlert = "alert('Mode demo: belum terhubung ke backend.'); return false;";

    if ($demo) {
        $bankAccounts = collect([
            (object) ['id' => 1, 'bank_name' => 'BCA',     'account_number' => '1234567890', 'account_holder_name' => 'PT Zakira Indonesia', 'status' => 'aktif'],
            (object) ['id' => 2, 'bank_name' => 'Mandiri', 'account_number' => '9876543210', 'account_holder_name' => 'PT Zakira Indonesia', 'status' => 'aktif'],
            (object) ['id' => 3, 'bank_name' => 'BRI',     'account_number' => '5566778899', 'account_holder_name' => 'Zakira Store',        'status' => 'nonaktif'],
        ]);
        $showCreateForm = request('form') === 'create';
        $editingBankAccount = request()->filled('edit')
            ? $bankAccounts->firstWhere('id', (int) request('edit'))
            : null;
    }

    $showCreateForm = $showCreateForm ?? false;
    $editingBankAccount = $editingBankAccount ?? null;

    // ---- URL (memakai route name yang sama; kalau route belum ada, jatuh ke "#" agar tidak error) ----
    $route = fn (string $name, $params = []) => \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : '#';
    $indexUrl = \Illuminate\Support\Facades\Route::has('banks.index') ? route('banks.index') : url()->current();
@endphp

<div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Informasi Bank</h1>
        @if (! $showCreateForm && ! $editingBankAccount)
            <a href="{{ $indexUrl . '?' . http_build_query(['form' => 'create']) }}" class="{{ $primaryBtn }}">
                + Tambah Bank
            </a>
        @endif
    </div>

    @if (session('success'))
        <div class="rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm dark:bg-green-900/20 dark:border-green-800 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    {{-- ==== FORM TAMBAH BANK ==== --}}
    @if ($showCreateForm)
        <div class="{{ $cardClass }} p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tambah Informasi Bank</h2>

            @if ($errors->any())
                <div class="{{ $errorBoxClass }}">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ $route('banks.store') }}" class="grid md:grid-cols-2 gap-4"
                  @if ($demo) onsubmit="{{ $demoAlert }}" @endif>
                @csrf
                <div>
                    <label for="create_bank_name" class="{{ $labelClass }}">Nama Bank</label>
                    <input type="text" id="create_bank_name" name="bank_name" value="{{ old('bank_name') }}"
                           placeholder="Masukkan nama bank" required class="{{ $inputClass }}">
                </div>
                <div>
                    <label for="create_account_number" class="{{ $labelClass }}">Nomor Rekening</label>
                    <input type="text" id="create_account_number" name="account_number" value="{{ old('account_number') }}"
                           placeholder="Masukkan nomor rekening (angka saja)" required {!! $accountNumberAttrs !!}
                           class="{{ $inputClass }}">
                </div>
                <div class="md:col-span-2">
                    <label for="create_account_holder_name" class="{{ $labelClass }}">Nama Pemilik Rekening</label>
                    <input type="text" id="create_account_holder_name" name="account_holder_name" value="{{ old('account_holder_name') }}"
                           placeholder="Masukkan nama pemilik rekening" required class="{{ $inputClass }}">
                </div>

                <div class="md:col-span-2 flex gap-3 mt-2">
                    <button type="submit" class="{{ $primaryBtn }}">Tambah Bank</button>
                    <a href="{{ $indexUrl }}" class="{{ $secondaryBtn }}">Batal</a>
                </div>
            </form>
        </div>
    @endif

    {{-- ==== FORM EDIT BANK ==== --}}
    @if ($editingBankAccount)
        <div class="{{ $cardClass }} p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Edit Informasi Bank</h2>

            @if ($errors->any())
                <div class="{{ $errorBoxClass }}">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ $route('banks.update', $editingBankAccount->id) }}" class="grid md:grid-cols-2 gap-4"
                  @if ($demo) onsubmit="{{ $demoAlert }}" @endif>
                @csrf
                @method('PUT')
                <div>
                    <label for="edit_bank_name" class="{{ $labelClass }}">Nama Bank</label>
                    <input type="text" id="edit_bank_name" name="bank_name" value="{{ old('bank_name', $editingBankAccount->bank_name) }}"
                           required class="{{ $inputClass }}">
                </div>
                <div>
                    <label for="edit_account_number" class="{{ $labelClass }}">Nomor Rekening</label>
                    <input type="text" id="edit_account_number" name="account_number" value="{{ old('account_number', $editingBankAccount->account_number) }}"
                           required {!! $accountNumberAttrs !!}
                           class="{{ $inputClass }}">
                </div>
                <div class="md:col-span-2">
                    <label for="edit_account_holder_name" class="{{ $labelClass }}">Nama Pemilik Rekening</label>
                    <input type="text" id="edit_account_holder_name" name="account_holder_name" value="{{ old('account_holder_name', $editingBankAccount->account_holder_name) }}"
                           required class="{{ $inputClass }}">
                </div>

                <div class="md:col-span-2 flex gap-3 mt-2">
                    <button type="submit" class="{{ $primaryBtn }}">Perbarui Bank</button>
                    <a href="{{ $indexUrl }}" class="{{ $secondaryBtn }}">Batal</a>
                </div>
            </form>
        </div>
    @endif

    {{-- ==== TABEL ==== --}}
    <div class="{{ $cardClass }} overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                <thead class="bg-slate-50 dark:bg-zinc-800">
                    <tr>
                        <th class="{{ $thClass }}">Nama Bank</th>
                        <th class="{{ $thClass }}">Nomor Rekening</th>
                        <th class="{{ $thClass }}">Nama Pemilik</th>
                        <th class="{{ $thClass }}">Status</th>
                        <th class="{{ $thClass }} text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                    @forelse ($bankAccounts as $bank)
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $bank->bank_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $bank->account_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $bank->account_holder_name }}</td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $bank->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($bank->status) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <div class="flex items-center justify-end space-x-2">
                                    {{-- Edit: server-driven lewat query ?edit={id} --}}
                                    <a href="{{ $indexUrl . '?' . http_build_query(['edit' => $bank->id]) }}"
                                       class="{{ $ghostBtnClass }}">Edit</a>

                                    <form method="POST" action="{{ $route('banks.status', $bank->id) }}"
                                          @if ($demo) onsubmit="{{ $demoAlert }}" @endif>
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="{{ $ghostBtnClass }}">
                                            {{ $bank->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ $route('banks.destroy', $bank->id) }}"
                                          onsubmit="{{ $demo ? $demoAlert : "return confirm('Apakah Anda yakin ingin menghapus bank ini?')" }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="{{ $ghostBtnClass }}">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">Belum ada data bank.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination gaya "Showing 1 to 10 of 23 results" (dilewati di mode demo karena datanya bukan paginator) --}}
        @if (method_exists($bankAccounts, 'total'))
            <div class="px-6 py-4 border-t border-gray-200 dark:border-zinc-700">
                {{ $bankAccounts->links('vendor.pagination.zakira') }}
            </div>
        @endif
    </div>

</div>
@endsection