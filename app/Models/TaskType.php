<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperTaskType
 */
class TaskType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_task',
        'description',
        'notification_template',
        'departemen',
        'priority_level',
        'asset_condition_on_create',
        'asset_status_on_create',
        'asset_condition_on_complete',
        'asset_status_on_complete',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'priority_level' => 'string',
        ];
    }
}
