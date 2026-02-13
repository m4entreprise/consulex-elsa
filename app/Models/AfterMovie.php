<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AfterMovie extends Model
{
    protected $fillable = [
        'date',
        'location',
        'theme',
        'winner',
        'aftermovie_url',
    ];
}
