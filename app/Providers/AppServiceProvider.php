<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Mengatur locale Carbon ke Bahasa Indonesia secara global
        // Ini akan mempengaruhi format tanggal seperti created_at->diffForHumans()
        Carbon::setLocale('id');

        // Blade directive untuk format mata uang Rupiah
        // Penggunaan di Blade: @rupiah(12345.67) -> akan menampilkan "Rp 12.346"
        Blade::directive('rupiah', function ($expression) {
            return "<?php echo 'Rp ' . number_format($expression, 0, ',', '.'); ?>";
        });

        // Blade directive untuk format tanggal Indonesia
        // Penggunaan di Blade: @tanggal($model->created_at) -> akan menampilkan "24 September 2025"
        Blade::directive('tanggal', function ($expression) {
            return "<?php echo ($expression) ? \Carbon\Carbon::parse($expression)->translatedFormat('d F Y') : ''; ?>";
        });

        // Directive untuk memeriksa jika user memiliki SALAH SATU dari peran yang diberikan.
        // Contoh: @role('SA00', 'MG00')
        Blade::if('role', function (...$roles) {
            if (!Auth::check()) {
                return false;
            }
            return in_array(Auth::user()->role_id, $roles);
        });
    }
}
