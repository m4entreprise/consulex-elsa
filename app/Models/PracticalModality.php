<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticalModality extends Model
{
    protected $fillable = [
        'order',
        'icon_name',
        'title',
        'description',
    ];

    protected $casts = [
        'order' => 'integer',
    ];
}
