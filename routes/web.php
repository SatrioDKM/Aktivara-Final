<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\TaskTypeController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PackingListController;
use App\Http\Controllers\TaskWorkflowController;
use App\Http\Controllers\GuestComplaintController;
use App\Http\Controllers\StockManagementController;
use App\Http\Controllers\AssetMaintenanceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ===================================================================
// RUTE PUBLIK (Dapat diakses tanpa login)
// ===================================================================
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/lapor-keluhan', [GuestComplaintController::class, 'create'])->name('guest.complaint.create');
Route::post('/lapor-keluhan', [GuestComplaintController::class, 'store'])->name('guest.complaint.store');


// ===================================================================
// RUTE YANG MEMERLUKAN AUTENTIKASI
// ===================================================================
Route::middleware(['auth', 'verified'])->group(function () {

    // --- Rute Umum (Semua Role Setelah Login) ---
    Route::get('/dashboard', [DashboardController::class, 'viewPage'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- RUTE UNTUK BARANG KELUAR / PACKING LIST ---
    Route::get('/packing-lists', [PackingListController::class, 'viewPage'])->name('packing_lists.index');
    Route::post('/packing-lists', [PackingListController::class, 'store'])->name('packing_lists.store');
    Route::get('/packing-lists/{id}/pdf', [PackingListController::class, 'exportPdf'])->name('packing_lists.pdf')->where('id', '[0-9]+');

    // --- Rute untuk Manajemen Stok (Warehouse, Admin, Manager) ---
    Route::get('/stock-management', [StockManagementController::class, 'viewPage'])
        ->middleware('role:SA00,MG00,WH01,WH02')
        ->name('stock.index');


    // --- Rute Leader, Manager, & Admin ---
    Route::middleware(['role:SA00,MG00,HK01,TK01,SC01,PK01'])->group(function () {
        Route::get('/complaints', [ComplaintController::class, 'viewPage'])->name('complaints.index');
        Route::get('/tasks/monitoring', [TaskWorkflowController::class, 'monitoringPage'])->name('tasks.monitoring');
        Route::get('/history/tasks', [TaskWorkflowController::class, 'historyPage'])->name('history.tasks');

        // Grup untuk Halaman Ekspor
        Route::prefix('export')->name('export.')->group(function () {
            Route::get('/', [ExportController::class, 'viewPage'])->name('index');
            Route::get('/assets', [ExportController::class, 'exportAssets'])->name('assets');
            Route::get('/daily-reports', [ExportController::class, 'exportDailyReports'])->name('daily_reports');
        });
    });

    // --- Rute Data Master (Hanya Admin & Manager) ---
    Route::middleware(['role:SA00,MG00'])->prefix('master')->name('master.')->group(function () {
        Route::prefix('buildings')->name('buildings.')->group(function () {
            Route::get('/', [BuildingController::class, 'viewPage'])->name('index');
            Route::get('/create', [BuildingController::class, 'create'])->name('create');
            Route::get('/{id}', [BuildingController::class, 'showPage'])->name('show')->where('id', '[0-9]+');
            Route::get('/{id}/edit', [BuildingController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
        });

        // Rute lain tetap sama
        Route::get('/floors', [FloorController::class, 'viewPage'])->name('floors.index');
        Route::get('/rooms', [RoomController::class, 'viewPage'])->name('rooms.index');
        Route::get('/task-types', [TaskTypeController::class, 'viewPage'])->name('task_types.index');
        Route::get('/assets', [AssetController::class, 'viewPage'])->name('assets.index');
        Route::get('/maintenances', [AssetMaintenanceController::class, 'viewPage'])->name('maintenances.index');
    });

    // --- Rute Khusus Superadmin ---
    Route::middleware(['role:SA00'])->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'viewPage'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::get('/{id}', [UserController::class, 'show'])->name('show')->where('id', '[0-9]+');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
    });


    // --- GRUP ROUTE UNTUK ALUR KERJA TUGAS ---
    Route::prefix('tasks')->name('tasks.')->group(function () {
        // Halaman Buat Tugas (Bisa diakses Staff juga)
        Route::get('/create', [TaskWorkflowController::class, 'createPage'])->middleware('role:SA00,MG00,HK01,TK01,SC01,PK01,HK02,TK02,SC02,PK02')->name('create');
        // Halaman Review Laporan
        Route::get('/review', [TaskWorkflowController::class, 'reviewPage'])->middleware('role:SA00,MG00,HK01,TK01,SC01,PK01')->name('review_list');

        // Rute Khusus Staff
        Route::middleware(['role:HK02,TK02,SC02,PK02'])->group(function () {
            Route::get('/available', [TaskWorkflowController::class, 'availablePage'])->name('available');
            Route::get('/my-history', [TaskWorkflowController::class, 'showMyHistoryPage'])->name('my_history');
            Route::get('/my-tasks', [TaskWorkflowController::class, 'myTasksPage'])->name('my_tasks'); // Rute lama
            Route::get('/completed-history', [TaskWorkflowController::class, 'completedHistoryPage'])->name('completed_history'); // Rute lama
        });

        // Rute Detail Tugas (ditempatkan di akhir)
        Route::get('/{id}', [TaskWorkflowController::class, 'showPage'])->name('show')->where('id', '[0-9]+');
    });
});

require __DIR__ . '/auth.php';
