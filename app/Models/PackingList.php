<?php

namespace App\Models;

use App\Models\User;
use App\Models\Asset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke Aset (banyak ke banyak).
     */
    public function assets()
    {
        return $this->belongsToMany(Asset::class, 'asset_packing_list');
    }
}
