<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CandidateRegistration;
use App\Models\EventSetting;
use App\Models\FoodOption;
use App\Models\SpectatorRegistration;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $settings = EventSetting::current();

        $seatsUsed = (int) (SpectatorRegistration::query()
            ->selectRaw('COALESCE(SUM(1 + accompanying_count), 0) as seats_used')
            ->value('seats_used') ?? 0);

        $spectatorRemaining = max(0, (int) $settings->spectator_capacity - $seatsUsed);

        $candidatesUsed = (int) CandidateRegistration::query()->count();
        $candidatesRemaining = max(0, (int) $settings->candidate_capacity - $candidatesUsed);

        $foodOptionsActive = (int) FoodOption::query()->where('is_active', true)->count();

        return Inertia::render('Dashboard', [
            'settings' => $settings,
            'stats' => [
                'seatsUsed' => $seatsUsed,
                'spectatorRemaining' => $spectatorRemaining,
                'candidatesUsed' => $candidatesUsed,
                'candidatesRemaining' => $candidatesRemaining,
                'foodOptionsActive' => $foodOptionsActive,
            ],
        ]);
    }
}
