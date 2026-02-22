<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CandidateRegistration;
use App\Models\EventSetting;
use App\Models\FoodOption;
use App\Models\SpectatorRegistration;
use Inertia\Inertia;
use Inertia\Response;

class RecapController extends Controller
{
    public function index(): Response
    {
        $settings = EventSetting::current();

        $spectatorRegistrations = SpectatorRegistration::query()
            ->latest()
            ->get();

        $spectatorSeatsUsed = (int) (SpectatorRegistration::query()
            ->selectRaw('COALESCE(SUM(1 + accompanying_count), 0) as seats_used')
            ->value('seats_used') ?? 0);

        $spectatorSeatsRemaining = max(0, (int) $settings->spectator_capacity - $spectatorSeatsUsed);

        $candidateRegistrations = CandidateRegistration::query()
            ->latest()
            ->get();

        $candidatesUsed = (int) $candidateRegistrations->count();
        $candidatesRemaining = max(0, (int) $settings->candidate_capacity - $candidatesUsed);

        $candidatesByFaculty = $candidateRegistrations
            ->groupBy(fn ($r) => (string) ($r->faculty ?? ''))
            ->map(fn ($items) => $items->count())
            ->sortDesc()
            ->values();

        $candidatesByYear = $candidateRegistrations
            ->groupBy(fn ($r) => (string) ($r->study_year ?? ''))
            ->map(fn ($items) => $items->count())
            ->sortDesc()
            ->values();

        $foodOptions = FoodOption::query()
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get();

        $orderedById = [];

        foreach ($foodOptions as $o) {
            $orderedById[$o->id] = 0;
        }

        $foodRegistrations = SpectatorRegistration::query()
            ->select(['food_option_id', 'food_quantities'])
            ->where(function ($query) {
                $query
                    ->whereNotNull('food_option_id')
                    ->orWhereNotNull('food_quantities');
            })
            ->get();

        foreach ($foodRegistrations as $r) {
            $quantities = $r->food_quantities;

            if (is_array($quantities)) {
                foreach ($quantities as $id => $qty) {
                    $intId = (int) $id;

                    if (! array_key_exists($intId, $orderedById)) {
                        continue;
                    }

                    $orderedById[$intId] += max(0, (int) $qty);
                }

                continue;
            }

            if (! empty($r->food_option_id) && array_key_exists((int) $r->food_option_id, $orderedById)) {
                $orderedById[(int) $r->food_option_id] += 1;
            }
        }

        $foodOptionsPayload = $foodOptions
            ->map(function (FoodOption $o) use ($orderedById) {
                return [
                    'id' => $o->id,
                    'label' => $o->label,
                    'is_active' => (bool) $o->is_active,
                    'ordered_quantity' => (int) ($orderedById[$o->id] ?? 0),
                ];
            })
            ->values();

        $totalFoodOrdered = (int) $foodOptionsPayload->sum('ordered_quantity');

        return Inertia::render('admin/Recap', [
            'settings' => $settings,
            'spectators' => [
                'registrations' => $spectatorRegistrations->map(function (SpectatorRegistration $r) {
                    return [
                        'id' => $r->id,
                        'created_at' => $r->created_at?->toDateTimeString(),
                        'full_name' => $r->full_name,
                        'email' => $r->email,
                        'phone' => $r->phone,
                        'accompanying_count' => (int) $r->accompanying_count,
                        'accompanying_people' => $r->accompanying_people,
                        'food_wanted' => (bool) $r->food_wanted,
                        'food_option_label' => $r->food_option_label,
                        'food_quantities' => $r->food_quantities,
                    ];
                })->values(),
                'seats_used' => $spectatorSeatsUsed,
                'seats_remaining' => $spectatorSeatsRemaining,
            ],
            'candidates' => [
                'registrations' => $candidateRegistrations->map(function (CandidateRegistration $r) {
                    return [
                        'id' => $r->id,
                        'created_at' => $r->created_at?->toDateTimeString(),
                        'full_name' => $r->full_name,
                        'email' => $r->email,
                        'phone' => $r->phone,
                        'faculty' => $r->faculty,
                        'study_year' => $r->study_year,
                    ];
                })->values(),
                'used' => $candidatesUsed,
                'remaining' => $candidatesRemaining,
                'by_faculty' => $candidateRegistrations
                    ->groupBy(fn ($r) => (string) ($r->faculty ?? ''))
                    ->map(fn ($items) => $items->count())
                    ->sortDesc(),
                'by_year' => $candidateRegistrations
                    ->groupBy(fn ($r) => (string) ($r->study_year ?? ''))
                    ->map(fn ($items) => $items->count())
                    ->sortDesc(),
            ],
            'food' => [
                'options' => $foodOptionsPayload,
                'total_ordered' => $totalFoodOrdered,
            ],
        ]);
    }
}
