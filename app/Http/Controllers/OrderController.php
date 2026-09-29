<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'model_id'   => 'nullable|integer',
            'color_id'   => 'nullable|integer',
            'size_id'    => 'nullable|integer',
            'quantity'   => 'required|integer|min:1',
        ]);

        Order::create($data);

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }
}