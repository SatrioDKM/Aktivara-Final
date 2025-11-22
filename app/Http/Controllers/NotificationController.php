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
    public function markAllAsRead(Request $request)
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Semua notifikasi ditandai terbaca.']);
        }

        return back()->with('success', 'Semua notifikasi telah ditandai sebagai sudah dibaca.');
    }

    /**
     * --- METHOD BARU DITAMBAHKAN DI SINI ---
     * Menandai SATU notifikasi spesifik sebagai sudah dibaca.
     */
    public function markOneAsRead(Request $request, $id = null)
    {
        // Support ID dari URL parameter atau Request body
        $notificationId = $id ?? $request->input('id');

        if (!$notificationId) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'ID notifikasi diperlukan.'], 422);
            }
            return back()->with('error', 'Terjadi kesalahan sistem.');
        }

        $notification = Auth::user()
            ->unreadNotifications()
            ->where('id', $notificationId)
            ->first();

        if ($notification) {
            $notification->markAsRead();
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Notifikasi ditandai terbaca.']);
        }

        return back()->with('success', 'Notifikasi ditandai terbaca.');
    }
    // --- AKHIR METHOD BARU ---

}
