<?php

namespace App\Http\Controllers;

use App\Models\AdminHandle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminHandleController extends Controller
{
    public function index(Request $request): View
    {
        $adminHandles = AdminHandle::latest()->paginate(10)->withQueryString();

        $editingAdminHandle = $request->filled('edit')
            ? AdminHandle::find($request->integer('edit'))
            : null;

        $showCreateForm = $request->query('form') === 'create';

        return view('admin-handle.index', compact('adminHandles', 'editingAdminHandle', 'showCreateForm'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        AdminHandle::create($data);

        return redirect()->route('admin-handles.index')->with('success', 'Admin handle berhasil ditambahkan.');
    }

    public function update(Request $request, AdminHandle $adminHandle): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $adminHandle->update($data);

        return redirect()->route('admin-handles.index')->with('success', 'Admin handle berhasil diperbarui.');
    }

    public function destroy(AdminHandle $adminHandle): RedirectResponse
    {
        $adminHandle->delete();

        return redirect()->route('admin-handles.index')->with('success', 'Admin handle berhasil dihapus.');
    }
}