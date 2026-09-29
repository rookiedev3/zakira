@extends('layouts.sidebar')
 
@section('title', 'Customer Service')
 
@section('content')
@php
    $inputClass = 'w-full border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500';
    $labelClass = 'block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2';
    $thClass = 'px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider';
    $cardClass = 'bg-white dark:bg-zinc-900 shadow rounded-lg border border-zinc-200 dark:border-zinc-700';
    $primaryBtn = 'inline-flex items-center justify-center h-10 px-5 text-sm font-medium rounded-lg bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] text-[var(--color-accent-foreground)] border border-black/10 dark:border-0';
    $secondaryBtn = 'inline-flex items-center justify-center h-10 px-5 text-sm font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-zinc-800 dark:text-white dark:hover:bg-zinc-700 transition-colors';
    $ghostBtnClass = 'relative items-center font-medium justify-center h-8 text-sm rounded-md px-3 inline-flex bg-transparent hover:bg-zinc-800/5 dark:hover:bg-white/15';
 
    // ---- MODE DEMO: aktif kalau controller tidak mengirim $customerServices ----
    $demo = ! isset($customerServices);
    $demoAlert = "alert('Mode demo: belum terhubung ke backend.'); return false;";
 
    if ($demo) {
        $customerServices = collect([
            (object) ['id' => 1, 'name' => 'CS Zakira 1', 'phone_number' => '081234567890', 'order' => 1, 'status' => 'aktif',    'is_floating_whatsapp' => true,  'whatsapp_url' => 'https://wa.me/6281234567890'],
            (object) ['id' => 2, 'name' => 'CS Zakira 2', 'phone_number' => '081298765432', 'order' => 2, 'status' => 'aktif',    'is_floating_whatsapp' => false, 'whatsapp_url' => 'https://wa.me/6281298765432'],
            (object) ['id' => 3, 'name' => 'CS Zakira 3', 'phone_number' => '085611223344', 'order' => 3, 'status' => 'nonaktif', 'is_floating_whatsapp' => false, 'whatsapp_url' => 'https://wa.me/6285611223344'],
        ]);
        $showCreateForm = request('form') === 'create';
        $editingCustomerService = request()->filled('edit')
            ? $customerServices->firstWhere('id', (int) request('edit'))
            : null;
    }
 
    $showCreateForm = $showCreateForm ?? false;
    $editingCustomerService = $editingCustomerService ?? null;
 
    // ---- URL (memakai route name yang sama; kalau route belum ada, jatuh ke "#" agar tidak error) ----
    $route = fn (string $name, $params = []) => \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : '#';
    $indexUrl = \Illuminate\Support\Facades\Route::has('customer-services.index') ? route('customer-services.index') : url()->current();
@endphp
 
