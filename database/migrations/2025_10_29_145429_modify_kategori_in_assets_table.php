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
        Schema::table('assets', function (Blueprint $table) {
            if (!Schema::hasColumn('assets', 'asset_category_id')) {
                $table->foreignId('asset_category_id')
                    ->nullable()
                    ->after('name_asset')
                    ->constrained('asset_categories')
                    ->onDelete('set null');
            }

            if (Schema::hasColumn('assets', 'category')) {
                $table->dropColumn('category');
            }
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // 1. Tambahkan kembali kolom 'kategori'
            $table->string('category')->nullable()->after('name_asset');

            // 2. Hapus foreign key dan kolom 'asset_category_id'
            // Pastikan nama constraint-nya (misal: assets_asset_category_id_foreign)
            // atau drop berdasarkan kolom
            $table->dropForeign(['asset_category_id']);
            $table->dropColumn('asset_category_id');
        });
    }
};
