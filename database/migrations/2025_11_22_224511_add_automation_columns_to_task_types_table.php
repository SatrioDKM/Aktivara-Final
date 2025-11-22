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
        Schema::table('task_types', function (Blueprint $table) {
            $table->string('asset_condition_on_create')->nullable()->after('departemen');
            $table->enum('asset_status_on_create', ['available', 'in_use', 'maintenance', 'disposed'])->nullable()->after('asset_condition_on_create');
            $table->string('asset_condition_on_complete')->nullable()->after('asset_status_on_create');
            $table->enum('asset_status_on_complete', ['available', 'in_use', 'maintenance', 'disposed'])->nullable()->after('asset_condition_on_complete');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_types', function (Blueprint $table) {
            $table->dropColumn([
                'asset_condition_on_create',
                'asset_status_on_create',
                'asset_condition_on_complete',
                'asset_status_on_complete'
            ]);
        });
    }
};
