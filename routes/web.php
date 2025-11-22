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
use App\Http\Controllers\NotificationController; // Add this line

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
// NOTE: Rute ini dapat diakses siapa saja tanpa login
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::prefix('lapor-keluhan')->name('guest.complaint.')->group(function () {
    Route::get('/', [GuestComplaintController::class, 'create'])->name('create');
});


// === Rute Autentikasi ===
// NOTE: Semua rute di dalam grup ini WAJIB login dan email terverifikasi
Route::middleware(['auth', 'verified'])->group(function () {

    // --- Rute Umum ---
    // NOTE: Rute dasar setelah login
    Route::get('/dashboard', [DashboardController::class, 'viewPage'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/signature', [App\Http\Controllers\ProfileController::class, 'updateSignature'])
        ->name('profile.signature.update');

    // --- Fitur Spesifik dengan Hak Akses ---

    // NOTE: Rute untuk fitur Gudang (Packing List, Stok, Riwayat Aset)
    Route::middleware(['role:SA00,MG00,WH01,WH02'])->group(function () {
        Route::get('packing-lists/{id}/pdf', [PackingListController::class, 'exportPdf'])->name('packing_lists.pdf')->where('id', '[0-9]+');
        Route::get('packing-lists', [PackingListController::class, 'viewPage'])->name('packing_lists.index');
        Route::get('stock-management', [StockManagementController::class, 'viewPage'])->name('stock.index');
        Route::get('asset-history', [AssetHistoryController::class, 'viewPage'])->name('asset_history.index');
    });

    // NOTE: Rute untuk melihat dan membuat Keluhan (Internal)
    Route::middleware(['role:SA00,MG00,HK01,TK01,SC01,PK01,WH01'])->group(function () {
        // Rute untuk menampilkan halaman daftar laporan (menunjuk ke viewPage)
        Route::get('complaints', [ComplaintController::class, 'viewPage'])->name('complaints.index');
        // Rute untuk menampilkan halaman form tambah
        Route::get('complaints/create', [ComplaintController::class, 'create'])->name('complaints.create');
        // Rute untuk menampilkan halaman detail laporan (menunjuk ke showPage)
        Route::get('complaints/{id}', [ComplaintController::class, 'showPage'])->name('complaints.show')->where('id', '[0-9]+');
    });

    // NOTE: Rute untuk monitoring dan ekspor data (Admin/Manager/Leader)
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
    // NOTE: Grup rute ini hanya untuk Superadmin, Manager, dan Admin Gudang
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
            Route::get('category/{categoryId}', [AssetController::class, 'showByCategory'])->name('master.assets.assets.by_category');
        });

        // --- PERBAIKAN: Rute Halaman Web Asset Categories (Manual) ---
        // NOTE: Menggantikan Route::resource untuk mematuhi aturan rute manual
        Route::prefix('asset_categories')->name('asset_categories.')->group(function () {
            Route::get('/', [AssetCategoryController::class, 'index'])->name('index');
            Route::get('/create', [AssetCategoryController::class, 'create'])->name('create');
            Route::post('/', [AssetCategoryController::class, 'store'])->name('store');
            Route::get('/{asset_category}', [AssetCategoryController::class, 'show'])->name('show')->where('asset_category', '[0-9]+');
            Route::get('/{asset_category}/edit', [AssetCategoryController::class, 'edit'])->name('edit')->where('asset_category', '[0-9]+');
            Route::put('/{asset_category}', [AssetCategoryController::class, 'update'])->name('update')->where('asset_category', '[0-9]+');
            Route::delete('/{asset_category}', [AssetCategoryController::class, 'destroy'])->name('destroy')->where('asset_category', '[0-9]+');
        });
        // --- AKHIR PERBAIKAN ---

        // --- Rute untuk Maintenances ---
        Route::prefix('maintenances')->name('maintenances.')->group(function () {
            Route::get('/', [AssetMaintenanceController::class, 'viewPage'])->name('index');
            Route::get('/create', [AssetMaintenanceController::class, 'create'])->name('create');
            Route::get('/{id}', [AssetMaintenanceController::class, 'showPage'])->name('show')->where('id', '[0-9]+');
            Route::get('/{id}/edit', [AssetMaintenanceController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
        });
    });

    // --- Rute Khusus Superadmin ---
    // NOTE: Rute ini hanya bisa diakses oleh Superadmin (SA00)
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
        // NOTE: Halaman Buat Tugas (Bisa diakses Leader & Staff)
        Route::get('/create', [TaskWorkflowController::class, 'createPage'])->middleware('role:SA00,MG00,HK01,TK01,SC01,PK01,HK02,TK02,SC02,PK02')->name('create');
        // NOTE: Halaman Review Laporan (Hanya Leader/Manager/SA)
        Route::get('/review', [TaskWorkflowController::class, 'reviewPage'])->middleware('role:SA00,MG00,HK01,TK01,SC01,PK01')->name('review_list');

        // NOTE: Rute Khusus Staff
        Route::middleware(['role:HK02,TK02,SC02,PK02,WH02'])->group(function () {
            Route::get('/available', [TaskWorkflowController::class, 'availablePage'])->name('available');
            Route::get('/my-history', [TaskWorkflowController::class, 'showMyHistoryPage'])->name('my_history');
            Route::get('/my-tasks', [TaskWorkflowController::class, 'myTasksPage'])->name('my_tasks');
        });

        // NOTE: Halaman riwayat tugas yang sudah selesai (untuk semua)
        Route::get('/completed-history', [TaskWorkflowController::class, 'completedHistoryPage'])->name('completed_history');

        // NOTE: Halaman detail tugas (untuk semua)
        Route::get('/{taskId}', [TaskWorkflowController::class, 'showPage'])->name('show')->where('taskId', '[0-9]+');
        
        // NOTE: Smart Redirect untuk notifikasi
        Route::get('/{id}/check', [TaskWorkflowController::class, 'checkAndRedirect'])->name('check');
    });

    // --- Rute Notifikasi ---
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'viewPage'])->name('index');
        Route::patch('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('markAllAsRead');
        Route::patch('/mark-one-as-read', [NotificationController::class, 'markOneAsRead'])->name('markAsRead');
    });
});

require __DIR__ . '/auth.php';
