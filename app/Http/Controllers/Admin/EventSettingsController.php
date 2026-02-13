<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
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
            'privacy_policy_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'rules_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'spectator_capacity' => ['required', 'integer', 'min:0', 'max:100000'],
            'candidate_capacity' => ['required', 'integer', 'min:0', 'max:100000'],
            'spectator_registrations_enabled' => ['nullable'],
            'candidate_registrations_enabled' => ['nullable'],
        ]);

        $data = Arr::except($validated, ['privacy_policy_pdf', 'rules_pdf']);

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

        $settings->fill([
            ...$data,
            'spectator_registrations_enabled' => (bool) request()->boolean('spectator_registrations_enabled'),
            'candidate_registrations_enabled' => (bool) request()->boolean('candidate_registrations_enabled'),
        ]);

        $settings->save();

        return back()->with('success', 'Paramètres mis à jour.');
    }

    private function isExternalUrl(string $value): bool
    {
        return str_starts_with($value, 'http://') || str_starts_with($value, 'https://');
    }
}
