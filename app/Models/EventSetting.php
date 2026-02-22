<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EventSetting extends Model
{
    protected $fillable = [
        'key',
        'event_edition_year',
        'event_title',
        'event_description',
        'event_theme',
        'event_date',
        'event_location',
        'venue_room_name',
        'venue_room_title',
        'map_place_label',
        'map_address',
        'map_open_url',
        'map_embed_url',
        'access_text',
        'network_text',
        'timeline',
        'instagram_url',
        'privacy_policy_url',
        'rules_url',
        'footer_brand',
        'footer_description',
        'footer_copyright',
        'spectator_capacity',
        'candidate_capacity',
        'spectator_registrations_enabled',
        'candidate_registrations_enabled',
    ];

    protected $casts = [
        'event_edition_year' => 'integer',
        'spectator_capacity' => 'integer',
        'candidate_capacity' => 'integer',
        'spectator_registrations_enabled' => 'boolean',
        'candidate_registrations_enabled' => 'boolean',
        'timeline' => 'array',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate(['key' => 'default']);
    }

    public function getPrivacyPolicyUrlAttribute(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return Storage::disk('public')->url($value);
    }

    public function getRulesUrlAttribute(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return Storage::disk('public')->url($value);
    }
}
