<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // Gunakan Request class
use Illuminate\Http\JsonResponse; // Gunakan JsonResponse untuk return type
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Mengambil notifikasi (read & unread) untuk pengguna yang sedang login.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user(); // Ambil user dari request

        return response()->json([
            'read' => $user->readNotifications()->limit(10)->get(), // Batasi notifikasi yang sudah dibaca
            'unread' => $user->unreadNotifications,
        ]);
    }

    /**
     * Menandai semua notifikasi yang belum dibaca sebagai sudah dibaca.
     */
    public function markAsRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['message' => 'Semua notifikasi ditandai telah dibaca.']);
    }
}
