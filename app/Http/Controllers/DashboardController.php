<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        if ($user->isAdmin()) {
            return app(AdminController::class)->index();
        }
        if ($user->isDealer()) {
            return view('dealer.dashboard', [
                'profile' => $user->dealerProfile,
                'items' => OrderItem::query()->where('dealer_id', $user->id)->with('product', 'order.customer')->latest()->get(),
            ]);
        }

        return view('customer.dashboard', ['orders' => $user->orders()->with('items.product.images', 'items.dealer')->latest()->get()]);
    }
}
