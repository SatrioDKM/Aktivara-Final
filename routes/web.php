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

Route::prefix('lapor-keluhan')->name('guest.complaint.')->group(function () {
    Route::get('/', [GuestComplaintController::class, 'create'])->name('create');
    Route::post('/', [GuestComplaintController::class, 'store'])->name('store');
});


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
    Route::middleware(['role:SA00,MG00,WH01,WH02'])->prefix('packing-lists')->name('packing_lists.')->group(function () {
        Route::get('/', [PackingListController::class, 'viewPage'])->name('index');
        Route::get('/{id}/pdf', [PackingListController::class, 'exportPdf'])->name('pdf')->where('id', '[0-9]+');
    });

    // --- Rute untuk Manajemen Stok (Warehouse, Admin, Manager) ---
    Route::middleware(['role:SA00,MG00,WH01,WH02'])->prefix('stock-management')->name('stock.')->group(function () {
        Route::get('/', [StockManagementController::class, 'viewPage'])->name('index');
    });


    // --- Rute Leader, Manager, & Admin ---

    Route::middleware(['role:SA00,MG00,HK01,TK01,SC01,PK01,WH01'])->prefix('complaints')->name('complaints.')->group(function () {
        Route::get('/', [ComplaintController::class, 'viewPage'])->name('index');
        Route::get('/create', [ComplaintController::class, 'create'])->name('create');
        Route::get('/{id}', [ComplaintController::class, 'showPage'])->name('show')->where('id', '[0-9]+');
    });

    Route::middleware(['role:SA00,MG00,HK01,TK01,SC01,PK01'])->group(function () {
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

        Route::prefix('floors')->name('floors.')->group(function () {
            Route::get('/', [FloorController::class, 'viewPage'])->name('index');
            Route::get('/create', [FloorController::class, 'create'])->name('create');
            Route::get('/{id}', [FloorController::class, 'showPage'])->name('show')->where('id', '[0-9]+');
            Route::get('/{id}/edit', [FloorController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
        });

        Route::prefix('rooms')->name('rooms.')->group(function () {
            Route::get('/', [RoomController::class, 'viewPage'])->name('index');
            Route::get('/create', [RoomController::class, 'create'])->name('create');
            Route::get('/{id}', [RoomController::class, 'showPage'])->name('show')->where('id', '[0-9]+');
            Route::get('/{id}/edit', [RoomController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
        });

        Route::prefix('task-types')->name('task_types.')->group(function () {
            Route::get('/', [TaskTypeController::class, 'viewPage'])->name('index');
            Route::get('/create', [TaskTypeController::class, 'create'])->name('create');
            Route::get('/{id}', [TaskTypeController::class, 'showPage'])->name('show')->where('id', '[0-9]+');
            Route::get('/{id}/edit', [TaskTypeController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
        });

        Route::prefix('assets')->name('assets.')->group(function () {
            Route::get('/', [AssetController::class, 'viewPage'])->name('index');
            Route::get('/create', [AssetController::class, 'create'])->name('create');
            Route::get('/{id}', [AssetController::class, 'showPage'])->name('show')->where('id', '[0-9]+');
            Route::get('/{id}/edit', [AssetController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
        });

        Route::prefix('maintenances')->name('maintenances.')->group(function () {
            Route::get('/', [AssetMaintenanceController::class, 'viewPage'])->name('index');
            Route::get('/create', [AssetMaintenanceController::class, 'create'])->name('create');
            Route::get('/{id}', [AssetMaintenanceController::class, 'showPage'])->name('show')->where('id', '[0-9]+');
            Route::get('/{id}/edit', [AssetMaintenanceController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
        });
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
        Route::middleware(['role:HK02,TK02,SC02,PK02,WH02'])->group(function () {
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
