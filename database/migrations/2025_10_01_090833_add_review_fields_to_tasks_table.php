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
            // Menambahkan kolom untuk ID user yang mereview
            $table->foreignId('reviewed_by')
                ->nullable()
                ->after('rejection_notes') // Posisikan setelah kolom catatan penolakan
                ->constrained('users')
                ->onDelete('set null');

            // Menambahkan kolom untuk catatan saat review (opsional)
            $table->text('review_notes')->nullable()->after('reviewed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Hapus foreign key constraint sebelum drop kolom
            $table->dropForeign(['reviewed_by']);

            // Hapus kolom yang telah ditambahkan
            $table->dropColumn(['reviewed_by', 'review_notes']);
        });
    }
};
