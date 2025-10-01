<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\TaskTypeController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\PackingListController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TaskWorkflowController;
use App\Http\Controllers\StockManagementController;
use App\Http\Controllers\AssetMaintenanceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/dashboard-stats', [DashboardController::class, 'getStats'])->name('api.dashboard.stats');

    // --- Rute manual untuk Buildings ---
    Route::prefix('buildings')->name('api.buildings.')->group(function () {
        Route::get('/', [BuildingController::class, 'index'])->name('index');
        Route::post('/', [BuildingController::class, 'store'])->name('store');
        Route::get('/{id}', [BuildingController::class, 'show'])->where('id', '[0-9]+')->name('show');
        Route::put('/{id}', [BuildingController::class, 'update'])->where('id', '[0-9]+')->name('update');
        Route::delete('/{id}', [BuildingController::class, 'destroy'])->where('id', '[0-9]+')->name('destroy');
    });

    // --- Rute manual untuk Floors ---
    Route::prefix('floors')->name('api.floors.')->group(function () {
        Route::get('/', [FloorController::class, 'index'])->name('index');
        Route::post('/', [FloorController::class, 'store'])->name('store');
        Route::get('/{id}', [FloorController::class, 'show'])->where('id', '[0-9]+')->name('show');
        Route::put('/{id}', [FloorController::class, 'update'])->where('id', '[0-9]+')->name('update');
        Route::delete('/{id}', [FloorController::class, 'destroy'])->where('id', '[0-9]+')->name('destroy');
    });

    // --- Rute manual untuk Rooms ---
    Route::prefix('rooms')->name('api.rooms.')->group(function () {
        Route::get('/', [RoomController::class, 'index'])->name('index');
        Route::post('/', [RoomController::class, 'store'])->name('store');
        Route::get('/{id}', [RoomController::class, 'show'])->where('id', '[0-9]+')->name('show');
        Route::put('/{id}', [RoomController::class, 'update'])->where('id', '[0-9]+')->name('update');
        Route::delete('/{id}', [RoomController::class, 'destroy'])->where('id', '[0-9]+')->name('destroy');
    });

    // ================== BAGIAN YANG DIPERBARUI ==================
    // --- Rute manual untuk Task Types ---
    Route::prefix('task-types')->name('api.task-types.')->group(function () {
        Route::get('/', [TaskTypeController::class, 'index'])->name('index');
        Route::post('/', [TaskTypeController::class, 'store'])->name('store');
        Route::get('/{id}', [TaskTypeController::class, 'show'])->where('id', '[0-9]+')->name('show');
        Route::put('/{id}', [TaskTypeController::class, 'update'])->where('id', '[0-9]+')->name('update');
        Route::delete('/{id}', [TaskTypeController::class, 'destroy'])->where('id', '[0-9]+')->name('destroy');
    });

    // Rute API tambahan untuk mengambil jenis tugas berdasarkan departemen (YANG HILANG)
    Route::get('/task-types/by-department/{department_code}', [TaskTypeController::class, 'getByDepartment'])
        ->name('api.task-types.by-department');
    // ==========================================================

    // --- Rute manual untuk Assets ---
    Route::prefix('assets')->name('api.assets.')->group(function () {
        Route::get('/', [AssetController::class, 'index'])->name('index');
        Route::post('/', [AssetController::class, 'store'])->name('store');
        Route::get('/{id}', [AssetController::class, 'show'])->where('id', '[0-9]+')->name('show');
        Route::put('/{id}', [AssetController::class, 'update'])->where('id', '[0-9]+')->name('update');
        Route::delete('/{id}', [AssetController::class, 'destroy'])->where('id', '[0-9]+')->name('destroy');
        Route::post('/{id}/stock-out', [AssetController::class, 'stockOut'])->name('stock_out')->where('id', '[0-9]+');
    });

    // --- Rute manual untuk Maintenances ---
    Route::prefix('maintenances')->name('api.maintenances.')->group(function () {
        Route::get('/', [AssetMaintenanceController::class, 'index'])->name('index');
        Route::post('/', [AssetMaintenanceController::class, 'store'])->name('store');
        Route::get('/{id}', [AssetMaintenanceController::class, 'show'])->where('id', '[0-9]+')->name('show');
        Route::put('/{id}', [AssetMaintenanceController::class, 'update'])->where('id', '[0-9]+')->name('update');
        Route::delete('/{id}', [AssetMaintenanceController::class, 'destroy'])->where('id', '[0-9]+')->name('destroy');
    });

    // --- Rute manual untuk Users (Hanya Superadmin) ---
    Route::middleware('role:SA00')->prefix('users')->name('api.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{id}', [UserController::class, 'show'])->where('id', '[0-9]+')->name('show');
        Route::put('/{id}', [UserController::class, 'update'])->where('id', '[0-9]+')->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->where('id', '[0-9]+')->name('destroy');
    });

    // --- API untuk Manajemen Stok ---
    Route::middleware(['role:SA00,MG00,WH01,WH02'])->prefix('stock-management')->name('api.stock.')->group(function () {
        Route::get('/', [StockManagementController::class, 'index'])->name('index');
        Route::put('/{id}', [StockManagementController::class, 'update'])->name('update')->where('id', '[0-9]+');
    });

    // --- API untuk Packing List ---
    Route::middleware(['role:SA00,MG00,WH01,WH02'])->prefix('packing-lists')->name('api.packing_lists.')->group(function () {
        Route::get('/', [PackingListController::class, 'index'])->name('index');
        Route::post('/', [PackingListController::class, 'store'])->name('store');
        Route::get('/get-assets', [PackingListController::class, 'getAvailableAssets'])->name('get_assets');
    });

    // --- GRUP ENDPOINT UNTUK LAPORAN/KELUHAN ---
    Route::middleware(['role:SA00,MG00,HK01,TK01,SC01,PK01,WH01'])->prefix('complaints')->name('api.complaints.')->group(function () {
        Route::get('/', [ComplaintController::class, 'index'])->name('index');
        Route::post('/', [ComplaintController::class, 'store'])->name('store');
        Route::get('/{id}', [ComplaintController::class, 'show'])->where('id', '[0-9]+')->name('show');
        Route::delete('/{id}', [ComplaintController::class, 'destroy'])->where('id', '[0-9]+')->name('destroy');
        Route::post('/{id}/convert', [ComplaintController::class, 'convertToTask'])->name('convert')->where('id', '[0-9]+');
    });

    // --- GRUP ENDPOINT UNTUK ALUR KERJA TUGAS ---
    Route::prefix('tasks')->name('api.tasks.')->group(function () {

        // Endpoint untuk Leader & Manager
        Route::middleware(['role:SA00,MG00,HK01,TK01,SC01'])->group(function () {
            Route::post('/', [TaskWorkflowController::class, 'store'])->name('store');
            Route::get('/review-list', [TaskWorkflowController::class, 'showReviewList'])->name('review_list_data');
            Route::post('/{id}/review', [TaskWorkflowController::class, 'submitReview'])->name('submit_review')->where('id', '[0-9]+');
            Route::get('/in-progress', [TaskWorkflowController::class, 'getInProgressTasks'])->name('in_progress_data');
            Route::get('/active', [TaskWorkflowController::class, 'getActiveTasks'])->name('active_data');
        });

        // Endpoint untuk Staff
        Route::middleware(['role:HK02,TK02,SC02'])->group(function () {
            Route::get('/available-list', [TaskWorkflowController::class, 'showAvailable'])->name('available_data');
            Route::post('/{id}/claim', [TaskWorkflowController::class, 'claimTask'])->name('claim')->where('id', '[0-9]+');
        });

        // Endpoint API BARU untuk riwayat tugas PRIBADI Staff
        Route::get('/my-history', [TaskWorkflowController::class, 'getMyTaskHistory'])->name('my_history_data');

        // Endpoint yang bisa diakses bersama
        Route::get('/history', [TaskWorkflowController::class, 'getTaskHistory'])->name('history_data');
        Route::get('/my-tasks-list', [TaskWorkflowController::class, 'myTasks'])->name('my_tasks_data');
        Route::get('/{id}', [TaskWorkflowController::class, 'show'])->name('show_data')->where('id', '[0-9]+');

        // Endpoint API untuk mengambil data riwayat tugas yang selesai
        Route::get('/history/completed', [TaskWorkflowController::class, 'getCompletedHistory'])->name('completed_history_data');

        // Endpoint untuk submit laporan
        Route::post('/{id}/report', [DailyReportController::class, 'storeApi'])->name('reports.store_api')->where('id', '[0-9]+');
    });

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
});
