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
        'asset_type',
        'category',
        'serial_number',
        'description',
        'purchase_date',
        'condition',
        'status',
        'current_stock',
        'minimum_stock',
        'created_by',
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
     * Relasi ke Maintenance Logs (untuk historikal maintenance)
     * (Nama relasi ini sudah benar, kita akan manfaatkan ini)
     */
    public function maintenances()
    {
        return $this->hasMany(AssetsMaintenance::class);
    }

    /**
     * Relasi ke User yang membuat aset.
     * (INI YANG DITAMBAHKAN)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke User yang terakhir memperbarui aset.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
