<?php

namespace App\Models;

use App\Models\Room;
use App\Models\User;
use App\Models\Asset;
use App\Models\TaskType;
use App\Models\DailyReport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

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
    ];

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
     * Relasi ke DailyReports
     */
    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class);
    }
}
