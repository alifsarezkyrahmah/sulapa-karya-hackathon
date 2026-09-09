<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    // Saat user mengklik satu notifikasi, tandai sudah dibaca lalu redirect ke halamannya
    public function read($id)
    {
        $userId = session('user_id') ?? auth()->id();
        $notification = Notification::where('id', $id)->where('user_id', $userId)->firstOrFail();
        
        $notification->update(['is_read' => true]);

        return redirect($notification->link ?? back());
    }

    // Tombol "Tandai Semua Dibaca"
    public function markAllAsRead()
    {
        $userId = session('user_id') ?? auth()->id();
        Notification::where('user_id', $userId)->update(['is_read' => true]);

        return back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }
}