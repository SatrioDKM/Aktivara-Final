<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; // Pastikan ini di-import
use Illuminate\Routing\Controller; // Pastikan ini di-import
use Illuminate\Support\Facades\Auth; // Pastikan ini di-import

class NotificationController extends Controller
{
    public function viewPage()
    {
        $user = Auth::user();
        $unreadNotifications = $user->unreadNotifications()->paginate(10);
        $readNotifications = $user->readNotifications()->paginate(10);

        return view('notifications.index', compact('unreadNotifications', 'readNotifications'));
    }

    /**
     * Mengambil notifikasi (belum dibaca & sudah dibaca).
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        return response()->json([
            'unread' => $user->unreadNotifications()->limit(10)->get(),
            'read' => $user->readNotifications()->limit(5)->get(),
        ]);
    }

    /**
     * Menandai SEMUA notifikasi yang belum dibaca sebagai sudah dibaca.
     * Nama method diganti agar lebih jelas.
     */
    public function markAllAsRead(): JsonResponse
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);
        return response()->json(['message' => 'Semua notifikasi ditandai terbaca.']);
    }

    /**
     * --- METHOD BARU DITAMBAHKAN DI SINI ---
     * Menandai SATU notifikasi spesifik sebagai sudah dibaca.
     */
    public function markOneAsRead(Request $request): JsonResponse
    {
        $request->validate(['id' => 'required|string']); // Validasi ID notifikasi

        $notification = Auth::user()
            ->unreadNotifications()
            ->where('id', $request->input('id'))
            ->first();

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['message' => 'Notifikasi ditandai terbaca.']);
        }

        return response()->json(['message' => 'Notifikasi tidak ditemukan atau sudah dibaca.'], 404);
    }
    // --- AKHIR METHOD BARU ---

}
