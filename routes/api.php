<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\TaskTypeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TaskWorkflowController;
use App\Http\Controllers\AssetMaintenanceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Endpoint API ini akan dilindungi oleh Sanctum
// Pastikan pengguna sudah login untuk mengaksesnya
Route::middleware(['auth:sanctum'])->group(function () {
    // Endpoint untuk mengambil data statistik dashboard
    Route::get('/dashboard-stats', [DashboardController::class, 'getStats'])->name('api.dashboard.stats');

    Route::apiResource('buildings', BuildingController::class);
    Route::apiResource('floors', FloorController::class);
    Route::apiResource('rooms', RoomController::class);
    Route::apiResource('task-types', TaskTypeController::class);
    Route::apiResource('assets', AssetController::class);
    Route::apiResource('maintenances', AssetMaintenanceController::class);
    Route::apiResource('users', UserController::class)->middleware('role:SA00');

    // --- GRUP ENDPOINT UNTUK ALUR KERJA TUGAS ---
    Route::prefix('tasks')->name('api.tasks.')->group(function () {

        // Endpoint untuk Leader & Manager
        Route::middleware(['role:SA00,MG00,HK01,TK01,SC01'])->group(function () {
            Route::post('/', [TaskWorkflowController::class, 'store'])->name('store');
            Route::get('/review-list', [TaskWorkflowController::class, 'showReviewList'])->name('review_list_data');
            Route::post('/{task}/review', [TaskWorkflowController::class, 'submitReview'])->name('submit_review');
            Route::get('/in-progress', [TaskWorkflowController::class, 'getInProgressTasks'])->name('in_progress_data');
        });

        // Endpoint untuk Staff
        Route::middleware(['role:HK02,TK02,SC02'])->group(function () {
            Route::get('/available-list', [TaskWorkflowController::class, 'showAvailable'])->name('available_data');
            Route::post('/{task}/claim', [TaskWorkflowController::class, 'claimTask'])->name('claim');
        });

        // Endpoint API BARU untuk riwayat tugas PRIBADI Staff
        Route::get('/my-history', [TaskWorkflowController::class, 'getMyTaskHistory'])->name('my_history_data');

        // Endpoint yang bisa diakses bersama
        Route::get('/history', [TaskWorkflowController::class, 'getTaskHistory'])->name('history_data');
        Route::get('/my-tasks-list', [TaskWorkflowController::class, 'myTasks'])->name('my_tasks_data');
        Route::get('/{task}', [TaskWorkflowController::class, 'show'])->name('show_data');

        // Endpoint API untuk mengambil data riwayat tugas yang selesai
        Route::get('/history/completed', [TaskWorkflowController::class, 'getCompletedHistory'])->name('completed_history_data');

        // Endpoint untuk submit laporan
        Route::post('/{task}/report', [DailyReportController::class, 'storeApi'])->name('reports.store_api');
    });

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
});
