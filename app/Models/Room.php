<?php

namespace App\Models;

use App\Models\User;
use App\Models\Floor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperRoom
 */
class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'floor_id',
        'name_room',
        'created_by',
        'status',
    ];

    /**
     * Relasi ke Floor
     */
    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    /**
     * Relasi ke User yang membuat
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
