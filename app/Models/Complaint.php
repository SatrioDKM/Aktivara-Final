<?php

namespace App\Models;

use App\Models\Room;
use App\Models\Task;
use App\Models\User;
use App\Models\Asset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke Tugas yang dihasilkan dari laporan ini.
     */
    public function generatedTask()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    /**
     * Relasi ke Ruangan (jika ada).
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Relasi ke Aset (jika ada).
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
