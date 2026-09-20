<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DisputeController extends Controller
{
    public function store(Request $request, OrderItem $item): RedirectResponse
    {
        abort_unless($item->order->customer_id === $request->user()->id, 403);
        $data = $request->validate(['reason' => ['required', 'string', 'max:5000'], 'evidence_urls' => ['nullable', 'array'], 'evidence_urls.*' => ['url']]);
        $item->dispute()->create($data + ['customer_id' => $request->user()->id, 'dealer_id' => $item->dealer_id]);
        $item->update(['delivery_status' => 'disputed']);

        return back()->with('success', 'เปิดข้อพิพาทเรียบร้อย ผู้ดูแลจะตรวจสอบหลักฐาน');
    }
}