<div class="space-y-6">
 
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Customer Service</h1>
        @if (! $showCreateForm && ! $editingCustomerService)
            <a href="{{ $indexUrl . '?' . http_build_query(['form' => 'create']) }}" class="{{ $primaryBtn }}">
                + Tambah Customer Service
            </a>
        @endif
    </div>
 
    @if (session('success'))
        <div class="rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm dark:bg-green-900/20 dark:border-green-800 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif
 
    {{-- ==== FORM TAMBAH ==== --}}
    @if ($showCreateForm)
        <div class="{{ $cardClass }} p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tambah Customer Service</h2>
 
            @if ($errors->any())
                <div class="rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 mb-4 dark:bg-red-900/20 dark:border-red-800 dark:text-red-300">
                    {{ $errors->first() }}
                </div>
            @endif
 
            <form method="POST" action="{{ $route('customer-services.store') }}" class="grid md:grid-cols-2 gap-4"
                  @if ($demo) onsubmit="{{ $demoAlert }}" @endif>
                @csrf
                <div>
                    <label for="create_name" class="{{ $labelClass }}">Nama</label>
                    <input type="text" id="create_name" name="name" value="{{ old('name') }}"
                           placeholder="Masukkan nama customer service" required class="{{ $inputClass }}">
                </div>
                <div>
                    <label for="create_phone" class="{{ $labelClass }}">Nomor Telepon</label>
                    <input type="text" id="create_phone" name="phone_number" value="{{ old('phone_number') }}"
                           placeholder="0812xxxxxxx" required class="{{ $inputClass }}">
                </div>
                <div>
                    <label for="create_order" class="{{ $labelClass }}">Urutan</label>
                    <input type="number" id="create_order" name="order" value="{{ old('order', 0) }}" min="0"
                           required class="{{ $inputClass }}">
                </div>
                <div class="flex items-center">
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 mt-6">
                        <input type="checkbox" name="status" value="aktif" @checked(old() ? old('status') === 'aktif' : true)
                               class="rounded border-gray-300 dark:border-zinc-600">
                        Aktif
                    </label>
                </div>
                <div class="md:col-span-2">
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input type="checkbox" name="is_floating_whatsapp" value="1" @checked(old('is_floating_whatsapp'))
                               class="rounded border-gray-300 dark:border-zinc-600">
                        Set ke Floating WhatsApp
                    </label>
                    <p class="text-xs text-gray-400 mt-1">Hanya satu nomor yang bisa diset sebagai floating WhatsApp</p>
                </div>
 
                <div class="md:col-span-2 flex gap-3 mt-2">
                    <button type="submit" class="{{ $primaryBtn }}">Tambah Customer Service</button>
                    <a href="{{ $indexUrl }}" class="{{ $secondaryBtn }}">Batal</a>
                </div>
            </form>
        </div>
    @endif
 
    {{-- ==== FORM EDIT ==== --}}
    @if ($editingCustomerService)
        <div class="{{ $cardClass }} p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Edit Customer Service</h2>
 
            @if ($errors->any())
                <div class="rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 mb-4 dark:bg-red-900/20 dark:border-red-800 dark:text-red-300">
                    {{ $errors->first() }}
                </div>
            @endif
 
            <form method="POST" action="{{ $route('customer-services.update', $editingCustomerService->id) }}" class="grid md:grid-cols-2 gap-4"
                  @if ($demo) onsubmit="{{ $demoAlert }}" @endif>
                @csrf
                @method('PUT')
                <div>
                    <label for="edit_name" class="{{ $labelClass }}">Nama</label>
                    <input type="text" id="edit_name" name="name" value="{{ old('name', $editingCustomerService->name) }}"
                           required class="{{ $inputClass }}">
                </div>
                <div>
                    <label for="edit_phone" class="{{ $labelClass }}">Nomor Telepon</label>
                    <input type="text" id="edit_phone" name="phone_number" value="{{ old('phone_number', $editingCustomerService->phone_number) }}"
                           required class="{{ $inputClass }}">
                </div>
                <div>
                    <label for="edit_order" class="{{ $labelClass }}">Urutan</label>
                    <input type="number" id="edit_order" name="order" value="{{ old('order', $editingCustomerService->order) }}" min="0"
                           required class="{{ $inputClass }}">
                </div>
                <div class="flex items-center">
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 mt-6">
                        <input type="checkbox" name="status" value="aktif"
                               @checked(old() ? old('status') === 'aktif' : $editingCustomerService->status === 'aktif')
                               class="rounded border-gray-300 dark:border-zinc-600">
                        Aktif
                    </label>
                </div>
                <div class="md:col-span-2">
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input type="checkbox" name="is_floating_whatsapp" value="1"
                               @checked(old() ? old('is_floating_whatsapp') : $editingCustomerService->is_floating_whatsapp)
                               class="rounded border-gray-300 dark:border-zinc-600">
                        Set ke Floating WhatsApp
                    </label>
                    <p class="text-xs text-gray-400 mt-1">Hanya satu nomor yang bisa diset sebagai floating WhatsApp</p>
                </div>
 
                <div class="md:col-span-2 flex gap-3 mt-2">
                    <button type="submit" class="{{ $primaryBtn }}">Perbarui Customer Service</button>
                    <a href="{{ $indexUrl }}" class="{{ $secondaryBtn }}">Batal</a>
                </div>
            </form>
        </div>
    @endif
 
    {{-- ==== TABEL ==== --}}
    <div class="{{ $cardClass }} overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                <thead class="bg-gray-50 dark:bg-zinc-800">
                    <tr>
                        <th class="{{ $thClass }}">Nama</th>
                        <th class="{{ $thClass }}">Nomor Telepon</th>
                        <th class="{{ $thClass }}">Urutan</th>
                        <th class="{{ $thClass }}">Status</th>
                        <th class="{{ $thClass }}">Floating WhatsApp</th>
                        <th class="{{ $thClass }} text-right">Aksi</th>
                    </tr>
                </thead>
 
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                    @forelse ($customerServices as $cs)
                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800/50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $cs->name }}</td>
 
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ $cs->whatsapp_url }}" target="_blank" rel="noopener"
                                   class="text-green-600 hover:underline">
                                    {{ $cs->phone_number }}
                                    <div class="text-xs text-green-500">WhatsApp</div>
                                </a>
                            </td>
 
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $cs->order }}</td>
 
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $cs->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($cs->status) }}
                                </span>
                            </td>
 
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if ($cs->is_floating_whatsapp)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-50 text-primary-800">
                                        Floating WhatsApp
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
 
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    {{-- Edit: server-driven lewat query ?edit={id} --}}
                                    <a href="{{ $indexUrl . '?' . http_build_query(['edit' => $cs->id]) }}"
                                       class="{{ $ghostBtnClass }} text-blue-600 hover:text-blue-800">Edit</a>
 
                                    <form method="POST" action="{{ $route('customer-services.status', $cs->id) }}"
                                          @if ($demo) onsubmit="{{ $demoAlert }}" @endif>
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="{{ $ghostBtnClass }} text-amber-600 hover:text-amber-800">
                                            {{ $cs->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
 
                                    <form method="POST" action="{{ $route('customer-services.destroy', $cs->id) }}"
                                          onsubmit="{{ $demo ? $demoAlert : 'return confirm(' . \Illuminate\Support\Js::from('Yakin hapus ' . $cs->name . '?') . ')' }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="{{ $ghostBtnClass }} text-red-600 hover:text-red-800">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">Belum ada data customer service.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
 
        @if (method_exists($customerServices, 'hasPages') && $customerServices->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-zinc-700">
                {{ $customerServices->links() }}
            </div>
        @endif
    </div>
 
</div>
@endsection
 