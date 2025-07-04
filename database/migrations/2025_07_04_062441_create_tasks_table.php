<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id(); // Standar Laravel
            $table->foreignId('task_type_id')->constrained('task_types');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('asset_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['unassigned', 'in_progress', 'pending_review', 'completed', 'rejected'])
                ->default('unassigned');
            $table->dateTime('due_date')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
