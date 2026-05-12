<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class NotificationController extends Controller
{
    /**
     * Show all notifications for authenticated user.
     */
    public function index(): View
    {
        $notifications = auth()->user()->notifications()->paginate(20);
        $unreadCount = auth()->user()->unreadNotifications()->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Notification $notification): RedirectResponse
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        return redirect()->back()->with('success', 'Notifikasi telah ditandai sebagai terbaca');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): RedirectResponse
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sebagai terbaca');
    }

    /**
     * Delete notification.
     */
    public function destroy(Notification $notification): RedirectResponse
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->delete();

        return redirect()->back()->with('success', 'Notifikasi telah dihapus');
    }

    /**
     * Get unread notifications count (API endpoint).
     */
    public function getUnreadCount(): JsonResponse
    {
        $count = auth()->user()->unreadNotifications()->count();

        return response()->json([
            'unread_count' => $count,
        ]);
    }

    /**
     * Get latest unread notifications (for dashboard widget).
     */
    public function getLatestUnread(): JsonResponse
    {
        $notifications = auth()->user()->unreadNotifications()
            ->limit(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'notifications' => $notifications,
        ]);
    }
}
