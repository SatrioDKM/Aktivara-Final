<?php

namespace App\Models;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperAssetCategory
 */
class AssetCategory extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'asset_categories';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code', // Kode singkatan kategori
    ];

    /**
     * Mendefinisikan relasi "one-to-many" ke model Asset.
     * Satu kategori bisa memiliki banyak aset.
     */
    public function assets()
    {
        return $this->hasMany(Asset::class, 'asset_category_id');
    }
}
