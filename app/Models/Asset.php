<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_asset',
        'description',
        'asset_type',
        'category',
        'serial_number',
        'purchase_date',
        'condition',
        'status',
        'current_stock',
        'minimum_stock',
        'room_id',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'current_stock' => 'integer',
            'minimum_stock' => 'integer',
        ];
    }

    /**
     * Relasi ke Room (lokasi aset).
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Relasi ke riwayat pemeliharaan aset ini.
     */
    public function maintenances(): HasMany
    {
        return $this->hasMany(AssetsMaintenance::class);
    }

    /**
     * Relasi ke semua tugas yang terkait dengan aset ini.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class)->latest();
    }

    /**
     * Relasi ke User yang membuat aset.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke User yang terakhir memperbarui aset.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relasi ke Packing Lists di mana aset ini terdaftar.
     */
    public function packingLists(): BelongsToMany
    {
        return $this->belongsToMany(PackingList::class, 'asset_packing_list');
    }
}
