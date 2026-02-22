<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CandidateRegistration;
use App\Models\EventSetting;
use App\Models\SpectatorRegistration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

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

    public function exportSpectators(): StreamedResponse
    {
        $rows = SpectatorRegistration::query()->orderBy('created_at')->get();

        $maxAccompanying = $rows->max('accompanying_count') ?? 0;

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="spectateurs.csv"',
        ];

        $callback = function () use ($rows, $maxAccompanying) {
            $out = fopen('php://output', 'w');

            fprintf($out, "\xEF\xBB\xBF");

            $headerRow = [
                'date',
                'nom_prenom',
                'email',
                'telephone',
                'accompagnants',
                'places_total',
                'nourriture',
            ];

            for ($i = 1; $i <= $maxAccompanying; $i++) {
                $headerRow[] = "accompagnant_{$i}_prenom_nom";
            }

            fputcsv($out, $headerRow, ';');

            foreach ($rows as $r) {
                $row = [
                    $r->created_at?->toDateTimeString(),
                    $r->full_name,
                    $r->email,
                    $r->phone,
                    $r->accompanying_count,
                    1 + (int) $r->accompanying_count,
                    $r->food_option_label,
                ];

                $accompanyingPeople = is_array($r->accompanying_people) ? $r->accompanying_people : [];

                for ($i = 1; $i <= $maxAccompanying; $i++) {
                    $person = $accompanyingPeople[$i - 1] ?? null;
                    if ($person) {
                        $row[] = trim(($person['first_name'] ?? '') . ' ' . ($person['last_name'] ?? ''));
                    } else {
                        $row[] = '';
                    }
                }

                fputcsv($out, $row, ';');
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportCandidates(): StreamedResponse
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
                'annee',
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
                    $r->study_year,
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

    public function downloadCandidatesArchive(): BinaryFileResponse
    {
        $rows = CandidateRegistration::query()
            ->orderBy('created_at')
            ->get(['id', 'full_name', 'text_pdf_path', 'photo_path']);

        $tmpPath = tempnam(sys_get_temp_dir(), 'candidates_zip_');

        if ($tmpPath === false) {
            abort(500, "Impossible de créer l'archive.");
        }

        $zipPath = $tmpPath.'.zip';
        @rename($tmpPath, $zipPath);

        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, "Impossible d'ouvrir l'archive.");
        }

        foreach ($rows as $r) {
            $base = Str::of((string) $r->full_name)
                ->trim()
                ->ascii()
                ->upper()
                ->replaceMatches('/\s+/', '-')
                ->replaceMatches('/[^A-Z0-9\-]/', '')
                ->trim('-');

            $baseName = $base->toString() !== '' ? $base->toString() : 'CANDIDAT-'.$r->id;
            $folder = $baseName;

            if (! empty($r->text_pdf_path) && Storage::disk('local')->exists($r->text_pdf_path)) {
                $zip->addFile(
                    Storage::disk('local')->path($r->text_pdf_path),
                    $folder.'/'.$baseName.'-TEXTE.pdf'
                );
            }

            if (! empty($r->photo_path) && Storage::disk('local')->exists($r->photo_path)) {
                $zip->addFile(
                    Storage::disk('local')->path($r->photo_path),
                    $folder.'/'.$baseName.'-ATTESTATION-INSCRIPTION.pdf'
                );
            }
        }

        $zip->close();

        $filename = 'candidats-archive-'.now()->format('Ymd-His').'.zip';

        return response()->download($zipPath, $filename)->deleteFileAfterSend(true);
    }

    public function destroySpectator(SpectatorRegistration $spectatorRegistration): RedirectResponse
    {
        $spectatorRegistration->delete();

        return redirect()
            ->back()
            ->with('success', 'Inscription spectateur supprimée.');
    }

    public function destroyCandidate(CandidateRegistration $candidateRegistration): RedirectResponse
    {
        if (!empty($candidateRegistration->text_pdf_path) && Storage::disk('local')->exists($candidateRegistration->text_pdf_path)) {
            Storage::disk('local')->delete($candidateRegistration->text_pdf_path);
        }

        if (!empty($candidateRegistration->photo_path) && Storage::disk('local')->exists($candidateRegistration->photo_path)) {
            Storage::disk('local')->delete($candidateRegistration->photo_path);
        }

        $candidateRegistration->delete();

        return redirect()
            ->back()
            ->with('success', 'Inscription candidat supprimée.');
    }
}
