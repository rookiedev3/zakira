<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BankAccountController extends Controller
{
    public function index(Request $request): View
    {
        $bankAccounts = BankAccount::latest()->paginate(10)->withQueryString();

        $editingBankAccount = $request->filled('edit')
            ? BankAccount::find($request->integer('edit'))
            : null;

        $showCreateForm = $request->query('form') === 'create';

        return view('bank.index', compact('bankAccounts', 'editingBankAccount', 'showCreateForm'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'account_holder_name' => 'required|string|max:255',
        ]);

        BankAccount::create($data);

        return redirect()->route('banks.index')->with('success', 'Bank berhasil ditambahkan.');
    }

    public function update(Request $request, BankAccount $bank): RedirectResponse
    {
        $data = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'account_holder_name' => 'required|string|max:255',
        ]);

        $bank->update($data);

        return redirect()->route('banks.index')->with('success', 'Bank berhasil diperbarui.');
    }

    public function updateStatus(BankAccount $bank): RedirectResponse
    {
        $bank->update([
            'status' => $bank->status === 'aktif' ? 'nonaktif' : 'aktif',
        ]);

        $message = $bank->status === 'aktif' ? 'Bank diaktifkan kembali.' : 'Bank dinonaktifkan.';

        return redirect()->route('banks.index')->with('success', $message);
    }

    public function destroy(BankAccount $bank): RedirectResponse
    {
        $bank->delete();

        return redirect()->route('banks.index')->with('success', 'Bank berhasil dihapus.');
    }
}