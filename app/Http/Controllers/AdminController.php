<?php

namespace App\Http\Controllers;

use App\Models\DealerProfile;
use App\Models\Dispute;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'profiles' => DealerProfile::query()->where('status', 'pending')->with('user')->latest()->get(),
            'disputes' => Dispute::query()->whereIn('status', ['open', 'reviewing'])->with('orderItem.product', 'customer', 'dealer')->latest()->get(),
            'stats' => [
                'users' => User::query()->count(),
                'products' => Product::query()->where('status', 'available')->count(),
                'orders' => Order::query()->count(),
                'sales' => Order::query()->where('payment_status', 'paid')->sum('total_amount'),
            ],
        ]);
    }

    public function updateKyc(Request $request, DealerProfile $profile): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:approved,rejected']]);
        $profile->update($data);

        return back()->with('success', 'อัปเดตสถานะ KYC แล้ว');
    }

    public function updateDispute(Request $request, Dispute $dispute): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:reviewing,resolved_refund,resolved_reject']]);
        $dispute->update($data);
        return back()->with('success', 'อัปเดตข้อพิพาทแล้ว');
    }
}
