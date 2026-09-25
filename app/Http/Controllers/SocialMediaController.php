<?php

namespace App\Http\Controllers;

use App\Models\SocialMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SocialMediaController extends Controller
{
    public function index(Request $request): View
    {
        $socialMedia = SocialMedia::orderBy('order')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $editingSocialMedia = $request->filled('edit')
            ? SocialMedia::find($request->integer('edit'))
            : null;

        $showCreateForm = $request->query('form') === 'create';

        $platforms = config('social_platforms');

        return view('social-media.index', compact('socialMedia', 'editingSocialMedia', 'showCreateForm', 'platforms'));
    }

    public function store(Request $request): RedirectResponse
    {
        SocialMedia::create($this->resolvePlatformData($request));

        return redirect()->route('social-media.index')->with('success', 'Media Sosial berhasil ditambahkan.');
    }

    public function update(Request $request, SocialMedia $socialMedium): RedirectResponse
    {
        $socialMedium->update($this->resolvePlatformData($request));

        return redirect()->route('social-media.index')->with('success', 'Media Sosial berhasil diperbarui.');
    }

    public function updateStatus(SocialMedia $socialMedium): RedirectResponse
    {
        $socialMedium->update([
            'status' => $socialMedium->status === 'aktif' ? 'nonaktif' : 'aktif',
        ]);

        $message = $socialMedium->status === 'aktif' ? 'Media Sosial diaktifkan kembali.' : 'Media Sosial dinonaktifkan.';

        return redirect()->route('social-media.index')->with('success', $message);
    }

    public function destroy(SocialMedia $socialMedium): RedirectResponse
    {
        $socialMedium->delete();

        return redirect()->route('social-media.index')->with('success', 'Media Sosial berhasil dihapus.');
    }

    /**
     * Susun data platform: kalau pilih platform standar, icon & warna
     * otomatis dari config. Kalau pilih "Lainnya (Kustom)", semua
     * (nama, icon, warna) diisi manual lewat 3 field tambahan.
     */
    private function resolvePlatformData(Request $request): array
    {
        $base = $request->validate([
            'platform_choice' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'order' => 'required|integer|min:0',
        ]);

        $status = $request->boolean('status') ? 'aktif' : 'nonaktif';

        if ($base['platform_choice'] === 'Lainnya (Kustom)') {
            $custom = $request->validate([
                'custom_platform_name' => 'required|string|max:255',
                'custom_icon_class' => 'required|string|max:255',
                'custom_color' => 'nullable|string|max:20',
            ]);

            return [
                'platform' => $custom['custom_platform_name'],
                'icon_class' => $custom['custom_icon_class'],
                'color' => $custom['custom_color'] ?? null,
                'url' => $base['url'],
                'order' => $base['order'],
                'status' => $status,
            ];
        }

        $mapping = config("social_platforms.{$base['platform_choice']}");

        return [
            'platform' => $base['platform_choice'],
            'icon_class' => $mapping['icon'] ?? 'fas fa-link',
            'color' => $mapping['color'] ?? null,
            'url' => $base['url'],
            'order' => $base['order'],
            'status' => $status,
        ];
    }
}