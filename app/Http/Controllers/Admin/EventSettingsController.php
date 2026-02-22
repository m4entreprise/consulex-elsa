<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class EventSettingsController extends Controller
{
    public function edit(): Response
    {
        $settings = EventSetting::current();

        return Inertia::render('admin/Settings', [
            'settings' => $settings,
        ]);
    }

    public function update(): RedirectResponse
    {
        $settings = EventSetting::current();

        $validated = request()->validate([
            'event_edition_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'event_title' => ['nullable', 'string', 'max:255'],
            'event_description' => ['nullable', 'string', 'max:10000'],
            'event_theme' => ['nullable', 'string', 'max:255'],
            'event_date' => ['nullable', 'string', 'max:255'],
            'event_location' => ['nullable', 'string', 'max:255'],
            'venue_room_name' => ['nullable', 'string', 'max:255'],
            'venue_room_title' => ['nullable', 'string', 'max:255'],
            'map_place_label' => ['nullable', 'string', 'max:255'],
            'map_address' => ['nullable', 'string', 'max:2000'],
            'map_open_url' => ['nullable', 'url', 'max:2000'],
            'map_embed_url' => ['nullable', 'url', 'max:2000'],
            'access_text' => ['nullable', 'string', 'max:10000'],
            'network_text' => ['nullable', 'string', 'max:255'],
            'timeline_json' => ['nullable', 'string'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'privacy_policy_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'rules_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'footer_brand' => ['nullable', 'string', 'max:255'],
            'footer_description' => ['nullable', 'string', 'max:10000'],
            'footer_copyright' => ['nullable', 'string', 'max:255'],
            'spectator_capacity' => ['required', 'integer', 'min:0', 'max:100000'],
            'candidate_capacity' => ['required', 'integer', 'min:0', 'max:100000'],
            'spectator_registrations_enabled' => ['nullable'],
            'candidate_registrations_enabled' => ['nullable'],
        ]);

        $data = Arr::except($validated, ['privacy_policy_pdf', 'rules_pdf', 'timeline_json']);

        $timeline = null;

        if (array_key_exists('timeline_json', $validated)) {
            $rawTimeline = trim((string) ($validated['timeline_json'] ?? ''));

            if ($rawTimeline !== '') {
                $decoded = json_decode($rawTimeline, true);

                if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
                    throw ValidationException::withMessages([
                        'timeline_json' => 'Timeline invalide (JSON attendu).',
                    ]);
                }

                $timeline = $decoded;
            }
        }

        if (request()->hasFile('privacy_policy_pdf')) {
            $oldPath = $settings->getRawOriginal('privacy_policy_url');

            if (! empty($oldPath) && ! $this->isExternalUrl($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            $settings->privacy_policy_url = Storage::disk('public')->putFile('legal', request()->file('privacy_policy_pdf'));
        }

        if (request()->hasFile('rules_pdf')) {
            $oldPath = $settings->getRawOriginal('rules_url');

            if (! empty($oldPath) && ! $this->isExternalUrl($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            $settings->rules_url = Storage::disk('public')->putFile('legal', request()->file('rules_pdf'));
        }

        $fill = [
            ...$data,
            'spectator_registrations_enabled' => (bool) request()->boolean('spectator_registrations_enabled'),
            'candidate_registrations_enabled' => (bool) request()->boolean('candidate_registrations_enabled'),
        ];

        if (array_key_exists('timeline_json', $validated)) {
            $fill['timeline'] = $timeline;
        }

        $settings->fill($fill);

        $settings->save();

        return back()->with('success', 'Paramètres mis à jour.');
    }

    private function isExternalUrl(string $value): bool
    {
        return str_starts_with($value, 'http://') || str_starts_with($value, 'https://');
    }
}
