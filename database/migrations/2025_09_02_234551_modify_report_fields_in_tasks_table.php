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
            // Hapus kolom report_image yang lama jika ada
            if (Schema::hasColumn('tasks', 'report_image')) {
                $table->dropColumn('report_image');
            }

            // Tambahkan kolom baru tanpa '.after()' agar tidak error
            $table->string('image_before')->nullable();
            $table->string('image_after')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Tambahkan kembali kolom report_image jika di-rollback
            if (!Schema::hasColumn('tasks', 'report_image')) {
                $table->string('report_image')->nullable();
            }

            $table->dropColumn(['image_before', 'image_after']);
        });
    }
};
