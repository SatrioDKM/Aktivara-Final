<?php

namespace App\Models;

use App\Models\Task;
use App\Models\User;
use App\Models\ReportAttachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DailyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'task_id',
        'title',
        'description',
        'reviewed_notes',
        'reviewed_at',
        'reviewed_by',
        'status',
    ];

    /**
     * Relasi ke User (yang membuat laporan)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Task
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Relasi ke Attachments
     */
    public function attachments()
    {
        return $this->hasMany(ReportAttachment::class);
    }
}
