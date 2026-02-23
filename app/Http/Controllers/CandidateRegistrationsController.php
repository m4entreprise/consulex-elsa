<?php

namespace App\Http\Controllers;

use App\Models\CandidateRegistration;
use App\Models\EventSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CandidateRegistrationsController extends Controller
{
    public function store(): RedirectResponse
    {
        $currentSettings = EventSetting::current();

        if ($currentSettings->candidate_custom_form_enabled && ! empty($currentSettings->candidate_custom_form_url)) {
            throw ValidationException::withMessages([
                'first_name' => "Les inscriptions candidats se font via un formulaire externe.",
            ]);
        }

        $faculties = [
            'Faculté de Philosophie et Lettres',
            'Faculté de Droit, Science politique et Criminologie',
            'Faculté des Sciences',
            'Faculté de Médecine',
            'Faculté des Sciences Appliquées',
            'Faculté de Médecine Vétérinaire',
            "Faculté de Psychologie, Logopédie et Sciences de l'Education",
            'HEC Liège - Ecole de Gestion',
            'Faculté des Sciences Sociales',
            'Faculté de Gembloux Agro-Bio Tech',
            "Faculté d'Architecture",
        ];

        $studyYears = [
            'BAC 1',
            'BAC 2',
            'BAC 3',
            'MASTER 0',
            'MASTER 1',
            'MASTER 2',
            'MASTER 3',
            'MASTER DE SPE',
            'DOCTORAT',
            'ERASMUS',
        ];

        $validated = request()->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:candidate_registrations,email'],
            'phone' => ['required', 'string', 'max:50'],
            'faculty' => ['required', 'string', 'max:255', Rule::in($faculties)],
            'study_year' => ['required', 'string', 'max:50', Rule::in($studyYears)],
            'text_pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'proof_pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'accepted_rgpd' => ['accepted'],
            'accepted_rules' => ['accepted'],
        ]);

        if (! $currentSettings->candidate_registrations_enabled) {
            throw ValidationException::withMessages([
                'first_name' => "Les inscriptions candidats sont clôturées.",
            ]);
        }

        $firstName = trim((string) $validated['first_name']);
        $lastName = trim((string) $validated['last_name']);
        $fullName = trim($firstName.' '.$lastName);

        DB::transaction(function () use ($validated, $fullName) {
            $settings = EventSetting::query()
                ->where('key', 'default')
                ->lockForUpdate()
                ->firstOrCreate(['key' => 'default']);

            if (! $settings->candidate_registrations_enabled) {
                throw ValidationException::withMessages([
                    'first_name' => "Les inscriptions candidats sont clôturées.",
                ]);
            }

            $used = (int) CandidateRegistration::query()->count();

            if ($used >= (int) $settings->candidate_capacity) {
                throw ValidationException::withMessages([
                    'first_name' => "Le quota candidats est atteint.",
                ]);
            }

            $textPath = Storage::disk('local')->putFile('candidates/texts', request()->file('text_pdf'));
            $proofPath = Storage::disk('local')->putFile('candidates/proofs', request()->file('proof_pdf'));

            CandidateRegistration::query()->create([
                'full_name' => $fullName,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'faculty' => $validated['faculty'],
                'study_year' => $validated['study_year'],
                'text_pdf_path' => $textPath,
                'photo_path' => $proofPath,
                'accepted_rgpd' => true,
                'accepted_rules' => true,
            ]);
        });

        return to_route('registrations.candidates')->with('success', "Inscription candidat enregistrée.");
    }
}
