<?php

namespace App\Http\Controllers;

use App\Models\EventSetting;
use App\Models\FoodOption;
use App\Models\SpectatorRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SpectatorRegistrationsController extends Controller
{
    public function store(): RedirectResponse
    {
        $validated = request()->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'accompanying_count' => ['nullable', 'integer', 'min:0', 'max:5'],
            'food_option_id' => ['nullable', 'integer', 'exists:food_options,id'],
            'accepted_rgpd' => ['accepted'],
            'accepted_rules' => ['accepted'],
        ]);

        if (! EventSetting::current()->spectator_registrations_enabled) {
            throw ValidationException::withMessages([
                'full_name' => "Les inscriptions spectateurs sont clôturées.",
            ]);
        }

        $accompanying = (int) ($validated['accompanying_count'] ?? 0);
        $seatsRequested = 1 + $accompanying;

        DB::transaction(function () use ($validated, $seatsRequested) {
            $settings = EventSetting::query()
                ->where('key', 'default')
                ->lockForUpdate()
                ->firstOrCreate(['key' => 'default']);

            if (! $settings->spectator_registrations_enabled) {
                throw ValidationException::withMessages([
                    'full_name' => "Les inscriptions spectateurs sont clôturées.",
                ]);
            }

            $seatsUsed = (int) (SpectatorRegistration::query()
                ->selectRaw('COALESCE(SUM(1 + accompanying_count), 0) as seats_used')
                ->value('seats_used') ?? 0);

            $seatsRemaining = (int) $settings->spectator_capacity - $seatsUsed;

            if ($seatsRemaining < $seatsRequested) {
                throw ValidationException::withMessages([
                    'full_name' => "Plus assez de places disponibles.",
                ]);
            }

            $foodOptionLabel = null;

            if (! empty($validated['food_option_id'])) {
                $foodOptionLabel = FoodOption::query()->whereKey($validated['food_option_id'])->value('label');
            }

            SpectatorRegistration::query()->create([
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'accompanying_count' => (int) ($validated['accompanying_count'] ?? 0),
                'food_option_id' => $validated['food_option_id'] ?? null,
                'food_option_label' => $foodOptionLabel,
                'accepted_rgpd' => true,
                'accepted_rules' => true,
            ]);
        });

        return to_route('registrations.spectators')->with('success', "Inscription spectateur enregistrée.");
    }
}
