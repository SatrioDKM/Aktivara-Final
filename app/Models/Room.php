<?php

namespace App\Models;

use App\Models\Floor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
}
