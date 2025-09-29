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
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TaskWorkflowController;
use App\Http\Controllers\StockManagementController;
use App\Http\Controllers\AssetMaintenanceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Endpoint API ini akan dilindungi oleh Sanctum
// Pastikan pengguna sudah login untuk mengaksesnya
Route::middleware(['auth:sanctum'])->group(function () {
    // Endpoint untuk mengambil data statistik dashboard
    Route::get('/dashboard-stats', [DashboardController::class, 'getStats'])->name('api.dashboard.stats');

    // --- Rute manual untuk Buildings ---
    Route::prefix('buildings')->group(function () {
        Route::get('/', [BuildingController::class, 'index']); // Untuk data tabel
        Route::post('/', [BuildingController::class, 'store']);
        Route::get('/{id}', [BuildingController::class, 'show'])->where('id', '[0-9]+');
        Route::put('/{id}', [BuildingController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('/{id}', [BuildingController::class, 'destroy'])->where('id', '[0-9]+');
    });

    // --- Rute manual untuk Floors ---
    Route::prefix('floors')->group(function () {
        Route::get('/', [FloorController::class, 'index']);
        Route::post('/', [FloorController::class, 'store']);
        Route::get('/{id}', [FloorController::class, 'show'])->where('id', '[0-9]+');
        Route::put('/{id}', [FloorController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('/{id}', [FloorController::class, 'destroy'])->where('id', '[0-9]+');
    });

    // --- Rute manual untuk Rooms ---
    Route::prefix('rooms')->group(function () {
        Route::get('/', [RoomController::class, 'index']);
        Route::post('/', [RoomController::class, 'store']);
        Route::get('/{id}', [RoomController::class, 'show'])->where('id', '[0-9]+');
        Route::put('/{id}', [RoomController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('/{id}', [RoomController::class, 'destroy'])->where('id', '[0-9]+');
    });

    // --- Rute manual untuk Task Types ---
    Route::prefix('task-types')->group(function () {
        Route::get('/', [TaskTypeController::class, 'index']);
        Route::post('/', [TaskTypeController::class, 'store']);
        Route::get('/{id}', [TaskTypeController::class, 'show'])->where('id', '[0-9]+');
        Route::put('/{id}', [TaskTypeController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('/{id}', [TaskTypeController::class, 'destroy'])->where('id', '[0-9]+');
    });

    // --- Rute manual untuk Assets ---
    Route::prefix('assets')->group(function () {
        Route::get('/', [AssetController::class, 'index']);
        Route::post('/', [AssetController::class, 'store']);
        Route::get('/{id}', [AssetController::class, 'show'])->where('id', '[0-9]+');
        Route::put('/{id}', [AssetController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('/{id}', [AssetController::class, 'destroy'])->where('id', '[0-9]+');
        Route::post('/{id}/stock-out', [AssetController::class, 'stockOut'])->name('api.assets.stock_out')->where('id', '[0-9]+');
    });

    // --- Rute manual untuk Maintenances ---
    Route::get('/maintenances', [AssetMaintenanceController::class, 'index']);
    Route::post('/maintenances', [AssetMaintenanceController::class, 'store']);
    Route::get('/maintenances/{id}', [AssetMaintenanceController::class, 'show'])->where('id', '[0-9]+');
    Route::put('/maintenances/{id}', [AssetMaintenanceController::class, 'update'])->where('id', '[0-9]+');
    Route::delete('/maintenances/{id}', [AssetMaintenanceController::class, 'destroy'])->where('id', '[0-9]+');

    // --- Rute manual untuk Users (Hanya Superadmin) ---
    Route::middleware('role:SA00')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('api.users.index');
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{id}', [UserController::class, 'show'])->where('id', '[0-9]+');
        Route::put('/users/{id}', [UserController::class, 'update'])->where('id', '[0-9]+');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->where('id', '[0-9]+');
    });


    // --- API untuk Manajemen Stok ---
    Route::middleware(['role:SA00,MG00,WH01,WH02'])->group(function () {
        Route::get('/stock-management', [StockManagementController::class, 'index'])->name('api.stock.index');
        Route::put('/stock-management/{id}', [StockManagementController::class, 'update'])->name('api.stock.update')->where('id', '[0-9]+');
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

    // --- GRUP ENDPOINT UNTUK LAPORAN/KELUHAN ---
    Route::prefix('complaints')->name('api.complaints.')->group(function () {
        Route::get('/', [ComplaintController::class, 'index'])->name('index');
        Route::post('/', [ComplaintController::class, 'store'])->name('store');
        Route::post('/{id}/convert', [ComplaintController::class, 'convertToTask'])->name('convert')->where('id', '[0-9]+');
    });
});
