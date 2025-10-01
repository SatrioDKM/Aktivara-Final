<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetMaintenanceController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\GuestComplaintController;
use App\Http\Controllers\PackingListController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\StockManagementController;
use App\Http\Controllers\TaskTypeController;
use App\Http\Controllers\TaskWorkflowController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Direvisi menggunakan Route::resource untuk menyederhanakan
| definisi rute halaman web dan mengikuti konvensi Laravel.
|
*/

// === Rute Publik ===
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::prefix('lapor-keluhan')->name('guest.complaint.')->group(function () {
    Route::get('/', [GuestComplaintController::class, 'create'])->name('create');
});


// === Rute Autentikasi ===
Route::middleware(['auth', 'verified'])->group(function () {

    // --- Rute Umum ---
    Route::get('/dashboard', [DashboardController::class, 'viewPage'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Fitur Spesifik dengan Hak Akses ---

    Route::middleware(['role:SA00,MG00,WH01,WH02'])->group(function () {
        Route::get('packing-lists/{id}/pdf', [PackingListController::class, 'exportPdf'])->name('packing_lists.pdf')->where('id', '[0-9]+');
        Route::get('packing-lists', [PackingListController::class, 'viewPage'])->name('packing_lists.index');
        Route::get('stock-management', [StockManagementController::class, 'viewPage'])->name('stock.index');
    });

    Route::middleware(['role:SA00,MG00,HK01,TK01,SC01,PK01,WH01'])->group(function () {
        Route::get('complaints/create', [ComplaintController::class, 'create'])->name('complaints.create');
        Route::resource('complaints', ComplaintController::class)->only(['index', 'show']);
    });

    Route::middleware(['role:SA00,MG00,HK01,TK01,SC01,PK01'])->group(function () {
        Route::get('/tasks/monitoring', [TaskWorkflowController::class, 'monitoringPage'])->name('tasks.monitoring');
        Route::get('/history/tasks', [TaskWorkflowController::class, 'historyPage'])->name('history.tasks');

        Route::prefix('export')->name('export.')->group(function () {
            Route::get('/', [ExportController::class, 'viewPage'])->name('index');
            Route::get('/assets', [ExportController::class, 'exportAssets'])->name('assets');
            Route::get('/daily-reports', [ExportController::class, 'exportDailyReports'])->name('daily_reports');
        });
    });

    // --- Rute Data Master (Hanya Admin & Manager) ---
    Route::middleware(['role:SA00,MG00'])->prefix('master')->name('master.')->group(function () {
        // Menggunakan Route::resource untuk semua data master.
        // Controller Anda menggunakan 'viewPage' untuk index dan 'showPage' untuk show.
        // Jika nama method di controller diubah ke standar (index, show), .names() tidak perlu.
        Route::resource('buildings', BuildingController::class)->only(['index', 'create', 'show', 'edit'])->names(['index' => 'buildings.index', 'create' => 'buildings.create', 'show' => 'buildings.show', 'edit' => 'buildings.edit']);
        Route::resource('floors', FloorController::class)->only(['index', 'create', 'show', 'edit'])->names(['index' => 'floors.index', 'create' => 'floors.create', 'show' => 'floors.show', 'edit' => 'floors.edit']);
        Route::resource('rooms', RoomController::class)->only(['index', 'create', 'show', 'edit'])->names(['index' => 'rooms.index', 'create' => 'rooms.create', 'show' => 'rooms.show', 'edit' => 'rooms.edit']);
        Route::resource('task-types', TaskTypeController::class)->only(['index', 'create', 'show', 'edit'])->names(['index' => 'task_types.index', 'create' => 'task_types.create', 'show' => 'task_types.show', 'edit' => 'task_types.edit']);
        Route::resource('assets', AssetController::class)->only(['index', 'create', 'show', 'edit'])->names(['index' => 'assets.index', 'create' => 'assets.create', 'show' => 'assets.show', 'edit' => 'assets.edit']);
        Route::resource('maintenances', AssetMaintenanceController::class)->only(['index', 'create', 'show', 'edit'])->names(['index' => 'maintenances.index', 'create' => 'maintenances.create', 'show' => 'maintenances.show', 'edit' => 'maintenances.edit']);
    });

    // --- Rute Khusus Superadmin ---
    Route::resource('users', UserController::class)->middleware('role:SA00')->only(['index', 'create', 'show', 'edit']);

    // --- GRUP ROUTE UNTUK ALUR KERJA TUGAS ---
    Route::prefix('tasks')->name('tasks.')->group(function () {
        // Halaman Buat Tugas (Bisa diakses Staff juga)
        Route::get('/create', [TaskWorkflowController::class, 'createPage'])->middleware('role:SA00,MG00,HK01,TK01,SC01,PK01,HK02,TK02,SC02,PK02')->name('create');
        // Halaman Review Laporan
        Route::get('/review', [TaskWorkflowController::class, 'reviewPage'])->middleware('role:SA00,MG00,HK01,TK01,SC01,PK01')->name('review_list');

        // Rute Khusus Staff
        Route::middleware(['role:HK02,TK02,SC02,PK02,WH02'])->group(function () {
            Route::get('/available', [TaskWorkflowController::class, 'availablePage'])->name('available');
            Route::get('/my-history', [TaskWorkflowController::class, 'showMyHistoryPage'])->name('my_history');
            Route::get('/my-tasks', [TaskWorkflowController::class, 'myTasksPage'])->name('my_tasks');
        });

        // ===================================================================
        // --- PERBAIKAN UTAMA DI SINI ---
        // Pastikan parameter rute adalah `{taskId}` agar cocok dengan
        // variabel `$taskId` di metode showPage(taskId) pada controller.
        // ===================================================================
        Route::get('/{taskId}', [TaskWorkflowController::class, 'showPage'])->name('show')->where('taskId', '[0-9]+');
    });
});

require __DIR__ . '/auth.php';
