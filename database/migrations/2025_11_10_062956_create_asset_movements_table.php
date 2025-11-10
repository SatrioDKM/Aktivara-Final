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
        Schema::create('asset_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            $table->foreignId('from_room_id')->nullable()->constrained('rooms')->onDelete('set null');
            $table->foreignId('to_room_id')->nullable()->constrained('rooms')->onDelete('set null');
            $table->foreignId('moved_by_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('movement_time')->useCurrent();
            $table->foreignId('task_id')->nullable()->constrained('tasks')->onDelete('set null');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_movements');
    }
};
