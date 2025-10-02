<?php

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
use App\Http\Controllers\GuestComplaintController;
use App\Http\Controllers\StockManagementController;
use App\Http\Controllers\AssetMaintenanceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Direvisi menggunakan Route::apiResource untuk menyederhanakan
| definisi endpoint CRUD dan membuatnya lebih mudah dikelola.
|
*/

Route::middleware(['auth:sanctum'])->name('api.')->group(function () {

    // === Endpoint Umum ===
    Route::get('/dashboard-stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    // === Resourceful Routes untuk Data Master ===
    // Menggantikan puluhan baris kode dengan beberapa baris saja.
    Route::apiResource('buildings', BuildingController::class);
    Route::apiResource('floors', FloorController::class);
    Route::apiResource('rooms', RoomController::class);
    Route::apiResource('assets', AssetController::class);
    Route::apiResource('maintenances', AssetMaintenanceController::class)->parameters(['maintenances' => 'id']);
    Route::apiResource('task-types', TaskTypeController::class);

    // Rute API tambahan diletakkan di sini
    Route::get('/task-types/by-department/{department_code}', [TaskTypeController::class, 'getByDepartment'])->name('task-types.by-department');
    Route::post('/assets/{id}/stock-out', [AssetController::class, 'stockOut'])->name('assets.stock_out')->where('id', '[0-9]+');

    // === Endpoint dengan Hak Akses Spesifik ===

    // Users (Hanya Superadmin)
    Route::apiResource('users', UserController::class)->middleware('role:SA00');

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

        // Route::post('/{id}/report', [DailyReportController::class, 'storeApi'])->name('reports.store_api')->where('id', '[0-9]+');
        Route::post('/{id}/report', [TaskWorkflowController::class, 'submitReport'])->name('reports.store')->where('id', '[0-9]+');
    });
});

// Rute Publik untuk Laporan Keluhan Tamu
Route::post('/guest-complaints', [GuestComplaintController::class, 'store'])
    ->name('api.guest.complaints.store');
