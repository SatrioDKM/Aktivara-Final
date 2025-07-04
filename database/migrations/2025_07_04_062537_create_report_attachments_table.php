<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_attachments', function (Blueprint $table) {
            $table->id(); // Standar Laravel
            $table->foreignId('daily_report_id')->constrained('daily_reports')->onDelete('cascade');
            $table->string('file_path', 255);
            $table->string('file_type', 50)->nullable();
            $table->timestamp('uploaded_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_attachments');
    }
};
