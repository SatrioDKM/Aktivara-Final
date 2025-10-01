<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AssetsMaintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'user_id', // Teknisi yang mengerjakan
        'start_date',
        'end_date',
        'maintenance_type',
        'description',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    /**
     * Relasi ke Asset yang sedang dalam pemeliharaan.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Relasi ke User (teknisi) yang bertanggung jawab.
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke tugas yang dihasilkan oleh jadwal maintenance ini.
     */
    public function task(): HasOne
    {
        return $this->hasOne(Task::class, 'assets_maintenance_id');
    }
}
