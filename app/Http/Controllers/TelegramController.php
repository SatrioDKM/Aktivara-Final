<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    // STEP 1: Generate Token & Link
    // STEP 1: Generate Token & Link
    public function connect()
    {
        $user = Auth::user();
        $token = Str::random(32);
        
        // Simpan token verifikasi ke user
        $user->update(['telegram_verification_token' => $token]);

        // Ambil Username Bot dari ENV (Fallback ke 'aktivaraBot' jika env kosong)
        $botUsername = env('TELEGRAM_BOT_USERNAME', 'aktivaraBot'); 
        
        // Buat URL Deep Link Telegram
        // https://t.me/MyBot?start=v3r1f1c4t10n_t0k3n
        $botUrl = "https://t.me/{$botUsername}?start={$token}";

        // Kirim URL ke session flash agar bisa ditampilkan di view
        return back()->with('telegram_connect_url', $botUrl);
    }

    // STEP 2: Cek API Telegram (Manual Verification)
    public function verify()
    {
        $user = Auth::user();
        $token = $user->telegram_verification_token;

        if (!$token) {
            return back()->with('error', 'Tidak ada sesi koneksi yang aktif. Silakan klik tombol Hubungkan lagi.');
        }

        // Ambil Token Bot dari ENV
        $botToken = env('TELEGRAM_BOT_TOKEN');
        
        // Tembak API getUpdates untuk mencari pesan dari user ini
        // Limit 100 pesan terakhir untuk efisiensi
        // Hapus offset=-100 agar lebih aman menangkap update
        $url = "https://api.telegram.org/bot{$botToken}/getUpdates?limit=100";
        
        try {
            $response = Http::get($url);
            Log::info('[Telegram Connect] URL: ' . $url);
            Log::info('[Telegram Connect] Response: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('[Telegram Connect] Connection Failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghubungi Server Telegram. Cek koneksi internet Anda.');
        }
        
        if ($response->successful()) {
            $result = $response->json();
            
            // Cek apakah ada konflik Webhook
            if (isset($result['error_code']) && $result['error_code'] === 409) {
                 // Hapus Webhook otomatis
                 Http::post("https://api.telegram.org/bot{$botToken}/deleteWebhook");
                 Log::info('[Telegram Connect] Webhook deleted automatically due to conflict.');
                 return back()->with('error', 'Terdeteksi konflik Webhook. Sistem telah memperbaikinya. Silakan KLIK tombol verifikasi 1 KALI LAGI.');
            }

            $updates = $result['result'] ?? [];
            
            foreach ($updates as $update) {
                // Cek apakah pesan berisi command /start <TOKEN_USER>
                if (isset($update['message']['text']) && str_contains($update['message']['text'], $token)) {
                    
                    $chatId = $update['message']['chat']['id'];
                    $username = $update['message']['from']['username'] ?? 'Tanpa Username';
                    
                    // Simpan Chat ID & Hapus Token Sementara
                    $user->update([
                        'telegram_chat_id' => $chatId,
                        'telegram_verification_token' => null
                    ]);

                    // Kirim pesan konfirmasi balik ke Telegram
                    Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                        'chat_id' => $chatId,
                        'text' => "✅ Halo {$user->name}! Akun Aktivara Anda berhasil terhubung."
                    ]);

                    return back()->with('success', "Berhasil terhubung dengan Telegram! (Chat ID: {$chatId})");
                }
            }
        } else {
            Log::error("Gagal koneksi ke Telegram API: " . $response->body());
        }

        return back()->with('error', 'Pesan konfirmasi belum ditemukan. Pastikan Anda sudah mengklik tombol START di bot Telegram.');
    }
    
    // STEP 3: Putuskan Koneksi
    public function disconnect()
    {
        Auth::user()->update([
            'telegram_chat_id' => null,
            'telegram_verification_token' => null
        ]);
        
        return back()->with('success', 'Koneksi Telegram berhasil diputus.');
    }
}
