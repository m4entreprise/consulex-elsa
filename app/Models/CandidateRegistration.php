<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateRegistration extends Model
{
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'faculty',
        'text_pdf_path',
        'photo_path',
        'accepted_rgpd',
        'accepted_rules',
    ];

    protected $casts = [
        'accepted_rgpd' => 'boolean',
        'accepted_rules' => 'boolean',
    ];
}
