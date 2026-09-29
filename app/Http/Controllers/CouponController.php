<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(Request $request): View
    {
        $coupons = Coupon::latest()->get();

        return view('kupon.index', compact('coupons'));
    }

    public function create(): View
    {
        return view('kupon.create', [
            'categories' => Category::orderBy('name')->get(),
            'brands' => Brand::active()->orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        $coupon = Coupon::create($data);
        $this->syncRestrictions($coupon, $request);

        return redirect()->route('kupon.index')->with('success', 'Kupon berhasil dibuat.');
    }

    public function edit(Coupon $kupon): View
    {
        $kupon->load(['categories', 'brands', 'products']);

        return view('kupon.edit', [
            'coupon' => $kupon,
            'categories' => Category::orderBy('name')->get(),
            'brands' => Brand::active()->orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Coupon $kupon): RedirectResponse
    {
        $data = $this->validateData($request, $kupon->id);

        $kupon->update($data);
        $this->syncRestrictions($kupon, $request);

        return redirect()->route('kupon.index')->with('success', 'Kupon berhasil diperbarui.');
    }

    public function duplicate(Coupon $kupon): RedirectResponse
    {
        $clone = $kupon->replicate();
        $clone->name = $kupon->name . ' (Copy)';
        $clone->code = $kupon->code . '-' . strtoupper(Str::random(4));
        $clone->used_count = 0;
        $clone->save();

        $clone->categories()->sync($kupon->categories->pluck('id'));
        $clone->brands()->sync($kupon->brands->pluck('id'));
        $clone->products()->sync($kupon->products->pluck('id'));

        return redirect()->route('kupon.index')->with('success', 'Kupon berhasil diduplikasi.');
    }

    public function toggleActive(Coupon $kupon): RedirectResponse
    {
        $kupon->update(['active' => ! $kupon->active]);

        $message = $kupon->active ? 'Kupon diaktifkan.' : 'Kupon dinonaktifkan.';

        return redirect()->route('kupon.index')->with('success', $message);
    }

    public function destroy(Coupon $kupon): RedirectResponse
    {
        $kupon->delete();

        return redirect()->route('kupon.index')->with('success', 'Kupon berhasil dihapus.');
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:coupons,code,' . ($ignoreId ?? 'NULL') . ',id',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_customer' => 'nullable|integer|min:1',
            'customer_scope' => 'required|in:all,member,non_member',
            'minimum_amount' => 'nullable|numeric|min:0',
            'minimum_quantity' => 'nullable|integer|min:1',
            'restriction_type' => 'nullable|in:only,except',
            'checkout_label' => 'nullable|string|max:255',
            'checkout_description' => 'nullable|string',
        ]);

        $data['active'] = $request->boolean('active');
        $data['new_customers_only'] = $request->boolean('new_customers_only');
        $data['stackable'] = $request->boolean('stackable');
        $data['show_in_checkout'] = $request->boolean('show_in_checkout');

        return $data;
    }

    private function syncRestrictions(Coupon $coupon, Request $request): void
    {
        $coupon->categories()->sync($request->input('categories', []));
        $coupon->brands()->sync($request->input('brands', []));
        $coupon->products()->sync($request->input('products', []));
    }
}