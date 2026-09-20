<?php

namespace App\Providers;

use App\Models\DealerProfile;
use App\Models\Dispute;
use App\Models\Message;
use App\Models\OrderItem;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('layouts.navigation', function ($view): void {
            $user = auth()->user();
            if (! $user) {
                $view->with(['cartCount' => 0, 'unreadMessageCount' => 0, 'navNotificationCount' => 0, 'notificationTarget' => route('login'), 'chatConversations' => collect()]);

                return;
            }

            $unreadMessages = Message::query()->where('receiver_id', $user->id)->where('is_read', false)->count();
            $recentMessages = Message::query()
                ->where(fn ($query) => $query->where('sender_id', $user->id)->orWhere('receiver_id', $user->id))
                ->with(['sender', 'receiver', 'product'])
                ->latest()
                ->limit(120)
                ->get();
            $chatConversations = $recentMessages
                ->groupBy(fn (Message $message) => $message->sender_id === $user->id ? $message->receiver_id : $message->sender_id)
                ->map(function ($conversationMessages, $otherUserId) use ($user) {
                    $latest = $conversationMessages->first();

                    return [
                        'user' => $latest->sender_id === $user->id ? $latest->receiver : $latest->sender,
                        'latest' => $latest,
                        'unread' => $conversationMessages->where('receiver_id', $user->id)->where('is_read', false)->count(),
                        'messages' => $conversationMessages->sortBy('created_at')->take(-30)->values(),
                    ];
                })
                ->values();
            $notificationTarget = route('messages.index');
            $roleNotifications = 0;

            if ($user->isDealer()) {
                $roleNotifications = OrderItem::query()
                    ->where('dealer_id', $user->id)
                    ->whereHas('order', fn ($query) => $query->where('slip_status', 'pending'))
                    ->count();
                $notificationTarget = route('dashboard');
            } elseif ($user->isAdmin()) {
                $roleNotifications = DealerProfile::query()->where('status', 'pending')->count()
                    + Dispute::query()->whereIn('status', ['open', 'reviewing'])->count();
                $notificationTarget = route('admin.dashboard');
            } else {
                $roleNotifications = $user->orders()
                    ->whereHas('items', fn ($query) => $query->where('delivery_status', 'shipped'))
                    ->count();
                $notificationTarget = route('dashboard');
            }

            $view->with([
                'cartCount' => count(array_unique(array_map('intval', session('cart', [])))),
                'unreadMessageCount' => $unreadMessages,
                'navNotificationCount' => $unreadMessages + $roleNotifications,
                'notificationTarget' => $notificationTarget,
                'chatConversations' => $chatConversations,
            ]);
        });
    }
}
