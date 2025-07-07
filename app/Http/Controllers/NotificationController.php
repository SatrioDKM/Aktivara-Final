<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Mengambil notifikasi pengguna yang login.
     */
    public function index()
    {
        $user = Auth::user();

        return response()->json([
            'read' => $user->readNotifications,
            'unread' => $user->unreadNotifications,
        ]);
    }

    /**
     * Menandai semua notifikasi yang belum dibaca sebagai sudah dibaca.
     */
    public function markAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json(['message' => 'All notifications marked as read.']);
    }
}
