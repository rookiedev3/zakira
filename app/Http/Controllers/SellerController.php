<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Seller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerController extends Controller
{
public function index(Request $request): View
{
    $sellers = Seller::with('brand')->orderBy('seller_id')->get();

    $editingSeller = $request->filled('edit')
        ? Seller::find($request->integer('edit'))
        : null;

    $showCreateForm = $request->query('form') === 'create';

    $brands = Brand::active()->orderBy('name')->get();

    return view('seller.index', compact('sellers', 'editingSeller', 'showCreateForm', 'brands'));
}

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'seller_id' => 'required|string|max:255|unique:sellers,seller_id',
            'name' => 'required|string|max:255',
            'brand_id' => 'required|exists:brands,id',
        ]);

        Seller::create($data);

        return redirect()->route('seller.index')->with('success', 'Seller berhasil ditambahkan.');
    }

    public function update(Request $request, Seller $seller): RedirectResponse
    {
        $data = $request->validate([
            'seller_id' => 'required|string|max:255|unique:sellers,seller_id,' . $seller->id,
            'name' => 'required|string|max:255',
            'brand_id' => 'required|exists:brands,id',
        ]);

        $seller->update($data);

        return redirect()->route('seller.index')->with('success', 'Seller berhasil diperbarui.');
    }

    public function destroy(Seller $seller): RedirectResponse
    {
        $seller->delete();

        return redirect()->route('seller.index')->with('success', 'Seller berhasil dihapus.');
    }
}