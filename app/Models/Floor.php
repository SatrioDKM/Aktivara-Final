<?php

namespace App\Models;

use App\Models\Room;
use App\Models\Building;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
}
