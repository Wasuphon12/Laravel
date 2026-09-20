<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $ids = $this->productIds($request);
        $products = Product::query()
            ->whereIn('id', $ids)
            ->where('status', 'available')
            ->with(['images', 'dealer.dealerProfile'])
            ->get()
            ->sortBy(fn (Product $product) => array_search($product->id, $ids, true))
            ->values();

        // Keep the session cart accurate if a product was sold in another order.
        $request->session()->put('cart', $products->pluck('id')->all());

        return view('cart.index', ['products' => $products, 'total' => $products->sum('price')]);
    }

    public function add(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->status === 'available', 422, 'สินค้านี้ไม่พร้อมจำหน่ายแล้ว');

        $ids = $this->productIds($request);
        if (! in_array($product->id, $ids, true)) {
            $ids[] = $product->id;
        }
        $request->session()->put('cart', $ids);

        return back()->with('success', "เพิ่ม {$product->name} ลงตะกร้าแล้ว");
    }

    public function remove(Request $request, Product $product): RedirectResponse
    {
        $request->session()->put('cart', array_values(array_filter(
            $this->productIds($request),
            fn (int $id) => $id !== $product->id,
        )));

        return back()->with('success', 'นำสินค้าออกจากตะกร้าแล้ว');
    }

    public function clear(Request $request): RedirectResponse
    {
        $request->session()->forget('cart');

        return back()->with('success', 'ล้างตะกร้าสินค้าแล้ว');
    }

    /** @return list<int> */
    private function productIds(Request $request): array
    {
        return array_values(array_unique(array_map('intval', $request->session()->get('cart', []))));
    }
}
