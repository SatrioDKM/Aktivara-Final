<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\TaskTypeController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PackingListController;
use App\Http\Controllers\AssetHistoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TaskWorkflowController;
use App\Http\Controllers\GuestComplaintController;
use App\Http\Controllers\StockManagementController;
use App\Http\Controllers\AssetMaintenanceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Direvisi untuk mematuhi standar rute manual,
| menghapus 'apiResource' dan mendefinisikan setiap endpoint
| secara eksplisit dengan 'where' constraint.
|
*/

// --- RUTE PUBLIK ---
// Rute ini tidak memerlukan login
Route::post('/guest-complaints', [GuestComplaintController::class, 'store'])->name('api.guest.complaint.store');

// --- RUTE TERAUTENTIKASI ---
// Semua rute di bawah ini memerlukan login (auth:sanctum)
// dan memiliki prefix nama 'api.'
Route::middleware(['auth:sanctum'])->name('api.')->group(function () {

    // === Endpoint Umum ===
    Route::get('/dashboard-stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.read.all');
    Route::post('/notifications/mark-one-read', [NotificationController::class, 'markOneAsRead'])->name('notifications.read.one');

    // === Resourceful Routes untuk Data Master (Manual) ===

    // --- Rute manual untuk Buildings ---
    Route::prefix('buildings')->name('buildings.')->group(function () {
        Route::get('/', [BuildingController::class, 'index'])->name('index');
        Route::post('/', [BuildingController::class, 'store'])->name('store');
        Route::get('/{id}', [BuildingController::class, 'show'])->where('id', '[0-9]+')->name('show');
        Route::put('/{id}', [BuildingController::class, 'update'])->where('id', '[0-9]+')->name('update');
        Route::delete('/{id}', [BuildingController::class, 'destroy'])->where('id', '[0-9]+')->name('destroy');
    });

    // --- Rute manual untuk Floors ---
    Route::prefix('floors')->name('floors.')->group(function () {
        Route::get('/', [FloorController::class, 'index'])->name('index'); // Untuk tabel index
        Route::get('/list', [FloorController::class, 'listAll'])->name('list'); // <-- RUTE UNTUK DROPDOWN
        Route::post('/', [FloorController::class, 'store'])->name('store');
        Route::get('/{id}', [FloorController::class, 'show'])->where('id', '[0-9]+')->name('show');
        Route::put('/{id}', [FloorController::class, 'update'])->where('id', '[0-9]+')->name('update');
        Route::delete('/{id}', [FloorController::class, 'destroy'])->where('id', '[0-9]+')->name('destroy');
    });

    // --- Rute manual untuk Rooms ---
    Route::prefix('rooms')->name('rooms.')->group(function () {
        Route::get('/', [RoomController::class, 'index'])->name('index'); // Untuk tabel index
        Route::get('/list', [RoomController::class, 'listAll'])->name('list'); // <-- RUTE UNTUK DROPDOWN
        Route::post('/', [RoomController::class, 'store'])->name('store');
        Route::get('/{id}', [RoomController::class, 'show'])->where('id', '[0-9]+')->name('show');
        Route::put('/{id}', [RoomController::class, 'update'])->where('id', '[0-9]+')->name('update');
        Route::delete('/{id}', [RoomController::class, 'destroy'])->where('id', '[0-9]+')->name('destroy');
    });

    // --- Rute manual untuk Assets ---
    Route::prefix('assets')->name('assets.')->group(function () {
        // Rute yang boleh diakses Warehouse, Manager, Admin, dan Staff (untuk klaim tugas)
        Route::middleware(['role:SA00,MG00,WH01,WH02,HK02,TK02,SC02'])->group(function () {
            Route::get('/', [AssetController::class, 'index'])->name('index'); // Read List
            Route::post('/', [AssetController::class, 'store'])->name('store'); // Create
            Route::get('/{id}', [AssetController::class, 'show'])->where('id', '[0-9]+')->name('show'); // Read Detail
            // NOTE: Menggunakan POST untuk update karena form mungkin berisi file (gambar)
            Route::post('/{id}', [AssetController::class, 'update'])->where('id', '[0-9]+')->name('update'); // Update
            Route::post('/{id}/stock-out', [AssetController::class, 'stockOut'])->name('stock_out')->where('id', '[0-9]+'); // Stock Out (jika Warehouse boleh)
            Route::get('/list-for-dropdown', [AssetController::class, 'listAllForDropdown'])->name('list_for_dropdown'); // New route for dropdowns
        });

        // Rute Delete (HANYA Manager & Admin)
        Route::delete('/{id}', [AssetController::class, 'destroy'])->where('id', '[0-9]+')->name('destroy')->middleware(['role:SA00,MG00']);
    });

    // --- Rute manual untuk Asset Categories ---
    // PERBAIKAN: Disesuaikan dengan standar Anda (nama, middleware, dan binding)
    Route::prefix('asset-categories')->name('asset_categories.')->group(function () {
        Route::get('/', [AssetCategoryController::class, 'apiIndex'])->name('index'); // Menjadi: api.asset_categories.index
        Route::post('/', [AssetCategoryController::class, 'apiStore'])->name('store'); // Menjadi: api.asset_categories.store
        Route::put('/{id}', [AssetCategoryController::class, 'apiUpdate'])->where('id', '[0-9]+')->name('update'); // Menjadi: api.asset_categories.update
        Route::delete('/{id}', [AssetCategoryController::class, 'apiDestroy'])->where('id', '[0-9]+')->name('destroy'); // Menjadi: api.asset_categories.destroy
    });

    // PERBAIKAN: Menghapus middleware berlebih dan memperbaiki nama
    Route::get('assets/by-category/{category}', [AssetController::class, 'apiShowByCategory'])
        ->name('assets.by_category'); // Menjadi: api.assets.by_category
    
    // Endpoint untuk mengambil grouping nama aset dalam kategori
    Route::get('assets/by-category/{category}/groups', [AssetController::class, 'getAssetNameGroups'])
        ->name('assets.name_groups'); // Menjadi: api.assets.name_groups

    // --- Rute manual untuk Asset Maintenances ---
    Route::prefix('maintenances')->name('maintenances.')->group(function () {
        Route::get('/', [AssetMaintenanceController::class, 'index'])->name('index');
        Route::post('/', [AssetMaintenanceController::class, 'store'])->name('store');
        Route::get('/{id}', [AssetMaintenanceController::class, 'show'])->where('id', '[0-9]+')->name('show');
        Route::put('/{id}', [AssetMaintenanceController::class, 'update'])->where('id', '[0-9]+')->name('update');
        Route::delete('/{id}', [AssetMaintenanceController::class, 'destroy'])->where('id', '[0-9]+')->name('destroy');
    });

    // --- Rute manual untuk Task Types ---
    Route::prefix('task-types')->name('task_types.')->group(function () {
        Route::get('/', [TaskTypeController::class, 'index'])->name('index');
        Route::post('/', [TaskTypeController::class, 'store'])->name('store');
        Route::get('/{id}', [TaskTypeController::class, 'show'])->where('id', '[0-9]+')->name('show');
        Route::put('/{id}', [TaskTypeController::class, 'update'])->where('id', '[0-9]+')->name('update');
        Route::delete('/{id}', [TaskTypeController::class, 'destroy'])->where('id', '[0-9]+')->name('destroy');
        Route::get('/by-department/{department_code}', [TaskTypeController::class, 'getByDepartment'])->name('by-department');
    });


    // === Endpoint dengan Hak Akses Spesifik ===

    // --- Rute manual untuk Users (Hanya Superadmin) ---
    Route::prefix('users')->name('users.')->middleware('role:SA00')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{id}', [UserController::class, 'show'])->where('id', '[0-9]+')->name('show');
        // NOTE: Menggunakan POST untuk update karena form mungkin berisi file (gambar)
        Route::post('/{id}', [UserController::class, 'update'])->where('id', '[0-9]+')->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->where('id', '[0-9]+')->name('destroy');
    });

    // Stock Management (Warehouse, Admin, Manager)
    Route::prefix('stock-management')->name('stock.')->middleware(['role:SA00,MG00,WH01,WH02'])->group(function () {
        Route::get('/', [StockManagementController::class, 'index'])->name('index');
        Route::put('/{id}', [StockManagementController::class, 'update'])->name('update')->where('id', '[0-9]+');
    });

    // Packing List (Warehouse, Admin, Manager)
    Route::prefix('packing-lists')->name('packing_lists.')->middleware(['role:SA00,MG00,WH01,WH02'])->group(function () {
        Route::get('/', [PackingListController::class, 'index'])->name('index');
        Route::post('/', [PackingListController::class, 'store'])->name('store');
        Route::get('/get-assets', [PackingListController::class, 'getAvailableAssets'])->name('get_assets');
    });

    // Complaints (Hanya Leader ke atas)
    Route::prefix('complaints')->name('complaints.')->middleware(['role:SA00,MG00,HK01,TK01,SC01,PK01,WH01'])->group(function () {
        Route::get('/', [ComplaintController::class, 'index'])->name('index');
        Route::post('/', [ComplaintController::class, 'store'])->name('store');
        Route::get('/{id}', [ComplaintController::class, 'show'])->where('id', '[0-9]+')->name('show');
        Route::delete('/{id}', [ComplaintController::class, 'destroy'])->where('id', '[0-9]+')->name('destroy');
        Route::post('/{id}/convert', [ComplaintController::class, 'convertToTask'])->name('convert')->where('id', '[0-9]+');
    });


    // === Endpoint untuk Alur Kerja Tugas (Task Workflow) ===
    Route::prefix('tasks')->name('tasks.')->group(function () {
        // Leader & Manager
        Route::middleware(['role:SA00,MG00,HK01,TK01,SC01'])->group(function () {
            Route::post('/', [TaskWorkflowController::class, 'store'])->name('store');
            Route::get('/review-list', [TaskWorkflowController::class, 'showReviewList'])->name('review_list_data');
            Route::post('/{id}/review', [TaskWorkflowController::class, 'submitReview'])->name('submit_review')->where('id', '[0-9]+');
            Route::get('/in-progress', [TaskWorkflowController::class, 'getInProgressTasks'])->name('in_progress_data');
            Route::get('/active', [TaskWorkflowController::class, 'getActiveTasks'])->name('active_data');
        });

        // Staff
        Route::middleware(['role:HK02,TK02,SC02'])->group(function () {
            Route::get('/available-list', [TaskWorkflowController::class, 'showAvailable'])->name('available_data');
            Route::post('/{id}/claim', [TaskWorkflowController::class, 'claimTask'])->name('claim')->where('id', '[0-9]+');
        });

        // Akses bersama & pribadi
        Route::get('/my-history', [TaskWorkflowController::class, 'getMyTaskHistory'])->name('my_history_data');
        Route::get('/history', [TaskWorkflowController::class, 'getTaskHistory'])->name('history_data');
        Route::get('/my-tasks-list', [TaskWorkflowController::class, 'myTasks'])->name('my_tasks_data');
        Route::get('/{id}', [TaskWorkflowController::class, 'show'])->name('show_data')->where('id', '[0-9]+');
        Route::get('/history/completed', [TaskWorkflowController::class, 'getCompletedHistory'])->name('completed_history_data');

        Route::post('/{id}/report', [TaskWorkflowController::class, 'submitReport'])->name('reports.store')->where('id', '[0-9]+');
    });

    Route::get('/asset-history', [AssetHistoryController::class, 'index'])->name('asset_history.index')->middleware(['role:SA00,MG00,WH01,WH02']);
});
