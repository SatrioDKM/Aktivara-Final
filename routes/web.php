<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\TaskTypeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\TaskWorkflowController;
use App\Http\Controllers\AssetMaintenanceController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'viewPage'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// --- GRUP ROUTE UNTUK DATA MASTER ---
// Hanya bisa diakses oleh user yang sudah login DAN memiliki peran SA00 atau MG00
Route::middleware(['auth', 'role:SA00,MG00'])->group(function () {
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
});

// --- GRUP ROUTE UNTUK ADMINISTRASI ---
// Hanya bisa diakses oleh Superadmin
Route::middleware(['auth', 'role:SA00'])->group(function () {
    Route::get('/admin/users', [UserController::class, 'viewPage'])->name('users.index');
});

// --- GRUP ROUTE UNTUK ALUR KERJA TUGAS ---
Route::middleware(['auth'])->name('tasks.')->prefix('tasks')->group(function () {

    // Rute untuk Leader & Manager (Menampilkan Halaman)
    Route::middleware(['role:SA00,MG00,HK01,TK01,SC01'])->group(function () {
        Route::get('/create', [TaskWorkflowController::class, 'createPage'])->name('create');
        Route::get('/review', [TaskWorkflowController::class, 'reviewPage'])->name('review_list');
    });

    // Rute untuk Staff (Menampilkan Halaman)
    Route::middleware(['role:HK02,TK02,SC02'])->group(function () {
        Route::get('/available', [TaskWorkflowController::class, 'availablePage'])->name('available');
    });

    // Rute yang bisa diakses bersama (Menampilkan Halaman)
    Route::get('/my-tasks', [TaskWorkflowController::class, 'myTasksPage'])->name('my_tasks');
    Route::get('/{task}', [TaskWorkflowController::class, 'showPage'])->name('show');
});

require __DIR__ . '/auth.php';
