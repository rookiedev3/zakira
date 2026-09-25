<?php

namespace App\Http\Controllers;

use App\Models\Advantage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdvantageController extends Controller
{
    public function index(Request $request): View
    {
        $advantages = Advantage::orderBy('order')->orderByDesc('created_at')->paginate(10)->withQueryString();

        $editingAdvantage = $request->filled('edit')
            ? Advantage::find($request->integer('edit'))
            : null;

        $showCreateForm = $request->query('form') === 'create';

        return view('advantage.index', compact('advantages', 'editingAdvantage', 'showCreateForm'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:12288',
            'order' => 'required|integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('advantages', 'public');
        }

        $data['status'] = $request->boolean('status') ? 'aktif' : 'nonaktif';
        unset($data['image']);

        Advantage::create($data);

        return redirect()->route('advantages.index')->with('success', 'Keunggulan berhasil dibuat.');
    }

    public function update(Request $request, Advantage $advantage): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:12288',
            'order' => 'required|integer|min:0',
        ]);

        $data['status'] = $request->boolean('status') ? 'aktif' : 'nonaktif';

        if ($request->hasFile('image')) {
            if ($advantage->image_path) {
                Storage::disk('public')->delete($advantage->image_path);
            }
            $data['image_path'] = $request->file('image')->store('advantages', 'public');
        }

        unset($data['image']);

        $advantage->update($data);

        return redirect()->route('advantages.index')->with('success', 'Keunggulan berhasil diperbarui.');
    }

    public function updateStatus(Advantage $advantage): RedirectResponse
    {
        $advantage->update([
            'status' => $advantage->status === 'aktif' ? 'nonaktif' : 'aktif',
        ]);

        $message = $advantage->status === 'aktif' ? 'Keunggulan diaktifkan kembali.' : 'Keunggulan dinonaktifkan.';

        return redirect()->route('advantages.index')->with('success', $message);
    }

    public function destroy(Advantage $advantage): RedirectResponse
    {
        if ($advantage->image_path) {
            Storage::disk('public')->delete($advantage->image_path);
        }

        $advantage->delete();

        return redirect()->route('advantages.index')->with('success', 'Keunggulan berhasil dihapus.');
    }
}