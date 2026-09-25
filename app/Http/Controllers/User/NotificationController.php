<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index()
    {
        $user          = Auth::user();
        $notifications = DB::table('notifications')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        $unreadCount = DB::table('notifications')
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return view('user.account.notifications', compact('user', 'notifications', 'unreadCount'));
    }

    public function markRead(string $id)
    {
        $user = Auth::user();
        DB::table('notifications')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->update(['read_at' => now()]);

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    public function markAllRead()
    {
        $user = Auth::user();
        DB::table('notifications')
            ->where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()->route('user.notifications.index')->with('success', 'All notifications marked as read.');
    }
}
