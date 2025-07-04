<?php

namespace App\Models;

use App\Models\User;
use App\Models\Floor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Building extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_building',
        'address',
        'lat_building',
        'long_building',
        'created_by',
        'status',
    ];

    /**
     * Relasi ke Floors
     */
    public function floors()
    {
        return $this->hasMany(Floor::class);
    }

    /**
     * Relasi ke User yang membuat
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
