<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\TaskTypeController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\TaskWorkflowController;
use App\Http\Controllers\AssetMaintenanceController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'viewPage'])
    ->middleware(['auth', 'verified'])->name('dashboard');

// Rute untuk form keluhan publik (tanpa login)
Route::get('/lapor-keluhan', [GuestComplaintController::class, 'create'])->name('guest.complaint.create');
Route::post('/lapor-keluhan', [GuestComplaintController::class, 'store'])->name('guest.complaint.store');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/complaints', [ComplaintController::class, 'viewPage'])->name('complaints.index');
});

// --- GRUP ROUTE UNTUK DATA MASTER ---
// Hanya bisa diakses oleh user yang sudah login DAN memiliki peran SA00 atau MG00
Route::middleware(['auth', 'role:SA00,MG00,HK01,TK01,SC01,PK01'])->group(function () {
    // Route untuk Gedung
    Route::get('/master/buildings', [BuildingController::class, 'viewPage'])->name('buildings.index');
    // Route untuk Lantai
    Route::get('/master/floors', [FloorController::class, 'viewPage'])->name('floors.index');
    // Route untuk Ruangan
    Route::get('/master/rooms', [RoomController::class, 'viewPage'])->name('rooms.index');
    // Route untuk Jenis Tugas
    Route::get('/master/task-types', [TaskTypeController::class, 'viewPage'])->name('task_types.index');
    // Route untuk Aset
    Route::get('/master/assets', [AssetController::class, 'viewPage'])->name('assets.index');
    // Route untuk Maintenance Aset
    Route::get('/master/maintenances', [AssetMaintenanceController::class, 'viewPage'])->name('maintenances.index');

    // Route untuk Halaman Ekspor
    Route::get('/export', [ExportController::class, 'viewPage'])->name('export.index'); // <-- Tambahkan ini

    // Route untuk Aksi Ekspor
    Route::get('/export/assets', [ExportController::class, 'exportAssets'])->name('export.assets');
    Route::get('/export/daily-reports', [ExportController::class, 'exportDailyReports'])->name('export.daily_reports');

    // Route untuk Halaman Riwayat Tugas
    Route::get('/history/tasks', [TaskWorkflowController::class, 'historyPage'])->name('history.tasks');

    // Route untuk Halaman Monitoring Tugas
    Route::get('/tasks/monitoring', [TaskWorkflowController::class, 'monitoringPage'])->name('tasks.monitoring');
});

// Hanya bisa diakses oleh Superadmin
Route::middleware(['auth', 'role:SA00'])->group(function () {
    Route::get('/admin/users', [UserController::class, 'viewPage'])->name('users.index');
});

// --- GRUP ROUTE UNTUK ALUR KERJA TUGAS ---
Route::middleware(['auth'])->name('tasks.')->prefix('tasks')->group(function () {

    // Rute untuk Leader & Manager (Menampilkan Halaman)
    Route::middleware(['role:SA00,MG00,HK01,TK01,SC01,PK01,HK02,TK02,SC02,PK02'])->group(function () {
        Route::get('/create', [TaskWorkflowController::class, 'createPage'])->name('create');
        Route::get('/review', [TaskWorkflowController::class, 'reviewPage'])->name('review_list');
    });

    // Rute untuk Staff (Menampilkan Halaman)
    Route::middleware(['role:HK02,TK02,SC02,PK02'])->group(function () {
        Route::get('/available', [TaskWorkflowController::class, 'availablePage'])->name('available');
        // Rute BARU untuk halaman History Tugas Staff
        Route::get('/history', [TaskWorkflowController::class, 'myHistoryPage'])->name('my_history');
    });

    // Rute yang lebih spesifik harus didefinisikan SEBELUM rute dengan parameter wildcard.
    Route::get('/my-tasks', [TaskWorkflowController::class, 'myTasksPage'])->name('my_tasks');
    Route::get('/completed-history', [TaskWorkflowController::class, 'completedHistoryPage'])->name('completed_history');

    // Rute dengan parameter (wildcard) ditempatkan di akhir.
    Route::get('/{task}', [TaskWorkflowController::class, 'showPage'])->name('show');
});

require __DIR__ . '/auth.php';
