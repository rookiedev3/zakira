<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Media Sosial - Zakira</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
<body class="bg-gray-50 text-gray-800">

    <div class="max-w-6xl mx-auto px-4 py-8">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-serif font-bold">Media Sosial</h1>
            @if (! $showCreateForm && ! $editingSocialMedia)
                <a href="{{ route('social-media.index', ['form' => 'create']) }}"
                   class="bg-brand-600 text-white px-5 py-2.5 rounded-full font-semibold text-sm hover:bg-brand-700">
                    + Tambah Media Sosial
                </a>
            @endif
        </div>

        @if (session('success'))
            <div class="bg-green-50 text-green-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
        @endif

        {{-- ==== FORM TAMBAH ==== --}}
        @if ($showCreateForm)
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="font-semibold text-lg mb-4">Tambah Media Sosial</h2>

                @if ($errors->any())
                    <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('social-media.store') }}" class="grid md:grid-cols-2 gap-4">
                    @csrf

                    <div class="md:col-span-2 grid md:grid-cols-[100px_1fr] gap-4 items-start">
                        <div>
                            <label class="text-sm font-medium block mb-1">Logo & Warna</label>
                            <div id="previewIcon"
                                 class="w-14 h-14 rounded-full flex items-center justify-center text-white text-xl"
                                 style="background-color: {{ old('platform_choice') ? ($platforms[old('platform_choice')]['color'] ?? '#999') : '#999' }}">
                                <i id="previewIconTag" class="{{ old('platform_choice') ? ($platforms[old('platform_choice')]['icon'] ?? 'fas fa-link') : 'fas fa-link' }}"></i>
                            </div>
                        </div>
                        <div>
                            <label class="text-sm font-medium">Pilih Media Sosial</label>
                            <select name="platform_choice" id="platformSelect" required
                                    class="w-full border rounded-lg px-4 py-2 mt-1">
                                <option value="">-- Pilih platform --</option>
                                @foreach ($platforms as $name => $meta)
                                    <option value="{{ $name }}"
                                            data-icon="{{ $meta['icon'] }}"
                                            data-color="{{ $meta['color'] }}"
                                            @selected(old('platform_choice') === $name)>
                                        {{ $name }}
                                    </option>
                                @endforeach
                                <option value="Lainnya (Kustom)" @selected(old('platform_choice') === 'Lainnya (Kustom)')>Lainnya (Kustom)</option>
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Logo dan warna brand otomatis terisi sesuai pilihan.</p>
                        </div>
                    </div>

                    {{-- Muncul kalau pilih "Lainnya (Kustom)" --}}
                    <div id="customFields" class="md:col-span-2 grid md:grid-cols-2 gap-4 {{ old('platform_choice') === 'Lainnya (Kustom)' ? '' : 'hidden' }}">
                        <div>
                            <label class="text-sm font-medium">Nama Platform</label>
                            <input type="text" name="custom_platform_name" value="{{ old('custom_platform_name') }}" placeholder="contoh: Zakira Blog"
                                   class="w-full border rounded-lg px-4 py-2 mt-1">
                        </div>
                        <div>
                            <label class="text-sm font-medium">Ikon</label>
                            <input type="text" name="custom_icon_class" value="{{ old('custom_icon_class') }}" placeholder="contoh: fab fa-facebook"
                                   class="w-full border rounded-lg px-4 py-2 mt-1">
                            <p class="text-xs text-gray-400 mt-1">Kelas CSS Font Awesome</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium">Warna Brand</label>
                            <div class="flex items-center gap-3 mt-1">
                                <input type="color" id="customColorPicker" value="{{ old('custom_color', '#000000') }}"
                                       class="w-12 h-10 border rounded-lg cursor-pointer p-1">
                                <input type="text" name="custom_color" id="customColorText" value="{{ old('custom_color', '#000000') }}" placeholder="#000000"
                                       class="flex-1 border rounded-lg px-4 py-2">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium">Link / URL</label>
                        <input type="url" name="url" value="{{ old('url') }}" placeholder="https://facebook.com/username" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>
                    <div>
                        <label class="text-sm font-medium">Urutan</label>
                        <input type="number" name="order" value="{{ old('order', 0) }}" min="0" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>
                    <div class="flex items-center">
                        <label class="flex items-center gap-2 text-sm mt-6">
                            <input type="checkbox" name="status" value="1" checked class="rounded border-gray-300">
                            Aktif
                        </label>
                    </div>

                    <div class="md:col-span-2 flex gap-3 mt-2">
                        <button class="bg-brand-600 text-white px-6 py-2.5 rounded-full font-semibold text-sm hover:bg-brand-700">
                            Tambah Media Sosial
                        </button>
                        <a href="{{ route('social-media.index') }}" class="px-6 py-2.5 rounded-full font-semibold text-sm border">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        @endif

        {{-- ==== FORM EDIT ==== --}}
        @if ($editingSocialMedia)
            @php
                // Kalau nama platform yang tersimpan ADA di daftar config, berarti standar.
                // Kalau TIDAK ADA di config, berarti dulunya diisi manual (custom).
                $currentChoice = array_key_exists($editingSocialMedia->platform, $platforms)
                    ? $editingSocialMedia->platform
                    : 'Lainnya (Kustom)';

                $editColorValue = old('custom_color', $currentChoice === 'Lainnya (Kustom)' ? $editingSocialMedia->color : '#000000') ?: '#000000';
            @endphp

            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="font-semibold text-lg mb-4">Edit Media Sosial</h2>

                @if ($errors->any())
                    <div class="bg-red-50 text-red-600 text-sm rounded-lg px-4 py-3 mb-4">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('social-media.update', $editingSocialMedia) }}" class="grid md:grid-cols-2 gap-4">
                    @csrf
                    @method('PUT')

                    <div class="md:col-span-2 grid md:grid-cols-[100px_1fr] gap-4 items-start">
                        <div>
                            <label class="text-sm font-medium block mb-1">Logo & Warna</label>
                            <div id="previewIconEdit"
                                 class="w-14 h-14 rounded-full flex items-center justify-center text-white text-xl"
                                 style="background-color: {{ $editingSocialMedia->color }}">
                                <i id="previewIconTagEdit" class="{{ $editingSocialMedia->icon_class }}"></i>
                            </div>
                        </div>
                        <div>
                            <label class="text-sm font-medium">Pilih Media Sosial</label>
                            <select name="platform_choice" id="platformSelectEdit" required
                                    class="w-full border rounded-lg px-4 py-2 mt-1">
                                <option value="">-- Pilih platform --</option>
                                @foreach ($platforms as $name => $meta)
                                    <option value="{{ $name }}"
                                            data-icon="{{ $meta['icon'] }}"
                                            data-color="{{ $meta['color'] }}"
                                            @selected(old('platform_choice', $currentChoice) === $name)>
                                        {{ $name }}
                                    </option>
                                @endforeach
                                <option value="Lainnya (Kustom)" @selected(old('platform_choice', $currentChoice) === 'Lainnya (Kustom)')>Lainnya (Kustom)</option>
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Logo dan warna brand otomatis terisi sesuai pilihan.</p>
                        </div>
                    </div>

                    <div id="customFieldsEdit" class="md:col-span-2 grid md:grid-cols-2 gap-4 {{ $currentChoice === 'Lainnya (Kustom)' ? '' : 'hidden' }}">
                        <div>
                            <label class="text-sm font-medium">Nama Platform</label>
                            <input type="text" name="custom_platform_name"
                                   value="{{ old('custom_platform_name', $currentChoice === 'Lainnya (Kustom)' ? $editingSocialMedia->platform : '') }}"
                                   placeholder="contoh: Zakira Blog"
                                   class="w-full border rounded-lg px-4 py-2 mt-1">
                        </div>
                        <div>
                            <label class="text-sm font-medium">Ikon</label>
                            <input type="text" name="custom_icon_class"
                                   value="{{ old('custom_icon_class', $currentChoice === 'Lainnya (Kustom)' ? $editingSocialMedia->icon_class : '') }}"
                                   placeholder="contoh: fab fa-facebook"
                                   class="w-full border rounded-lg px-4 py-2 mt-1">
                            <p class="text-xs text-gray-400 mt-1">Kelas CSS Font Awesome</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium">Warna Brand</label>
                            <div class="flex items-center gap-3 mt-1">
                                <input type="color" id="customColorPickerEdit" value="{{ $editColorValue }}"
                                       class="w-12 h-10 border rounded-lg cursor-pointer p-1">
                                <input type="text" name="custom_color" id="customColorTextEdit" value="{{ $editColorValue }}" placeholder="#000000"
                                       class="flex-1 border rounded-lg px-4 py-2">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium">Link / URL</label>
                        <input type="url" name="url" value="{{ old('url', $editingSocialMedia->url) }}" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>
                    <div>
                        <label class="text-sm font-medium">Urutan</label>
                        <input type="number" name="order" value="{{ old('order', $editingSocialMedia->order) }}" min="0" required
                               class="w-full border rounded-lg px-4 py-2 mt-1">
                    </div>
                    <div class="flex items-center">
                        <label class="flex items-center gap-2 text-sm mt-6">
                            <input type="checkbox" name="status" value="1" @checked($editingSocialMedia->status === 'aktif') class="rounded border-gray-300">
                            Aktif
                        </label>
                    </div>

                    <div class="md:col-span-2 flex gap-3 mt-2">
                        <button class="bg-brand-600 text-white px-6 py-2.5 rounded-full font-semibold text-sm hover:bg-brand-700">
                            Perbarui Media Sosial
                        </button>
                        <a href="{{ route('social-media.index') }}" class="px-6 py-2.5 rounded-full font-semibold text-sm border">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        @endif

        {{-- ==== TABEL ==== --}}
        <div class="bg-white rounded-xl shadow-sm overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="px-4 py-3">Platform</th>
                        <th class="px-4 py-3">URL</th>
                        <th class="px-4 py-3">Urutan</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($socialMedia as $sm)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm"
                                         style="background-color: {{ $sm->color ?? '#999' }}">
                                        <i class="{{ $sm->icon_class }}"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium">{{ $sm->platform }}</p>
                                        <p class="text-xs text-gray-400">{{ $sm->icon_class }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ $sm->url }}" target="_blank" rel="noopener" class="text-brand-600 hover:underline break-all">
                                    {{ $sm->url }}
                                </a>
                            </td>
                            <td class="px-4 py-3">{{ $sm->order }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $sm->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($sm->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('social-media.index', ['edit' => $sm->id]) }}"
                                       class="text-brand-700 font-semibold text-xs">Edit</a>

                                    <form method="POST" action="{{ route('social-media.status', $sm) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="text-amber-600 font-semibold text-xs">
                                            {{ $sm->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('social-media.destroy', $sm) }}"
                                          onsubmit="return confirm('Yakin hapus {{ $sm->platform }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-500 font-semibold text-xs">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada data media sosial.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $socialMedia->links() }}
        </div>
    </div>

    <script>
        function setupPlatformPreview(selectId, iconWrapId, iconTagId, customFieldsId) {
            const select = document.getElementById(selectId);
            if (!select) return;

            select.addEventListener('change', function () {
                const option = this.options[this.selectedIndex];
                const iconWrap = document.getElementById(iconWrapId);
                const iconTag = document.getElementById(iconTagId);
                const customFields = document.getElementById(customFieldsId);

                if (this.value === 'Lainnya (Kustom)') {
                    customFields.classList.remove('hidden');
                    iconTag.className = 'fas fa-link';
                    iconWrap.style.backgroundColor = '#999';
                    return;
                }

                customFields.classList.add('hidden');

                if (option.dataset.icon) {
                    iconTag.className = option.dataset.icon;
                    iconWrap.style.backgroundColor = option.dataset.color || '#999';
                }
            });
        }

        function syncColorInputs(pickerId, textId) {
            const picker = document.getElementById(pickerId);
            const text = document.getElementById(textId);
            if (!picker || !text) return;

            // Color picker berubah -> update text
            picker.addEventListener('input', function () {
                text.value = this.value;
            });

            // Text diketik manual -> update color picker (kalau formatnya valid hex)
            text.addEventListener('input', function () {
                if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                    picker.value = this.value;
                }
            });
        }

        setupPlatformPreview('platformSelect', 'previewIcon', 'previewIconTag', 'customFields');
        setupPlatformPreview('platformSelectEdit', 'previewIconEdit', 'previewIconTagEdit', 'customFieldsEdit');

        syncColorInputs('customColorPicker', 'customColorText');
        syncColorInputs('customColorPickerEdit', 'customColorTextEdit');
    </script>
</body>
</html>