<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperBuilding
 */
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

    protected function casts(): array
    {
        return [
            'lat_building' => 'double',
            'long_building' => 'double',
        ];
    }

    /**
     * Relasi ke semua lantai di gedung ini.
     */
    public function floors(): HasMany
    {
        return $this->hasMany(Floor::class);
    }

    /**
     * Relasi ke User yang membuat data gedung ini.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
