<?php

namespace App\Models;

use App\Models\Room;
use App\Models\User;
use App\Models\Asset;
use App\Models\TaskType;
use App\Models\AssetsMaintenance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'rejection_notes',
        'due_date',
        'image_before',
        'image_after',
        'report_text', // Pastikan ini juga ada
        'task_type_id',
        'user_id',
        'asset_id',
        'room_id',
        'created_by', // Ini adalah kolom database
        'assets_maintenance_id',
        'reviewed_by', // Pastikan ini juga ada
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
        ];
    }

    /**
     * Relasi ke User (staff yang ditugaskan).
     * Nama: assignee
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke User (leader yang membuat).
     * PERBAIKAN UTAMA: Nama method adalah 'creator', bukan 'createdBy'.
     * Ini untuk menghindari konflik dengan kolom 'created_by'.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke TaskType.
     */
    public function taskType(): BelongsTo
    {
        return $this->belongsTo(TaskType::class);
    }

    /**
     * Relasi ke Room.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Relasi ke Asset.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Relasi ke record maintenance yang terkait.
     */
    public function maintenanceRecord(): BelongsTo
    {
        return $this->belongsTo(AssetsMaintenance::class, 'assets_maintenance_id');
    }
}
