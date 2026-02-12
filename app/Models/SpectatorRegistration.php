<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpectatorRegistration extends Model
{
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'accompanying_count',
        'food_option_id',
        'food_option_label',
        'accepted_rgpd',
        'accepted_rules',
    ];

    protected $casts = [
        'accompanying_count' => 'integer',
        'accepted_rgpd' => 'boolean',
        'accepted_rules' => 'boolean',
    ];

    public function seatsCount(): int
    {
        return 1 + (int) $this->accompanying_count;
    }
}
