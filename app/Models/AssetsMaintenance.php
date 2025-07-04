<?php

namespace App\Models;

use App\Models\User;
use App\Models\Asset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssetsMaintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'user_id', // Teknisi yang mengerjakan
        'start_date',
        'end_date',
        'maintenance_type',
        'description_text',
        'notes',
        'status',
    ];

    /**
     * Relasi ke Asset
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Relasi ke User (teknisi)
     */
    public function technician()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
