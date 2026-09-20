<?php

namespace App\Http\Controllers;

use App\Models\DealerReview;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DealerReviewController extends Controller
{
    public function store(Request $request, OrderItem $item): RedirectResponse
    {
        $item->loadMissing('order');

        abort_unless(
            $item->order->customer_id === $request->user()->id && $item->delivery_status === 'delivered',
            403,
        );

        abort_if($item->dealerReview()->exists(), 422, 'คุณให้คะแนนรายการนี้แล้ว');

        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        DealerReview::create([
            'order_item_id' => $item->id,
            'reviewer_id' => $request->user()->id,
            'dealer_id' => $item->dealer_id,
            ...$data,
        ]);

        return back()->with('success', 'ขอบคุณสำหรับคะแนนรีวิวร้านค้า');
    }
}
