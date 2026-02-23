<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
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
        'spectator_registrations_end_at',
        'candidate_registrations_enabled',
        'candidate_registrations_end_at',
    ];

    protected $casts = [
        'event_edition_year' => 'integer',
        'spectator_capacity' => 'integer',
        'candidate_capacity' => 'integer',
        'spectator_registrations_enabled' => 'boolean',
        'spectator_registrations_end_at' => 'datetime',
        'candidate_registrations_enabled' => 'boolean',
        'candidate_registrations_end_at' => 'datetime',
        'timeline' => 'array',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate(['key' => 'default']);
    }

    public function getSpectatorRegistrationsEnabledAttribute(mixed $value): bool
    {
        $enabled = (bool) $value;

        if (! $enabled) {
            return false;
        }

        $endAt = $this->spectator_registrations_end_at;

        if ($endAt instanceof Carbon && now()->greaterThanOrEqualTo($endAt)) {
            return false;
        }

        return true;
    }

    public function getCandidateRegistrationsEnabledAttribute(mixed $value): bool
    {
        $enabled = (bool) $value;

        if (! $enabled) {
            return false;
        }

        $endAt = $this->candidate_registrations_end_at;

        if ($endAt instanceof Carbon && now()->greaterThanOrEqualTo($endAt)) {
            return false;
        }

        return true;
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
