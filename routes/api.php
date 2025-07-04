<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\TaskTypeController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TaskWorkflowController;

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
    Route::apiResource('buildings', BuildingController::class);
    Route::apiResource('floors', FloorController::class);
    Route::apiResource('rooms', RoomController::class);
    Route::apiResource('task-types', TaskTypeController::class);
    Route::apiResource('assets', AssetController::class);
    Route::apiResource('users', UserController::class)->middleware('role:SA00');

    // --- GRUP ENDPOINT UNTUK ALUR KERJA TUGAS ---
    Route::prefix('tasks')->name('api.tasks.')->group(function () {

        // Endpoint untuk Leader & Manager
        Route::middleware(['role:SA00,MG00,HK01,TK01,SC01'])->group(function () {
            Route::post('/', [TaskWorkflowController::class, 'store'])->name('store');
            Route::get('/review-list', [TaskWorkflowController::class, 'showReviewList'])->name('review_list_data');
            Route::post('/{task}/review', [TaskWorkflowController::class, 'submitReview'])->name('submit_review');
        });

        // Endpoint untuk Staff
        Route::middleware(['role:HK02,TK02,SC02'])->group(function () {
            Route::get('/available-list', [TaskWorkflowController::class, 'showAvailable'])->name('available_data');
            Route::post('/{task}/claim', [TaskWorkflowController::class, 'claimTask'])->name('claim');
        });

        // Endpoint yang bisa diakses bersama
        Route::get('/my-tasks-list', [TaskWorkflowController::class, 'myTasks'])->name('my_tasks_data');
        Route::get('/{task}', [TaskWorkflowController::class, 'show'])->name('show_data');

        // Endpoint untuk submit laporan
        Route::post('/{task}/report', [DailyReportController::class, 'storeApi'])->name('reports.store_api');
    });

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
});
