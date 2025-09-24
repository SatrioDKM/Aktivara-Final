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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('reporter_name')->comment('Nama pelapor, bisa tamu atau staff');
            $table->string('location_text')->comment('Deskripsi lokasi, cth: Lobi dekat pintu barat');

            // Kolom status untuk melacak progres laporan
            $table->enum('status', ['open', 'converted_to_task', 'closed'])->default('open');

            // Relasi (opsional saat laporan dibuat)
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('asset_id')->nullable()->constrained()->onDelete('set null');

            // Relasi ke tabel lain
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null'); // Siapa yang mencatat laporan ini di sistem
            $table->foreignId('task_id')->nullable()->unique()->constrained()->onDelete('set null'); // Tautan ke tugas yang dihasilkan

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
