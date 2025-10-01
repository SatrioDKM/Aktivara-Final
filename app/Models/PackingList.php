<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PackingList extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_number',
        'recipient_name',
        'created_by',
        'notes',
    ];

    /**
     * Relasi ke User yang membuat packing list.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke Aset (banyak ke banyak).
     */
    public function assets(): BelongsToMany
    {
        return $this->belongsToMany(Asset::class, 'asset_packing_list');
    }
}
