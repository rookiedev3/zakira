{{-- resources/views/products/create.blade.php --}}
@extends('layouts.sidebar')

@section('title', 'Buat Produk')

@section('content')
@php
    // Class "pf-field", "pf-btn-brown", "pf-btn-gray", "pf-file", dll didefinisikan di <style> di bawah,
    // jadi tidak perlu rebuild Tailwind agar border abu-abu & warna coklat muncul.
    $inputCls = 'pf-field w-full block h-10 py-2 ps-3 pe-3 text-base sm:text-sm leading-[1.375rem] rounded-lg appearance-none bg-white text-zinc-700 placeholder-zinc-400 shadow-xs disabled:bg-zinc-50 disabled:text-zinc-500 disabled:shadow-none dark:bg-white/10 dark:text-zinc-300 dark:placeholder-zinc-400 dark:disabled:bg-white/[7%]';
    $selectCls = 'pf-field w-full block h-10 py-2 ps-3 pe-10 text-base sm:text-sm leading-[1.375rem] rounded-lg appearance-none bg-white text-zinc-700 shadow-xs disabled:bg-zinc-50 disabled:text-zinc-500 disabled:shadow-none dark:bg-white/10 dark:text-zinc-300 dark:[&>option]:bg-zinc-700 dark:[&>option]:text-white';
    $textareaCls = 'pf-field block w-full p-3 text-base sm:text-sm rounded-lg resize-y bg-white text-zinc-700 placeholder-zinc-400 shadow-xs disabled:bg-zinc-50 disabled:text-zinc-500 disabled:shadow-none dark:bg-white/10 dark:text-zinc-300 dark:placeholder-zinc-400';
    $inputSoftCls = $inputCls;
    $labelCls = 'inline-flex items-center text-sm font-medium text-zinc-800 dark:text-white mb-3';
    $fileCls = 'pf-file block w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold';
    $addBtnCls = 'pf-btn-gray relative inline-flex items-center justify-center gap-2 whitespace-nowrap h-8 px-4 text-sm font-medium rounded-lg focus:outline-none disabled:opacity-75 disabled:cursor-default disabled:pointer-events-none';
    $delBtnCls = 'relative inline-flex items-center justify-center whitespace-nowrap h-8 px-3 text-sm font-medium rounded-md border border-zinc-300 bg-white text-black hover:bg-zinc-100 focus:outline-none focus:ring-2 focus:ring-zinc-400/30';
    $ghostBtnCls = 'relative inline-flex items-center justify-center gap-2 whitespace-nowrap h-10 ps-4 pe-4 text-sm font-medium rounded-lg border border-zinc-300 bg-white text-zinc-700 hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-zinc-400/30 dark:bg-transparent dark:border-white/10 dark:text-white dark:hover:bg-white/15';
    $backBtnCls = 'pf-btn-brown relative inline-flex items-center justify-center gap-2 whitespace-nowrap h-10 ps-4 pe-4 text-sm font-medium rounded-lg shadow-sm focus:outline-none';
    $primaryBtnCls = 'pf-btn-brown relative inline-flex items-center justify-center gap-2 whitespace-nowrap h-10 ps-4 pe-4 text-sm font-medium rounded-lg shadow-sm focus:outline-none disabled:opacity-75 disabled:cursor-default disabled:pointer-events-none';
    // Centang/ceklis tetap biru
    $checkCls = 'rounded border-zinc-300 text-blue-600 shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20';
    $plusIcon = '<svg class="shrink-0" style="width:1rem;height:1rem" width="16" height="16" data-flux-icon="" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z"/></svg>';

    $hasOldToken = old('_token') !== null;
    $initial = [
        'colors'      => old('colors', [[]]),
        'models'      => old('models', [[]]),
        'sizes'       => old('sizes', [['available' => 1]]),
        'free_items'  => old('free_items', []),
        'price_rules' => old('price_rules', []),
        'prices'      => old('prices', []),
    ];
@endphp

