<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Array dari peran yang diizinkan
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Pastikan pengguna sudah login terlebih dahulu.
        if (!Auth::check()) {
            // Jika belum login, arahkan ke halaman login.
            return redirect('login');
        }

        // 2. Dapatkan peran dari pengguna yang sedang login.
        // Pastikan nama kolom di model User dan tabel users adalah 'role_id'.
        $userRole = Auth::user()->role_id;

        // 3. Periksa apakah peran pengguna ada di dalam daftar peran yang diizinkan.
        foreach ($roles as $role) {
            if ($userRole == $role) {
                // Jika peran cocok, izinkan request untuk melanjutkan.
                return $next($request);
            }
        }

        // 4. Jika setelah dicek semua tidak ada peran yang cocok,
        // hentikan request dan tampilkan halaman error 403 (Forbidden).
        abort(403, 'AKSES DITOLAK. ANDA TIDAK MEMILIKI HAK AKSES YANG SESUAI.');
    }
}
