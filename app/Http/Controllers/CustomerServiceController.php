<?php

namespace App\Http\Controllers;

use App\Models\CustomerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerServiceController extends Controller
{
    public function index(Request $request): View
    {
        $customerServices = CustomerService::orderBy('order')->paginate(10)->withQueryString();

        $editingCustomerService = $request->filled('edit')
            ? CustomerService::find($request->integer('edit'))
            : null;

        $showCreateForm = $request->query('form') === 'create';

        return view('customer-service.index', compact('customerServices', 'editingCustomerService', 'showCreateForm'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data) {
            // Kalau yang baru diset jadi floating, matikan floating yang lain dulu
            if ($data['is_floating_whatsapp']) {
                CustomerService::where('is_floating_whatsapp', true)->update(['is_floating_whatsapp' => false]);
            }

            CustomerService::create($data);
        });

        return redirect()->route('customer-services.index')->with('success', 'Customer Service berhasil ditambahkan.');
    }

    public function update(Request $request, CustomerService $customerService): RedirectResponse
    {
        $data = $this->validated($request);

        DB::transaction(function () use ($data, $customerService) {
            if ($data['is_floating_whatsapp']) {
                CustomerService::where('id', '!=', $customerService->id)
                    ->where('is_floating_whatsapp', true)
                    ->update(['is_floating_whatsapp' => false]);
            }

            $customerService->update($data);
        });

        return redirect()->route('customer-services.index')->with('success', 'Customer Service berhasil diperbarui.');
    }

    public function updateStatus(CustomerService $customerService): RedirectResponse
    {
        $customerService->update([
            'status' => $customerService->status === 'aktif' ? 'nonaktif' : 'aktif',
        ]);

        $message = $customerService->status === 'aktif' ? 'Customer Service diaktifkan kembali.' : 'Customer Service dinonaktifkan.';

        return redirect()->route('customer-services.index')->with('success', $message);
    }

    public function destroy(CustomerService $customerService): RedirectResponse
    {
        $customerService->delete();

        return redirect()->route('customer-services.index')->with('success', 'Customer Service berhasil dihapus.');
    }

    /**
     * Validasi bersama untuk tambah & edit.
     * Nomor telepon: hanya angka (boleh diawali "+"), format Indonesia seperti di checkout.
     */
    private function validated(Request $request): array
    {
        // Checkbox "Aktif" yang tidak dicentang tidak ikut terkirim oleh browser
        $request->merge([
            'status' => $request->input('status') === 'aktif' ? 'aktif' : 'nonaktif',
        ]);

        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'phone_number' => ['required', 'regex:/^(\+62|62|0)8[0-9]{8,12}$/'],
            'order'        => 'required|integer|min:0',
            'status'       => 'required|in:aktif,nonaktif',
        ], [
            'phone_number.required' => 'Nomor telepon wajib diisi.',
            'phone_number.regex'    => 'Nomor telepon harus berupa angka dan diawali 08. Contoh: 081234567890.',
        ]);

        $data['is_floating_whatsapp'] = $request->boolean('is_floating_whatsapp');

        return $data;
    }
}