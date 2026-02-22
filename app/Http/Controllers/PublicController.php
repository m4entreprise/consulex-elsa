<?php

namespace App\Http\Controllers;

use App\Models\AfterMovie;
use App\Models\EventSetting;
use App\Models\FoodOption;
use App\Models\JuryMember;
use App\Models\Partner;
use App\Models\PracticalModality;
use App\Models\SpectatorRegistration;
use App\Models\CandidateRegistration;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PublicController extends Controller
{
    public function home(): Response
    {
        $settings = EventSetting::current();

        $afterMovies = AfterMovie::query()
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        $featuredPartners = Partner::query()
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit(12)
            ->get();

        $partners = Partner::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $juryMembers = JuryMember::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit(10)
            ->get();

        $practicalModalities = PracticalModality::query()
            ->orderBy('order')
            ->get();

        return Inertia::render('public/Home', [
            'settings' => $settings,
            'afterMovies' => $afterMovies,
            'featuredPartners' => $featuredPartners,
            'partners' => $partners,
            'juryMembers' => $juryMembers,
            'practicalModalities' => $practicalModalities,
        ]);
    }

    public function partners(): Response
    {
        $settings = EventSetting::current();

        $partners = Partner::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return Inertia::render('public/Partners', [
            'settings' => $settings,
            'partners' => $partners,
        ]);
    }

    public function legalNotice(): RedirectResponse|Response
    {
        $settings = EventSetting::current();

        if (! empty($settings->rules_url)) {
            return redirect()->to($settings->rules_url);
        }

        return Inertia::render('public/LegalNotice', [
            'settings' => $settings,
        ]);
    }

    public function privacy(): RedirectResponse|Response
    {
        $settings = EventSetting::current();

        if (! empty($settings->privacy_policy_url)) {
            return redirect()->to($settings->privacy_policy_url);
        }

        return Inertia::render('public/Privacy', [
            'settings' => $settings,
        ]);
    }

    public function spectators(): Response
    {
        $settings = EventSetting::current();

        $foodOptions = FoodOption::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get();

        $seatsUsed = (int) (SpectatorRegistration::query()
            ->selectRaw('COALESCE(SUM(1 + accompanying_count), 0) as seats_used')
            ->value('seats_used') ?? 0);

        $seatsRemaining = max(0, (int) $settings->spectator_capacity - $seatsUsed);

        return Inertia::render('public/RegisterSpectators', [
            'settings' => $settings,
            'foodOptions' => $foodOptions,
            'seatsUsed' => $seatsUsed,
            'seatsRemaining' => $seatsRemaining,
        ]);
    }

    public function candidates(): Response
    {
        $settings = EventSetting::current();

        $candidatesUsed = (int) CandidateRegistration::query()->count();
        $candidatesRemaining = max(0, (int) $settings->candidate_capacity - $candidatesUsed);

        return Inertia::render('public/RegisterCandidates', [
            'settings' => $settings,
            'candidatesUsed' => $candidatesUsed,
            'candidatesRemaining' => $candidatesRemaining,
        ]);
    }
}
