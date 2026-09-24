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
        $socialMedia = SocialMedia::orderBy('order')->paginate(10)->withQueryString();

        $editingSocialMedia = $request->filled('edit')
            ? SocialMedia::find($request->integer('edit'))
            : null;

        $showCreateForm = $request->query('form') === 'create';

        $platforms = config('social_platforms');

        return view('social-media.index', compact('socialMedia', 'editingSocialMedia', 'showCreateForm', 'platforms'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'platform' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'order' => 'required|integer|min:0',
        ]);

        $data['status'] = $request->boolean('status') ? 'aktif' : 'nonaktif';

        // Kalau platform "Lainnya (Kustom)", ambil icon & warna dari input manual
        if ($data['platform'] === 'Lainnya (Kustom)') {
            $custom = $request->validate([
                'custom_icon_class' => 'required|string|max:255',
                'custom_color' => 'nullable|string|max:20',
            ]);
            $data['icon_class'] = $custom['custom_icon_class'];
            $data['color'] = $custom['custom_color'] ?? null;
        } else {
            $mapping = config("social_platforms.{$data['platform']}");
            $data['icon_class'] = $mapping['icon'] ?? 'fas fa-link';
            $data['color'] = $mapping['color'] ?? null;
        }

        SocialMedia::create($data);

        return redirect()->route('social-media.index')->with('success', 'Media Sosial berhasil ditambahkan.');
    }

    public function update(Request $request, SocialMedia $socialMedium): RedirectResponse
    {
        $data = $request->validate([
            'platform' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'order' => 'required|integer|min:0',
        ]);

        $data['status'] = $request->boolean('status') ? 'aktif' : 'nonaktif';

        if ($data['platform'] === 'Lainnya (Kustom)') {
            $custom = $request->validate([
                'custom_icon_class' => 'required|string|max:255',
                'custom_color' => 'nullable|string|max:20',
            ]);
            $data['icon_class'] = $custom['custom_icon_class'];
            $data['color'] = $custom['custom_color'] ?? null;
        } else {
            $mapping = config("social_platforms.{$data['platform']}");
            $data['icon_class'] = $mapping['icon'] ?? 'fas fa-link';
            $data['color'] = $mapping['color'] ?? null;
        }

        $socialMedium->update($data);

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
}