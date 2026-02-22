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
            'accompanying_people' => ['nullable', 'array'],
            'accompanying_people.*.first_name' => ['nullable', 'string', 'max:255'],
            'accompanying_people.*.last_name' => ['nullable', 'string', 'max:255'],
            'food_option_id' => ['nullable', 'integer', 'exists:food_options,id'],
            'food_wanted' => ['nullable'],
            'food_quantities' => ['nullable', 'array'],
            'food_quantities.*' => ['nullable', 'integer', 'min:0', 'max:20'],
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

        $accompanyingPeople = $validated['accompanying_people'] ?? null;

        if ($accompanying > 0) {
            if (! is_array($accompanyingPeople) || count($accompanyingPeople) !== $accompanying) {
                throw ValidationException::withMessages([
                    'accompanying_people' => "Renseigne le nom et le prénom de chaque accompagnant.",
                ]);
            }

            $cleanedPeople = [];

            foreach (array_values($accompanyingPeople) as $index => $person) {
                $firstName = trim((string) ($person['first_name'] ?? ''));
                $lastName = trim((string) ($person['last_name'] ?? ''));

                if ($firstName === '') {
                    throw ValidationException::withMessages([
                        "accompanying_people.$index.first_name" => "Prénom requis.",
                    ]);
                }

                if ($lastName === '') {
                    throw ValidationException::withMessages([
                        "accompanying_people.$index.last_name" => "Nom requis.",
                    ]);
                }

                $cleanedPeople[] = [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                ];
            }

            $accompanyingPeople = $cleanedPeople;
        } else {
            $accompanyingPeople = null;
        }

        $foodWanted = (bool) request()->boolean('food_wanted');
        $foodQuantities = $validated['food_quantities'] ?? [];

        if (! $foodWanted) {
            $foodQuantities = [];
        }

        if (! is_array($foodQuantities)) {
            $foodQuantities = [];
        }

        $foodQuantities = collect($foodQuantities)
            ->mapWithKeys(fn ($qty, $id) => [(int) $id => (int) $qty])
            ->filter(fn ($qty) => $qty > 0)
            ->all();

        if ($foodWanted && count($foodQuantities) === 0 && FoodOption::query()->where('is_active', true)->exists()) {
            throw ValidationException::withMessages([
                'food_quantities' => 'Choisis au moins une quantité.',
            ]);
        }

        DB::transaction(function () use ($validated, $seatsRequested, $accompanyingPeople, $foodWanted, $foodQuantities) {
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

            $foodOptionId = $validated['food_option_id'] ?? null;
            $foodOptionLabel = null;

            if ($foodWanted) {
                $ids = array_keys($foodQuantities);

                if (count($ids) > 0) {
                    $labels = FoodOption::query()
                        ->where('is_active', true)
                        ->whereIn('id', $ids)
                        ->pluck('label', 'id');

                    if ($labels->count() !== count($ids)) {
                        throw ValidationException::withMessages([
                            'food_quantities' => "Liste nourriture invalide.",
                        ]);
                    }

                    $parts = [];

                    foreach ($labels as $id => $label) {
                        $qty = (int) ($foodQuantities[(int) $id] ?? 0);
                        if ($qty <= 0) continue;
                        $parts[] = trim($label).' x'.$qty;
                    }

                    $foodOptionLabel = count($parts) ? implode(', ', $parts) : null;
                }

                $foodOptionId = null;
            } elseif (! empty($foodOptionId)) {
                $foodOptionLabel = FoodOption::query()->whereKey($foodOptionId)->value('label');
            }

            SpectatorRegistration::query()->create([
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'accompanying_count' => (int) ($validated['accompanying_count'] ?? 0),
                'accompanying_people' => $accompanyingPeople,
                'food_option_id' => $foodOptionId,
                'food_option_label' => $foodOptionLabel,
                'food_wanted' => $foodWanted,
                'food_quantities' => $foodWanted ? $foodQuantities : null,
                'accepted_rgpd' => true,
                'accepted_rules' => true,
            ]);
        });

        return to_route('registrations.spectators')->with('success', "Inscription spectateur enregistrée.");
    }
}
