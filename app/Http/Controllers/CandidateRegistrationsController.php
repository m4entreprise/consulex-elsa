<?php

namespace App\Http\Controllers;

use App\Models\CandidateRegistration;
use App\Models\EventSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CandidateRegistrationsController extends Controller
{
    public function store(): RedirectResponse
    {
        $validated = request()->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:candidate_registrations,email'],
            'phone' => ['required', 'string', 'max:50'],
            'faculty' => ['required', 'string', 'max:255'],
            'text_pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'photo' => ['required', 'file', 'image', 'max:5120'],
            'accepted_rgpd' => ['accepted'],
            'accepted_rules' => ['accepted'],
        ]);

        if (! EventSetting::current()->candidate_registrations_enabled) {
            throw ValidationException::withMessages([
                'full_name' => "Les inscriptions candidats sont clôturées.",
            ]);
        }

        DB::transaction(function () use ($validated) {
            $settings = EventSetting::query()
                ->where('key', 'default')
                ->lockForUpdate()
                ->firstOrCreate(['key' => 'default']);

            if (! $settings->candidate_registrations_enabled) {
                throw ValidationException::withMessages([
                    'full_name' => "Les inscriptions candidats sont clôturées.",
                ]);
            }

            $used = (int) CandidateRegistration::query()->count();

            if ($used >= (int) $settings->candidate_capacity) {
                throw ValidationException::withMessages([
                    'full_name' => "Le quota candidats est atteint.",
                ]);
            }

            $textPath = Storage::disk('local')->putFile('candidates/texts', request()->file('text_pdf'));
            $photoPath = Storage::disk('local')->putFile('candidates/photos', request()->file('photo'));

            CandidateRegistration::query()->create([
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'faculty' => $validated['faculty'],
                'text_pdf_path' => $textPath,
                'photo_path' => $photoPath,
                'accepted_rgpd' => true,
                'accepted_rules' => true,
            ]);
        });

        return to_route('registrations.candidates')->with('success', "Inscription candidat enregistrée.");
    }
}
