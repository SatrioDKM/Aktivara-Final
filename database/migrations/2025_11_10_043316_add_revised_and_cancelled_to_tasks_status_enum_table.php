<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQL specific: Change ENUM column
        // Note: This approach might cause issues if there are existing values that are not in the new ENUM list.
        // Ensure all existing 'rejected' tasks are handled before running this,
        // e.g., by setting them to 'cancelled' or 'revised' if appropriate.
        DB::statement("ALTER TABLE tasks CHANGE status status ENUM('unassigned', 'in_progress', 'pending_review', 'completed', 'rejected', 'revised', 'cancelled') DEFAULT 'unassigned'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to the original enum values
        DB::statement("ALTER TABLE tasks CHANGE status status ENUM('unassigned', 'in_progress', 'pending_review', 'completed', 'rejected') DEFAULT 'unassigned'");
    }
};