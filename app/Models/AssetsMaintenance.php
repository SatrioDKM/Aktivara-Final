<?php

namespace App\Models;

use App\Models\Task;
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
        'description', // Disesuaikan dengan migrasi
        'notes',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'maintenance_type' => 'string',
            'status' => 'string',
        ];
    }

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

    /**
     * Relasi ke tugas yang dihasilkan oleh laporan maintenance ini.
     */
    public function generatedTask()
    {
        return $this->hasOne(Task::class, 'assets_maintenance_id');
    }
}