<style>
    /* Input, select, textarea: border abu-abu tipis, tanpa highlight hitam */
    .pf-field { border: 1px solid #e4e4e7; box-shadow: 0 1px 2px rgba(0, 0, 0, .05); }
    /* Saat kolom diklik: border & cincin coklat (sama dengan tombol) */
    .pf-field:focus { outline: none; border-color: #8b5a2b; box-shadow: 0 0 0 2px rgba(139, 90, 43, .25); }
    .pf-field:disabled { background-color: #fafafa; }
    .pf-field.border-red-400 { border-color: #f87171; }
    .dark .pf-field { border-color: rgba(255, 255, 255, .2); }

    /* Tulisan di dalam form: solid, tidak transparan */
    .pf-field { color: #18181b; opacity: 1; }
    .pf-field::placeholder { color: #52525b; opacity: 1; }
    .pf-field:disabled { color: #3f3f46; opacity: 1; -webkit-text-fill-color: #3f3f46; }
    .dark .pf-field { color: #ffffff; }
    .dark .pf-field::placeholder { color: #d4d4d8; opacity: 1; }

    /* Daftar dropdown (meniru tampilan bawaan browser, sorotan biru) */
    .pf-dd { position: fixed; z-index: 9999; box-sizing: border-box; max-height: 15rem; overflow-y: auto; background: #fff; border: 1px solid #c4c4c4; box-shadow: 0 2px 6px rgba(0, 0, 0, .25); }
    .pf-dd-item { padding: .45rem .75rem; color: #27272a; cursor: default; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; user-select: none; }
    .pf-dd-item.is-active { background-color: #2563eb; color: #fff; }

    /* Tombol abu-abu muda (semua tombol "Tambah ...") */
    .pf-btn-gray { background-color: #f4f4f5; color: #27272a; }
    .pf-btn-gray:hover { background-color: #e4e4e7; }
    .pf-btn-gray:focus-visible { box-shadow: 0 0 0 2px rgba(161, 161, 170, .35); }

    /* Tombol utama & kembali: coklat solid */
    .pf-btn-brown { background-color: #8b5a2b; color: #fff; border: 1px solid #6f4520; }
    .pf-btn-brown:hover { background-color: #6f4520; color: #fff; }
    .pf-btn-brown:focus-visible { box-shadow: 0 0 0 2px rgba(139, 90, 43, .4); }

    /* Tombol "Choose file" pada input file */
    .pf-file::file-selector-button { background-color: #f6ede3; color: #8b5a2b; cursor: pointer; }
    .pf-file:hover::file-selector-button { background-color: #eddcc9; }

    /* Badge "Utama" */
    .pf-badge-brown { background-color: #8b5a2b; }
</style>

<div class="[grid-area:main] p-6 lg:p-8 [[data-flux-container]_&]:px-0" data-flux-main="">
    <div class="space-y-6">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900">Buat Produk</h1>
            <a href="{{ route('products.index') }}" class="{{ $backBtnCls }}" data-flux-button="data-flux-button">
                Kembali ke Produk
            </a>
        </div>

        <!-- Form -->
        <div class="bg-white shadow rounded-lg">
            <form id="product-form" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-8">
                @csrf

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-700">
                        <ul class="list-disc ml-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Validasi sisi klien -->
                <div id="client-error-alert" class="hidden bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-700">
                    <span id="client-error-message"></span>
                </div>

                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="{{ $labelCls }}">Nama Produk</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="{{ $inputCls }}" placeholder="Masukkan nama produk" required autofocus>
                        @error('name')<div class="mt-3 text-sm font-medium text-red-500">{{ $message }}</div>@enderror
                    </div>

                    <div>
                        <label class="{{ $labelCls }}">Merek</label>
                        <select name="brand_id" class="{{ $selectCls }}" required>
                            <option value="">Pilih merek</option>
                            @foreach($brands ?? [] as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                        @if(empty($brands) || count($brands) === 0)
                            <div class="text-xs text-amber-600 mt-1">Belum ada data merek aktif. Silakan tambahkan merek terlebih dahulu.</div>
                        @endif
                        @error('brand_id')<div class="mt-3 text-sm font-medium text-red-500">{{ $message }}</div>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="{{ $labelCls }}">Status</label>
                        <select name="is_active" class="{{ $selectCls }}">
                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <!-- Zakira Commerce Settings -->
                <div class="border border-zinc-300 rounded-lg p-5 space-y-5">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Pengaturan Produk Zakira</h3>
                        <p class="text-sm text-gray-500 mt-1">Atur Ready Stock / PO dan siapa yang dapat melihat produk.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="{{ $labelCls }}">Jenis Produk</label>
                            <select name="product_type" class="{{ $selectCls }}">
                                <option value="ready" {{ old('product_type', 'ready') == 'ready' ? 'selected' : '' }}>Ready Stock</option>
                                <option value="po" {{ old('product_type') == 'po' ? 'selected' : '' }}>PO (Pre Order)</option>
                            </select>
                        </div>

                        <div>
                            <div class="text-sm font-medium text-gray-700 mb-2">Tampilkan Untuk</div>
                            <div class="space-y-2">
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="show_public" value="1"
                                           {{ $hasOldToken ? (old('show_public') ? 'checked' : '') : 'checked' }}
                                           class="{{ $checkCls }}">
                                    <span class="text-sm">Umum / Non Member</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="show_member" value="1"
                                           {{ $hasOldToken ? (old('show_member') ? 'checked' : '') : 'checked' }}
                                           class="{{ $checkCls }}">
                                    <span class="text-sm">Member</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="show_distributor" value="1" {{ old('show_distributor') ? 'checked' : '' }} class="{{ $checkCls }}">
                                    <span class="text-sm">Distributor</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="{{ $labelCls }}">Berat Produk (gram)</label>
                        <input type="number" name="weight_grams" value="{{ old('weight_grams', 1000) }}" min="1" step="1" class="{{ $inputCls }}" placeholder="1000" required>
                        <div class="text-xs text-gray-500 mt-1">Dipakai untuk hitung ongkir otomatis. Isi berat aktual produk beserta kemasan.</div>
                    </div>

                    <div>
                        <label class="{{ $labelCls }}">Note / Catatan Produk</label>
                        <textarea name="product_note" rows="3" class="{{ $textareaCls }}" placeholder="Contoh: Size XXL dikenakan tambahan harga sesuai bahan.">{{ old('product_note') }}</textarea>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="{{ $labelCls }}">Deskripsi</label>
                    <textarea name="description" rows="3" class="{{ $textareaCls }}" placeholder="Masukkan deskripsi produk">{{ old('description') }}</textarea>
                </div>

                <!-- Categories -->
                <div>
                    <label class="{{ $labelCls }}">Kategori</label>
                    @if(!empty($categories) && count($categories) > 0)
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mt-2">
                            @foreach($categories as $category)
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                                           {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}
                                           class="{{ $checkCls }}">
                                    <span class="text-sm text-zinc-600">{{ $category->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="text-sm text-gray-500 bg-gray-50 border border-zinc-200 rounded-lg p-4">Belum ada kategori. Produk tetap dapat disimpan tanpa kategori.</div>
                    @endif
                </div>

                <!-- Colors -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Warna</h3>
                        <button type="button" data-add="colors" class="{{ $addBtnCls }}">{!! $plusIcon !!}<span>Tambah Warna</span></button>
                    </div>
                    <div id="colors-list" class="space-y-4"></div>
                </div>

                <!-- Models -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Model</h3>
                        <button type="button" data-add="models" class="{{ $addBtnCls }}">{!! $plusIcon !!}<span>Tambah Model</span></button>
                    </div>
                    <div id="models-list" class="space-y-4"></div>
                </div>

                <!-- Sizes -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Ukuran</h3>
                        <button type="button" data-add="sizes" class="{{ $addBtnCls }}">{!! $plusIcon !!}<span>Tambah Ukuran</span></button>
                    </div>
                    <div id="sizes-list" class="space-y-4"></div>
                </div>

                <!-- Pricing Matrix -->
                <div id="matrix-wrap" class="hidden">
                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Harga Produk</h3>
                        <p class="text-sm text-gray-600 mt-1">Tentukan harga untuk setiap kombinasi model dan ukuran. Semua field harus diisi.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-separate border-spacing-0 border border-gray-200 rounded-lg overflow-hidden">
                            <thead class="bg-gray-50" id="matrix-head"></thead>
                            <tbody class="bg-white" id="matrix-body"></tbody>
                        </table>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-4">
                        <div class="flex">
                            <div class="flex-shrink-0"><i class="fas fa-info-circle text-blue-400"></i></div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    <strong>Penting:</strong> Isi harga untuk setiap kombinasi model dan ukuran.
                                    Ini akan menjadi harga yang ditampilkan kepada pelanggan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Free Items -->
                <div class="border border-zinc-300 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">FREE Barang / Bonus</h3>
                            <p class="text-sm text-gray-500">Kosongkan kondisi bila bonus berlaku untuk semua varian.</p>
                        </div>
                        <button type="button" data-add="free_items" class="{{ $addBtnCls }}">{!! $plusIcon !!}<span>Tambah FREE Item</span></button>
                    </div>
                    <div id="free_items-list" class="space-y-4"></div>
                    <div id="free_items-empty" class="text-sm text-gray-500 bg-gray-50 border border-zinc-200 rounded-lg p-4">Belum ada bonus. Klik “Tambah FREE Item”.</div>
                </div>

                <!-- Price Rules -->
                <div class="border border-zinc-300 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Harga Otomatis (Tambah / Potong)</h3>
                            <p class="text-sm text-gray-500">Rule dihitung dari harga dasar matriks. Kondisi kosong berarti berlaku ke semua.</p>
                        </div>
                        <button type="button" data-add="price_rules" class="{{ $addBtnCls }}">{!! $plusIcon !!}<span>Tambah Rule</span></button>
                    </div>
                    <div id="price_rules-list" class="space-y-4"></div>
                    <div id="price_rules-empty" class="text-sm text-gray-500 bg-gray-50 border border-zinc-200 rounded-lg p-4">Belum ada rule harga otomatis.</div>
                </div>

                <!-- Pratinjau Gambar Produk Umum -->
                <div id="gallery-preview-wrap" class="hidden">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Pratinjau Gambar Produk</h3>
                    <div id="gallery-preview-grid" class="grid grid-cols-3 md:grid-cols-6 gap-4"></div>
                </div>

                <!-- General Product Images -->
                <div>
                    <label class="{{ $labelCls }}">Gambar Produk Umum</label>
                    <div class="mt-2">
                        <input type="file" id="general_images_input" name="general_images[]" multiple accept="image/*" class="{{ $fileCls }}">
                        <p class="mt-1 text-sm text-gray-500">Pilih gambar produk umum. Gambar pertama akan diatur sebagai gambar utama.</p>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('products.index') }}" class="{{ $ghostBtnCls }}" data-flux-button="data-flux-button">Batal</a>
                    <button type="submit" id="submit-btn" class="{{ $primaryBtnCls }}" data-flux-button="data-flux-button">
                        <span id="submit-text">Buat Produk</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    const C = {
        input:  @json($inputCls),
        inputSoft: @json($inputSoftCls),
        select: @json($selectCls),
        label:  @json($labelCls),
        file:   @json($fileCls),
        delBtn: @json($delBtnCls),
        check:  @json($checkCls),
    };

    // Data awal (dari old() bila validasi gagal). File tidak bisa dipulihkan browser.
    const initial = @json($initial);

    const esc = s => String(s ?? '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    const $ = (sel, root = document) => root.querySelector(sel);
    const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

    let uidCounter = 0;
    const prices = {}; // key "muid:suid" => angka

    function field(label, inner) {
        return `<div><label class="${C.label}">${label}</label>${inner}</div>`;
    }
    function textInput(f, v, ph, req = false) {
        return `<input type="text" data-f="${f}" value="${esc(v)}" class="${C.input}" placeholder="${esc(ph)}" ${req ? 'required' : ''}>`;
    }
    function fileBlock(label) {
        return `<div class="mt-4" data-file-wrap>
            ${field(label, `<input type="file" data-f="image" accept="image/*" class="${C.file}">`)}
            <div class="image-preview-slot hidden mt-2"></div>
        </div>`;
    }
    function header(title) {
        return `<div class="flex items-center justify-between mb-4">
            <h4 class="text-md font-medium text-gray-900 row-title" data-title="${title}"></h4>
            <button type="button" data-remove class="${C.delBtn}">Hapus</button>
        </div>`;
    }
    const condSelect = (f, sel) => `<select data-f="${f}" data-cond="${f}" data-sel="${esc(sel ?? '')}" class="${C.select}"></select>`;

    const sections = {
        colors: {
            title: 'Warna', wrap: 'border border-zinc-200 rounded-lg p-4', min: 1,
            build: v => header('Warna') + `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    ${field('Nama Warna', `<input type="text" data-f="name" value="${esc(v.name)}" class="${C.inputSoft}" placeholder="contoh: Merah, Biru, Hitam" required>`)}
                    ${field('Kode Hex (Opsional)', `<input type="text" data-f="hex_code" value="${esc(v.hex_code)}" class="${C.inputSoft}" placeholder="contoh: #FF0000" maxlength="7">`)}
                </div>
                ${fileBlock('Gambar Warna')}`,
        },
        models: {
            title: 'Model', wrap: 'border border-zinc-300 rounded-lg p-4', min: 1,
            build: v => header('Model') + `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    ${field('Nama Model', textInput('name', v.name, 'contoh: Classic, Sport, Pro', true))}
                    ${field('Deskripsi Model', textInput('description', v.description, 'Deskripsi singkat model ini'))}
                </div>
                ${fileBlock('Gambar Model')}`,
        },
        sizes: {
            title: 'Ukuran', wrap: 'flex items-center space-x-4 p-4 border border-zinc-300 rounded-lg', min: 1,
            build: v => `
                <div class="flex-1">${field('Ukuran', textInput('size', v.size, 'contoh: XS, S, M, L, XL', true))}</div>
                <div class="flex items-center">
                    <label class="flex items-center space-x-2">
                        <input type="hidden" data-f="available" value="0">
                        <input type="checkbox" data-f="available" value="1" ${(v.available ?? 1) == 1 ? 'checked' : ''}
                               class="${C.check}">
                        <span class="text-sm text-gray-700">Tersedia</span>
                    </label>
                </div>
                <button type="button" data-remove class="${C.delBtn}">Hapus</button>`,
        },
        free_items: {
            title: 'FREE Item', wrap: 'border border-zinc-300 rounded-lg p-4', min: 0,
            build: v => header('FREE Item') + `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    ${field('Nama Bonus', textInput('name', v.name, 'contoh: Tas, Inner, Pin', true))}
                    ${field('Jumlah', `<input type="number" min="1" data-f="quantity" value="${esc(v.quantity ?? 1)}" class="${C.input}" placeholder="1" required>`)}
                    ${field('Kondisi Warna (Opsional)', condSelect('color', v.color))}
                    ${field('Kondisi Model (Opsional)', condSelect('model', v.model))}
                    ${field('Kondisi Ukuran (Opsional)', condSelect('size', v.size))}
                </div>`,
        },
        price_rules: {
            title: 'Rule', wrap: 'border border-zinc-300 rounded-lg p-4', min: 0,
            build: v => header('Rule') + `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    ${field('Kondisi Warna (Opsional)', condSelect('color', v.color))}
                    ${field('Kondisi Model (Opsional)', condSelect('model', v.model))}
                    ${field('Kondisi Ukuran (Opsional)', condSelect('size', v.size))}
                    ${field('Label (Opsional)', textInput('label', v.label, 'contoh: Tambahan bahan XXL'))}
                    ${field('Tipe', `<select data-f="type" class="${C.select}">
                        <option value="add" ${v.type === 'add' || !v.type ? 'selected' : ''}>Tambah</option>
                        <option value="cut" ${v.type === 'cut' ? 'selected' : ''}>Potong</option>
                    </select>`)}
                    ${field('Nominal', `<input type="number" min="1" data-f="amount" value="${esc(v.amount)}" class="${C.input}" placeholder="contoh: 10000" required>`)}
                </div>`,
        },
    };

    function addRow(key, values = {}) {
        const s = sections[key];
        const row = document.createElement('div');
        row.className = s.wrap;
        row.dataset.uid = ++uidCounter;
        row.innerHTML = s.build(values);
        $('#' + key + '-list').appendChild(row);
        refreshSection(key);
    }

    // Nomori ulang name="key[i][field]" sesuai urutan baris, update judul & tombol hapus
    function refreshSection(key) {
        const s = sections[key];
        const rows = $$('#' + key + '-list > div');
        rows.forEach((row, i) => {
            $$('[data-f]', row).forEach(inp => { inp.name = `${key}[${i}][${inp.dataset.f}]`; });
            const t = $('.row-title', row);
            if (t) t.textContent = `${s.title} ${i + 1}`;
            const rm = $('[data-remove]', row);
            if (rm) rm.classList.toggle('hidden', rows.length <= s.min);
        });
        const empty = $('#' + key + '-empty');
        if (empty) empty.classList.toggle('hidden', rows.length > 0);
        refreshConditionSelects();
        if (key === 'models' || key === 'sizes') renderMatrix();
    }

    function labelList(key, prefix, field) {
        return $$('#' + key + '-list > div').map((row, i) => {
            const el = $(`[data-f="${field}"]`, row);
            const v = el ? el.value.trim() : '';
            return v || `${prefix} ${i + 1}`;
        });
    }

    function refreshConditionSelects() {
        const lists = {
            color: [labelList('colors', 'Warna', 'name'), 'Semua warna'],
            model: [labelList('models', 'Model', 'name'), 'Semua model'],
            size:  [labelList('sizes', 'Ukuran', 'size'), 'Semua ukuran'],
        };
        ['free_items', 'price_rules'].forEach(key => {
            $$('#' + key + '-list select[data-cond]').forEach(sel => {
                const [labels, all] = lists[sel.dataset.cond];
                const current = sel.options.length ? sel.value : sel.dataset.sel;
                sel.innerHTML = `<option value="">${all}</option>` +
                    labels.map((l, i) => `<option value="${i}">${esc(l)}</option>`).join('');
                sel.value = (current !== '' && Number(current) < labels.length) ? current : '';
            });
        });
    }

    const fmt = n => String(n).replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    function renderMatrix() {
        const mRows = $$('#models-list > div');
        const sRows = $$('#sizes-list > div');
        const wrap = $('#matrix-wrap');
        wrap.classList.toggle('hidden', !(mRows.length && sRows.length));
        if (!mRows.length || !sRows.length) return;

        const mLabels = labelList('models', 'Model', 'name');
        const sLabels = labelList('sizes', 'Ukuran', 'size');

        $('#matrix-head').innerHTML = `<tr>
            <th class="w-20 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-r border-gray-200 sticky left-0 bg-gray-50 z-30">Model / Ukuran</th>
            ${sLabels.map(l => `<th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">${esc(l)}</th>`).join('')}
        </tr>`;

        $('#matrix-body').innerHTML = mRows.map((mr, mi) => `<tr>
            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 border-b border-r border-gray-200 sticky left-0 bg-white z-20 shadow-sm">${esc(mLabels[mi])}</td>
            ${sRows.map((sr, si) => {
                const key = mr.dataset.uid + ':' + sr.dataset.uid;
                const val = prices[key] ?? '';
                return `<td class="px-4 py-3 whitespace-nowrap text-center border-b border-r border-gray-200 last:border-r-0">
                    <input type="text" inputmode="numeric" data-price-key="${key}" value="${val === '' ? '' : fmt(val)}"
                           class="${C.inputSoft} matrix-cell-input min-w-[120px] text-sm" placeholder="Masukkan harga" required>
                    <input type="hidden" name="prices[${mi}][${si}]" value="${val}">
                </td>`;
            }).join('')}
        </tr>`).join('');
    }

    // Pratinjau gambar varian (warna / model), gaya sama seperti halaman edit
    function renderVariantPreview(input) {
        const wrap = input.closest('[data-file-wrap]');
        const slot = wrap && $('.image-preview-slot', wrap);
        if (!slot) return;
        if (input.files && input.files[0]) {
            const url = URL.createObjectURL(input.files[0]);
            slot.innerHTML = `<p class="text-sm text-gray-600 mb-1">Pratinjau gambar:</p>
                <div class="relative inline-block">
                    <img src="${url}" alt="Pratinjau" class="w-20 h-20 object-cover rounded border border-zinc-300">
                    <button type="button" data-clear-file class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 transition-colors">×</button>
                </div>`;
            slot.classList.remove('hidden');
        } else {
            slot.innerHTML = '';
            slot.classList.add('hidden');
        }
    }

    // Pratinjau galeri umum
    const galleryInput = $('#general_images_input');

    function renderGallery() {
        const wrap = $('#gallery-preview-wrap');
        const grid = $('#gallery-preview-grid');
        grid.innerHTML = '';
        const files = Array.from(galleryInput.files || []);
        wrap.classList.toggle('hidden', files.length === 0);

        files.forEach((file, idx) => {
            const url = URL.createObjectURL(file);
            const div = document.createElement('div');
            div.className = 'relative group w-20 h-20';
            div.innerHTML = `
                <img src="${url}" alt="${esc(file.name)}" class="w-20 h-20 object-cover rounded-lg border border-zinc-300">
                ${idx === 0 ? '<span class="pf-badge-brown absolute -top-2 -left-2 text-white text-xs px-2 py-1 rounded">Utama</span>' : ''}
                <button type="button" data-remove-gallery="${idx}" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 transition-colors">×</button>
                <div class="absolute bottom-0 left-0 right-0 bg-black/50 text-white text-xs p-1 rounded-b-lg">Order: ${idx}</div>`;
            grid.appendChild(div);
        });
    }

    galleryInput.addEventListener('change', renderGallery);

    // Event delegation
    document.addEventListener('click', e => {
        const add = e.target.closest('[data-add]');
        if (add) { addRow(add.dataset.add); return; }

        const clear = e.target.closest('[data-clear-file]');
        if (clear) {
            const wrap = clear.closest('[data-file-wrap]');
            const input = wrap && $('input[type="file"]', wrap);
            if (input) { input.value = ''; renderVariantPreview(input); }
            return;
        }

        const rg = e.target.closest('[data-remove-gallery]');
        if (rg) {
            const idx = Number(rg.dataset.removeGallery);
            const dt = new DataTransfer();
            Array.from(galleryInput.files).forEach((f, i) => { if (i !== idx) dt.items.add(f); });
            galleryInput.files = dt.files;
            renderGallery();
            return;
        }

        const rm = e.target.closest('[data-remove]');
        if (rm) {
            const row = rm.closest('#colors-list > div, #models-list > div, #sizes-list > div, #free_items-list > div, #price_rules-list > div');
            if (!row) return;
            const key = row.parentElement.id.replace('-list', '');
            row.remove();
            refreshSection(key);
        }
    });

    document.addEventListener('input', e => {
        const t = e.target;

        if (t.dataset.priceKey) {
            const raw = t.value.replace(/[^0-9]/g, '');
            t.value = raw === '' ? '' : fmt(raw);
            prices[t.dataset.priceKey] = raw === '' ? '' : parseInt(raw, 10);
            if (t.nextElementSibling) t.nextElementSibling.value = raw === '' ? '' : parseInt(raw, 10);
            if (raw !== '') t.classList.remove('border-red-400', 'bg-red-50');
            return;
        }

        // Sinkron color picker <-> hex
        if (t.hasAttribute('data-color-sync')) {
            const hexInp = t.parentElement.querySelector('input[data-f="hex_code"]');
            if (hexInp) hexInp.value = t.value.toUpperCase();
            return;
        }
        if (t.dataset.f === 'hex_code') {
            const picker = t.parentElement.querySelector('input[data-color-sync]');
            let v = t.value.trim();
            if (!v.startsWith('#')) v = '#' + v;
            if (/^#[0-9A-Fa-f]{6}$/.test(v) && picker) picker.value = v;
            return;
        }

        if (t.closest('#colors-list') || t.closest('#models-list') || t.closest('#sizes-list')) {
            refreshConditionSelects();
            if (!t.closest('#colors-list')) renderMatrix();
        }
    });

    document.addEventListener('change', e => {
        const t = e.target;
        if (t.type === 'file' && (t.closest('#colors-list') || t.closest('#models-list'))) {
            renderVariantPreview(t);
        }
    });

    // Validasi sisi klien saat submit
    const form = $('#product-form');
    const clientAlert = $('#client-error-alert');
    const clientMsg = $('#client-error-message');

    function showError(msg) {
        clientMsg.textContent = msg;
        clientAlert.classList.remove('hidden');
        clientAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    form.addEventListener('submit', function (e) {
        clientAlert.classList.add('hidden');

        const checks = [
            ['#colors-list input[data-f="name"]', 'Nama Warna'],
            ['#models-list input[data-f="name"]', 'Nama Model'],
            ['#sizes-list input[data-f="size"]', 'Nama Ukuran'],
        ];
        for (const [sel, label] of checks) {
            const inputs = $$(sel);
            for (let i = 0; i < inputs.length; i++) {
                if (!inputs[i].value.trim()) {
                    e.preventDefault();
                    showError(`${label} ${i + 1} wajib diisi.`);
                    inputs[i].focus();
                    return;
                }
            }
        }

        let firstEmpty = null;
        $$('.matrix-cell-input').forEach(inp => {
            const raw = inp.value.replace(/[^0-9]/g, '');
            if (raw === '') {
                inp.classList.add('border-red-400', 'bg-red-50');
                if (!firstEmpty) firstEmpty = inp;
            } else {
                inp.classList.remove('border-red-400', 'bg-red-50');
            }
        });
        if (firstEmpty) {
            e.preventDefault();
            showError('Harap lengkapi harga untuk semua kombinasi Model dan Ukuran pada Harga Produk.');
            firstEmpty.focus();
            return;
        }

        // Loading state
        const btn = $('#submit-btn');
        btn.disabled = true;
        $('#submit-text').textContent = 'Menyimpan...';
    });

    // Init
    initial.colors.forEach(v => addRow('colors', v));
    initial.models.forEach(v => addRow('models', v));
    initial.sizes.forEach(v => addRow('sizes', v));
    initial.free_items.forEach(v => addRow('free_items', v));
    initial.price_rules.forEach(v => addRow('price_rules', v));

    // Pulihkan harga lama berdasarkan posisi model/ukuran
    const mUids = $$('#models-list > div').map(r => r.dataset.uid);
    const sUids = $$('#sizes-list > div').map(r => r.dataset.uid);
    Object.entries(initial.prices || {}).forEach(([mi, row]) => {
        Object.entries(row || {}).forEach(([si, val]) => {
            if (mUids[mi] && sUids[si] && val !== null && val !== '') {
                prices[mUids[mi] + ':' + sUids[si]] = parseInt(val, 10);
            }
        });
    });
    renderMatrix();
})();
</script>

<script>
// Dropdown: tampilan kolom tetap bawaan, hanya daftar pilihannya yang diganti agar sorotannya biru (bukan abu-abu)
(function () {
    let dd = null, sel = null, active = -1;

    function close() {
        if (dd) { dd.remove(); }
        dd = null; sel = null; active = -1;
    }

    function setActive(i) {
        if (!dd) return;
        active = i;
        Array.from(dd.children).forEach((el, k) => el.classList.toggle('is-active', k === i));
        const el = dd.children[i];
        if (el) el.scrollIntoView({ block: 'nearest' });
    }

    function choose(i) {
        if (!sel) return;
        const s = sel;
        s.selectedIndex = i;
        close();
        s.dispatchEvent(new Event('input', { bubbles: true }));
        s.dispatchEvent(new Event('change', { bubbles: true }));
        s.focus();
    }

    function open(select) {
        close();
        sel = select;
        const cs = getComputedStyle(select);
        dd = document.createElement('div');
        dd.className = 'pf-dd';
        dd.setAttribute('role', 'listbox');
        dd.style.fontSize = cs.fontSize;
        dd.style.fontFamily = cs.fontFamily;

        Array.from(select.options).forEach((opt, i) => {
            const it = document.createElement('div');
            it.className = 'pf-dd-item';
            it.setAttribute('role', 'option');
            it.textContent = opt.text;
            if (opt.disabled) it.style.opacity = '.5';
            it.addEventListener('mouseenter', () => setActive(i));
            it.addEventListener('mousedown', ev => {
                ev.preventDefault();
                if (!opt.disabled) choose(i);
            });
            dd.appendChild(it);
        });

        document.body.appendChild(dd);

        const r = select.getBoundingClientRect();
        dd.style.width = r.width + 'px';
        dd.style.left = r.left + 'px';
        const h = Math.min(dd.scrollHeight, 240);
        const below = window.innerHeight - r.bottom;
        if (below < h + 12 && r.top > below) {
            dd.style.bottom = (window.innerHeight - r.top + 6) + 'px';
        } else {
            dd.style.top = (r.bottom + 6) + 'px';
        }
        setActive(select.selectedIndex);
    }

    document.addEventListener('mousedown', e => {
        const s = e.target.closest ? e.target.closest('select.pf-field') : null;
        if (s && !s.disabled) {
            e.preventDefault();          // cegah daftar bawaan browser (abu-abu)
            s.focus();
            if (dd && sel === s) close(); else open(s);
            return;
        }
        if (dd && !dd.contains(e.target)) close();
    }, true);

    document.addEventListener('keydown', e => {
        if (dd) {
            if (e.key === 'Escape') { e.preventDefault(); close(); return; }
            if (e.key === 'ArrowDown') { e.preventDefault(); setActive(Math.min(active + 1, sel.options.length - 1)); return; }
            if (e.key === 'ArrowUp') { e.preventDefault(); setActive(Math.max(active - 1, 0)); return; }
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); choose(active); return; }
            if (e.key === 'Tab') close();
            return;
        }
        const s = e.target.closest ? e.target.closest('select.pf-field') : null;
        if (s && !s.disabled && (e.key === 'Enter' || e.key === ' ' || (e.key === 'ArrowDown' && e.altKey))) {
            e.preventDefault();
            open(s);
        }
    }, true);

    window.addEventListener('scroll', e => { if (dd && !dd.contains(e.target) && e.target !== dd) close(); }, true);
    window.addEventListener('resize', close);
})();
</script>
@endsection