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
        Schema::table('packing_lists', function (Blueprint $table) {
            // Tambahkan kolom TEXT (karena path bisa panjang) yang bisa null, setelah 'notes'
            $table->text('signature_pad')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packing_lists', function (Blueprint $table) {
            // Hapus kolom jika migration di-rollback
            $table->dropColumn('signature_pad');
        });
    }
};
