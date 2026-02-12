<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventSetting extends Model
{
    protected $fillable = [
        'key',
        'event_title',
        'event_theme',
        'event_date',
        'event_location',
        'instagram_url',
        'aftermovie_url',
        'privacy_policy_url',
        'rules_url',
        'spectator_capacity',
        'candidate_capacity',
        'spectator_registrations_enabled',
        'candidate_registrations_enabled',
    ];

    protected $casts = [
        'spectator_capacity' => 'integer',
        'candidate_capacity' => 'integer',
        'spectator_registrations_enabled' => 'boolean',
        'candidate_registrations_enabled' => 'boolean',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate(['key' => 'default']);
    }
}
