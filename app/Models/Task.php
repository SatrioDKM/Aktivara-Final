<?php

namespace App\Models;

use App\Models\Room;
use App\Models\User;
use App\Models\Asset;
use App\Models\TaskType;
use App\Models\DailyReport;
use App\Models\AssetsMaintenance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'task_type_id',
        'user_id', // Staff yang mengerjakan
        'asset_id',
        'room_id',
        'title',
        'description',
        'status',
        'due_date',
        'created_by', // Leader yang membuat
        'assets_maintenance_id',
        'report_text',
        'image_before', // Kolom baru untuk foto sebelum
        'image_after',  // Kolom baru untuk foto sesudah
        'reviewed_by',
        'review_notes',
        'rejection_notes', // Kolom untuk catatan penolakan
        'priority', // Ditambahkan dari migrasi
        'department_code', // Ditambahkan dari migrasi
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'status' => 'string',
            'priority' => 'string',
        ];
    }

    /**
     * Relasi ke User (staff yang mengerjakan)
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke User (leader yang membuat)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke TaskType
     */
    public function taskType()
    {
        return $this->belongsTo(TaskType::class);
    }

    /**
     * Relasi ke Room
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Relasi ke Asset
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Relasi ke DailyReports (jika masih digunakan)
     */
    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class);
    }

    /**
     * Relasi ke record maintenance yang menghasilkan tugas ini.
     */
    public function maintenanceRecord()
    {
        return $this->belongsTo(AssetsMaintenance::class, 'assets_maintenance_id');
    }
}
