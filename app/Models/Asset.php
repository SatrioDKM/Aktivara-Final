<?php

namespace App\Models;

use App\Models\Room;
use App\Models\User;
use App\Models\AssetsMaintenance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'name_asset',
        'category',
        'serial_number',
        'description',
        'purchase_date',
        'condition',
        'status',
        'current_stock',
        'minimum_stock',
        'updated_by',
    ];

    /**
     * Relasi ke Room (lokasi aset)
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Relasi ke Maintenance Logs
     */
    public function maintenances()
    {
        return $this->hasMany(AssetsMaintenance::class);
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
