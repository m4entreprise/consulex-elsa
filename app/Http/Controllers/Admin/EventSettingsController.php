<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventSetting;
use Illuminate\Http\RedirectResponse;
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
            'event_title' => ['nullable', 'string', 'max:255'],
            'event_theme' => ['nullable', 'string', 'max:255'],
            'event_date' => ['nullable', 'string', 'max:255'],
            'event_location' => ['nullable', 'string', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'aftermovie_url' => ['nullable', 'url', 'max:255'],
            'privacy_policy_url' => ['nullable', 'url', 'max:255'],
            'rules_url' => ['nullable', 'url', 'max:255'],
            'spectator_capacity' => ['required', 'integer', 'min:0', 'max:100000'],
            'candidate_capacity' => ['required', 'integer', 'min:0', 'max:100000'],
            'spectator_registrations_enabled' => ['nullable'],
            'candidate_registrations_enabled' => ['nullable'],
        ]);

        $settings->fill([
            ...$validated,
            'spectator_registrations_enabled' => (bool) request()->boolean('spectator_registrations_enabled'),
            'candidate_registrations_enabled' => (bool) request()->boolean('candidate_registrations_enabled'),
        ]);

        $settings->save();

        return back()->with('success', 'Paramètres mis à jour.');
    }
}
