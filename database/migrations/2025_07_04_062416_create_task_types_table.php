<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_types', function (Blueprint $table) {
            $table->id(); // Standar Laravel
            $table->string('name_task', 100);
            $table->text('description')->nullable();
            $table->string('notification_template', 255)->nullable();
            $table->string('departemen', 50)->nullable(); // Perbaikan typo
            $table->enum('priority_level', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_types');
    }
};
