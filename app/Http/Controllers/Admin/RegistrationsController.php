<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CandidateRegistration;
use App\Models\EventSetting;
use App\Models\SpectatorRegistration;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class RegistrationsController extends Controller
{
    public function spectators(): Response
    {
        $settings = EventSetting::current();

        $registrations = SpectatorRegistration::query()
            ->latest()
            ->get();

        $seatsUsed = (int) (SpectatorRegistration::query()
            ->selectRaw('COALESCE(SUM(1 + accompanying_count), 0) as seats_used')
            ->value('seats_used') ?? 0);

        return Inertia::render('admin/RegistrationsSpectators', [
            'settings' => $settings,
            'registrations' => $registrations,
            'seatsUsed' => $seatsUsed,
        ]);
    }

    public function candidates(): Response
    {
        $settings = EventSetting::current();

        $registrations = CandidateRegistration::query()
            ->latest()
            ->get();

        return Inertia::render('admin/RegistrationsCandidates', [
            'settings' => $settings,
            'registrations' => $registrations,
        ]);
    }

    public function exportSpectators(): HttpResponse
    {
        $rows = SpectatorRegistration::query()->orderBy('created_at')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="spectateurs.csv"',
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');

            fprintf($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'date',
                'nom_prenom',
                'email',
                'telephone',
                'accompagnants',
                'places_total',
                'nourriture',
            ], ';');

            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->created_at?->toDateTimeString(),
                    $r->full_name,
                    $r->email,
                    $r->phone,
                    $r->accompanying_count,
                    1 + (int) $r->accompanying_count,
                    $r->food_option_label,
                ], ';');
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportCandidates(): HttpResponse
    {
        $rows = CandidateRegistration::query()->orderBy('created_at')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="candidats.csv"',
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');

            fprintf($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'date',
                'nom_prenom',
                'email',
                'telephone',
                'faculte',
                'pdf_path',
                'photo_path',
            ], ';');

            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->created_at?->toDateTimeString(),
                    $r->full_name,
                    $r->email,
                    $r->phone,
                    $r->faculty,
                    $r->text_pdf_path,
                    $r->photo_path,
                ], ';');
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadCandidateText(CandidateRegistration $candidateRegistration)
    {
        return Storage::disk('local')->download($candidateRegistration->text_pdf_path);
    }

    public function downloadCandidatePhoto(CandidateRegistration $candidateRegistration)
    {
        return Storage::disk('local')->download($candidateRegistration->photo_path);
    }
}
