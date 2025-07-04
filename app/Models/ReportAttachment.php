<?php

namespace App\Models;

use App\Models\DailyReport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportAttachment extends Model
{
    use HasFactory;

    // Tidak menggunakan created_at/updated_at bawaan
    public $timestamps = false;

    protected $fillable = [
        'daily_report_id',
        'file_path',
        'file_type',
        'uploaded_at',
    ];

    /**
     * Relasi ke DailyReport
     */
    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }
}
