<?php

namespace App\Models;

use App\Models\Room;
use App\Models\Task;
use App\Models\User;
use App\Models\Asset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Added for HasFactory

class AssetMovement extends Model
{
    use HasFactory; // Added for HasFactory

    protected $fillable = [
        'asset_id',
        'from_room_id',
        'to_room_id',
        'moved_by_user_id',
        'movement_time',
        'task_id',
        'description',
    ];

    protected $casts = [
        'movement_time' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function fromRoom(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'from_room_id');
    }

    public function toRoom(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'to_room_id');
    }

    public function movedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moved_by_user_id');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }
}
