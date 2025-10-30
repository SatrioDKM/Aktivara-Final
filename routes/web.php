<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\TaskTypeController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PackingListController;
use App\Http\Controllers\AssetHistoryController;
use App\Http\Controllers\TaskWorkflowController;
use App\Http\Controllers\GuestComplaintController;
use App\Http\Controllers\StockManagementController;
use App\Http\Controllers\AssetMaintenanceController;



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
    Route::patch('/profile/signature', [App\Http\Controllers\ProfileController::class, 'updateSignature'])
        ->name('profile.signature.update');

    // --- Fitur Spesifik dengan Hak Akses ---

    Route::middleware(['role:SA00,MG00,WH01,WH02'])->group(function () {
        Route::get('packing-lists/{id}/pdf', [PackingListController::class, 'exportPdf'])->name('packing_lists.pdf')->where('id', '[0-9]+');
        Route::get('packing-lists', [PackingListController::class, 'viewPage'])->name('packing_lists.index');
        Route::get('stock-management', [StockManagementController::class, 'viewPage'])->name('stock.index');
        Route::get('asset-history', [AssetHistoryController::class, 'viewPage'])->name('asset_history.index');
    });

    Route::middleware(['role:SA00,MG00,HK01,TK01,SC01,PK01,WH01'])->group(function () {
        // Rute untuk menampilkan halaman daftar laporan (menunjuk ke viewPage)
        Route::get('complaints', [ComplaintController::class, 'viewPage'])->name('complaints.index');
        // Rute untuk menampilkan halaman form tambah
        Route::get('complaints/create', [ComplaintController::class, 'create'])->name('complaints.create');
        // Rute untuk menampilkan halaman detail laporan (menunjuk ke showPage)
        Route::get('complaints/{id}', [ComplaintController::class, 'showPage'])->name('complaints.show')->where('id', '[0-9]+');
    });

    Route::middleware(['role:SA00,MG00,HK01,TK01,SC01,PK01,WH01'])->group(function () {
        Route::get('/tasks/monitoring', [TaskWorkflowController::class, 'monitoringPage'])->name('tasks.monitoring');
        Route::get('/history/tasks', [TaskWorkflowController::class, 'historyPage'])->name('history.tasks');

        Route::prefix('export')->name('export.')->group(function () {
            // Rute untuk menampilkan halaman index
            Route::get('/', [ExportController::class, 'viewPage'])->name('index');
            // Rute untuk download file aset
            Route::get('/assets', [ExportController::class, 'exportAssets'])->name('assets');
            // Rute untuk download riwayat tugas (nama diperbarui)
            Route::get('/task-history', [ExportController::class, 'exportTaskHistory'])->name('task_history');
        });
    });

    // --- Rute Data Master (Hanya Admin & Manager) ---
    Route::middleware(['role:SA00,MG00,WH01'])->prefix('master')->name('master.')->group(function () {

        // --- Rute untuk Buildings ---
        Route::prefix('buildings')->name('buildings.')->group(function () {
            Route::get('/', [BuildingController::class, 'viewPage'])->name('index');
            Route::get('/create', [BuildingController::class, 'create'])->name('create');
            Route::get('/{id}', [BuildingController::class, 'showPage'])->name('show')->where('id', '[0-9]+');
            Route::get('/{id}/edit', [BuildingController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
        });

        // --- Rute untuk Floors ---
        Route::prefix('floors')->name('floors.')->group(function () {
            Route::get('/', [FloorController::class, 'viewPage'])->name('index');
            Route::get('/create', [FloorController::class, 'create'])->name('create');
            Route::get('/{id}', [FloorController::class, 'showPage'])->name('show')->where('id', '[0-9]+');
            Route::get('/{id}/edit', [FloorController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
        });

        // --- Rute untuk Rooms ---
        Route::prefix('rooms')->name('rooms.')->group(function () {
            Route::get('/', [RoomController::class, 'viewPage'])->name('index');
            Route::get('/create', [RoomController::class, 'create'])->name('create');
            Route::get('/{id}', [RoomController::class, 'showPage'])->name('show')->where('id', '[0-9]+');
            Route::get('/{id}/edit', [RoomController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
        });

        // --- Rute untuk Task Types ---
        Route::prefix('task-types')->name('task_types.')->group(function () {
            Route::get('/', [TaskTypeController::class, 'viewPage'])->name('index');
            Route::get('/create', [TaskTypeController::class, 'create'])->name('create');
            Route::get('/{id}', [TaskTypeController::class, 'showPage'])->name('show')->where('id', '[0-9]+');
            Route::get('/{id}/edit', [TaskTypeController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
        });

        // --- Rute untuk Assets ---
        Route::prefix('assets')->name('assets.')->group(function () {
            Route::get('/', [AssetController::class, 'viewPage'])->name('index');
            Route::get('/create', [AssetController::class, 'create'])->name('create');
            Route::get('/{id}', [AssetController::class, 'showPage'])->name('show')->where('id', '[0-9]+');
            Route::get('/{id}/edit', [AssetController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
        });

        // --- Rute Halaman Web Asset Categories ---
        Route::resource('asset_categories', AssetCategoryController::class);

        // --- Rute untuk Maintenances ---
        Route::prefix('maintenances')->name('maintenances.')->group(function () {
            Route::get('/', [AssetMaintenanceController::class, 'viewPage'])->name('index');
            Route::get('/create', [AssetMaintenanceController::class, 'create'])->name('create');
            Route::get('/{id}', [AssetMaintenanceController::class, 'showPage'])->name('show')->where('id', '[0-9]+');
            Route::get('/{id}/edit', [AssetMaintenanceController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
        });
    });

    // --- Rute Khusus Superadmin ---
    Route::middleware(['role:SA00'])->prefix('users')->name('users.')->group(function () {
        // Rute untuk menampilkan halaman daftar pengguna (menunjuk ke viewPage)
        Route::get('/', [UserController::class, 'viewPage'])->name('index');
        // Rute untuk menampilkan halaman form tambah
        Route::get('/create', [UserController::class, 'create'])->name('create');
        // Rute untuk menampilkan halaman detail
        Route::get('/{id}', [UserController::class, 'show'])->name('show')->where('id', '[0-9]+');
        // Rute untuk menampilkan halaman form edit
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
            Route::get('/my-tasks', [TaskWorkflowController::class, 'myTasksPage'])->name('my_tasks');
        });

        Route::get('/completed-history', [TaskWorkflowController::class, 'completedHistoryPage'])->name('completed_history');

        Route::get('/{taskId}', [TaskWorkflowController::class, 'showPage'])->name('show')->where('taskId', '[0-9]+');
    });
});

require __DIR__ . '/auth.php';
