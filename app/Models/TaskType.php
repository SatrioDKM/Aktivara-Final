<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_task',
        'description',
        'notification_template',
        'departemen',
        'priority_level',
    ];
}
