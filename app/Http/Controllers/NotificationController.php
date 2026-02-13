<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserNotification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $notifications = UserNotification::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate(15);

        $unreadCount = UserNotification::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();

        return view('notifications.index', compact('notifications','unreadCount'));
    }

    public function markRead(Request $request, $id)
    {
        $n = UserNotification::where('user_id', $request->user()->id)->findOrFail($id);
        if (is_null($n->read_at)) {
            $n->read_at = now();
            $n->save();
        }
        return back();
    }

    public function markAllRead(Request $request)
    {
        UserNotification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        return back();
    }

    // JSON feed สำหรับ dropdown (ล่าสุด 10)
    public function feed(Request $request)
    {
        $items = UserNotification::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get(['id','type','data','read_at','created_at']);

        $unread = $items->whereNull('read_at')->count();

        return response()->json([
            'unread' => $unread,
            'items'  => $items,
        ]);
    }
}