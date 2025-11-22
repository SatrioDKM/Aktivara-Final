<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperComplaint
 */
class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'reporter_name',
        'location_text',
        'status',
        'room_id',
        'asset_id',
        'created_by',
        'task_id',
    ];

    /**
     * Relasi ke User yang mencatat laporan.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke Tugas yang dihasilkan dari laporan ini.
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    /**
     * Relasi ke Ruangan (jika ada).
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Relasi ke Aset (jika ada).
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
