<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('task_types', function (Blueprint $table) {
            // Mengubah tipe kolom menjadi ENUM
            // Karena Doctrine DBAL (yang digunakan Laravel untuk rename/change column) memiliki keterbatasan dengan ENUM,
            // kita gunakan raw statement untuk memastikan kompatibilitas.
            DB::statement("ALTER TABLE task_types MODIFY COLUMN asset_condition_on_create ENUM('Baik', 'Rusak') NULL");
            DB::statement("ALTER TABLE task_types MODIFY COLUMN asset_condition_on_complete ENUM('Baik', 'Rusak') NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_types', function (Blueprint $table) {
            // Kembalikan ke VARCHAR jika rollback
            DB::statement("ALTER TABLE task_types MODIFY COLUMN asset_condition_on_create VARCHAR(255) NULL");
            DB::statement("ALTER TABLE task_types MODIFY COLUMN asset_condition_on_complete VARCHAR(255) NULL");
        });
    }
};
