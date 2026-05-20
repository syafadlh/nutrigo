<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller {

    public function index() {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(20);

        // Tandai semua sebagai sudah dibaca
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('user.notifications', compact('notifications'));
    }

    public function markAllRead() {
        Notification::where('user_id', Auth::id())->update(['is_read' => true]);
        return back()->with('success', 'Semua notifikasi sudah dibaca.');
    }
}