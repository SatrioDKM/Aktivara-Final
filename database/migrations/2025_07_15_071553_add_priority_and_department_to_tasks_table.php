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
            // Tambahkan kolom baru untuk prioritas dan departemen
            $table->string('department_code', 10)->nullable()->after('task_type_id');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium')->after('department_code');

            // Jadikan task_type_id opsional (nullable) karena tidak lagi menjadi input utama
            $table->foreignId('task_type_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['department_code', 'priority']);
            $table->foreignId('task_type_id')->nullable(false)->change();
        });
    }
};
