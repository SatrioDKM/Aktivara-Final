<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id(); // Standar Laravel
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name_asset', 100);
            $table->string('category', 50)->nullable();
            $table->string('serial_number', 100)->nullable()->unique();
            $table->text('description')->nullable();
            $table->date('purchase_date')->nullable();
            $table->string('condition', 50)->nullable();
            $table->enum('status', ['available', 'in_use', 'maintenance', 'disposed'])->default('available');
            $table->integer('current_stock')->default(0);
            $table->integer('minimum_stock')->default(0);
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
