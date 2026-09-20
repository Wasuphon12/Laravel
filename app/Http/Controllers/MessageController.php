<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()->id;
        $messages = Message::query()
            ->where(fn ($query) => $query->where('sender_id', $userId)->orWhere('receiver_id', $userId))
            ->with(['sender', 'receiver', 'product'])
            ->latest()
            ->get();
        $conversations = $messages->groupBy(fn (Message $message) => $message->sender_id === $userId ? $message->receiver_id : $message->sender_id)
            ->map(function ($conversationMessages, $otherUserId) use ($userId) {
                $latest = $conversationMessages->first();

                return [
                    'user' => $latest->sender_id === $userId ? $latest->receiver : $latest->sender,
                    'latest' => $latest,
                    'unread' => $conversationMessages->where('receiver_id', $userId)->where('is_read', false)->count(),
                ];
            })->values();

        return view('messages.index', compact('conversations'));
    }

    public function show(Request $request, User $user): View
    {
        abort_if($user->id === $request->user()->id, 404);
        $userId = $request->user()->id;
        $conversationQuery = Message::query()->where(function ($query) use ($userId, $user) {
            $query->where('sender_id', $userId)->where('receiver_id', $user->id);
        })->orWhere(function ($query) use ($userId, $user) {
            $query->where('sender_id', $user->id)->where('receiver_id', $userId);
        });

        abort_unless($conversationQuery->exists(), 404);
        Message::query()->where('sender_id', $user->id)->where('receiver_id', $userId)->where('is_read', false)->update(['is_read' => true]);
        $messages = $conversationQuery->with('product')->oldest()->get();

        return view('messages.show', compact('messages', 'user'));
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        $count = Message::query()
            ->where('receiver_id', $request->user()->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', $count ? 'ทำเครื่องหมายข้อความใหม่ว่าอ่านแล้ว' : 'ไม่มีข้อความใหม่ให้ล้าง');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['receiver_id' => ['required', 'exists:users,id', 'different:sender_id'], 'product_id' => ['nullable', 'exists:products,id'], 'message' => ['required', 'string', 'max:3000']]);
        abort_if((int) $data['receiver_id'] === $request->user()->id, 422, 'ไม่สามารถส่งข้อความหาตนเองได้');
        $request->user()->sentMessages()->create($data);

        return back()->with('success', 'ส่งข้อความแล้ว');
    }
}
