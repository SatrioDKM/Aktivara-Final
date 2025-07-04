<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BuildingController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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
});

require __DIR__ . '/auth.php';
