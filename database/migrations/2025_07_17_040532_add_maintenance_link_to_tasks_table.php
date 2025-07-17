<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Tambahkan foreign key ke tabel assets_maintenances
            // onDelete('set null') berarti jika record maintenance dihapus, tugasnya tidak ikut terhapus.
            $table->foreignId('assets_maintenance_id')->nullable()->after('asset_id')->constrained('assets_maintenances')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['assets_maintenance_id']);
            $table->dropColumn('assets_maintenance_id');
        });
    }
};
