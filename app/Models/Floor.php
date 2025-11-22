<?php

namespace App\Models;

use App\Models\Room;
use App\Models\User;
use App\Models\Building;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperFloor
 */
class Floor extends Model
{
    use HasFactory;

    protected $fillable = [
        'building_id',
        'name_floor',
        'created_by',
        'status',
    ];

    /**
     * Relasi ke Building
     */
    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Relasi ke Rooms
     */
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Relasi ke User yang membuat (INI YANG DITAMBAHKAN)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
