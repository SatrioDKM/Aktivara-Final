<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Cek dulu apakah kolomnya ada sebelum dihapus
            if (Schema::hasColumn('assets', 'department_code')) {
                Schema::table('assets', function (Blueprint $table) {
                    $table->dropColumn('department_code');
                });
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Jika perlu rollback, tambahkan kembali kolomnya
            $table->string('department_code', 10)->nullable()->after('task_type_id');
        });
    }
};
