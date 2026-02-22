<?php

namespace Database\Seeders;

use App\Models\AfterMovie;
use App\Models\CandidateRegistration;
use App\Models\EventSetting;
use App\Models\FoodOption;
use App\Models\JuryMember;
use App\Models\Partner;
use App\Models\PracticalModality;
use App\Models\SpectatorRegistration;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('fr_BE');
        $faker->seed(26022026);

        User::query()->firstOrCreate(
            ['email' => 'admin@consulex-elsa.be'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ],
        );

        $settings = EventSetting::current();

        $settings->fill([
            'event_edition_year' => (int) now()->format('Y'),
            'event_theme' => "Finale interfacultaire — Éloquence et conviction",
            'event_title' => "Une soirée d'éloquence, de talent et de passion.",
            'event_description' => "Concours d'éloquence organisé par ConsuLex ULiège x ELSA Liège.\nSoirée ouverte au public dans la limite des places disponibles.",
            'event_date' => now()->addWeeks(4)->format('d/m/Y'),
            'event_location' => 'ULiège — Sart Tilman',
            'venue_room_title' => 'Auditoire Central',
            'venue_room_name' => 'Bât. B7 — Niveau 1',
            'map_place_label' => 'ULiège — Sart Tilman',
            'map_address' => "Université de Liège\nQuartier du Sart Tilman\n4000 Liège",
            'map_open_url' => 'https://www.google.com/maps',
            'map_embed_url' => 'https://www.google.com/maps',
            'access_text' => "Bus + parking à proximité.\nArrive 20 minutes avant pour être bien placé.",
            'network_text' => 'ULiège',
            'instagram_url' => 'https://www.instagram.com/',
            'footer_brand' => "Concours d'éloquence - Consulex ULiège x ELSA Liège",
            'footer_description' => "ConsuLex ULiège x ELSA Liège",
            'footer_copyright' => "© " . now()->format('Y'),
            'spectator_registrations_enabled' => true,
            'candidate_registrations_enabled' => true,
        ]);

        $settings->timeline = [
            ['time' => '18:00', 'label' => 'Accueil', 'description' => 'Ouverture des portes et installation.'],
            ['time' => '19:00', 'label' => 'Début', 'description' => 'Présentation du concours et des règles.'],
            ['time' => '20:15', 'label' => 'Entracte', 'description' => "Pause et délibération intermédiaire."],
            ['time' => '21:30', 'label' => 'Remise des prix', 'description' => 'Annonce du gagnant et clôture.'],
        ];

        $settings->save();

        $foodOptions = collect([
            ['label' => 'Aucune', 'sort_order' => 1],
            ['label' => 'Sandwich — Classique', 'sort_order' => 2],
            ['label' => 'Sandwich — Végétarien', 'sort_order' => 3],
            ['label' => 'Sandwich — Vegan', 'sort_order' => 4],
        ])->map(function (array $payload) {
            return FoodOption::query()->firstOrCreate(
                ['label' => $payload['label']],
                ['sort_order' => $payload['sort_order'], 'is_active' => true],
            );
        })->values();

        PracticalModality::query()->delete();
        foreach ([
            ['icon_name' => 'MapPin', 'title' => 'Accès', 'description' => "Sart Tilman — bus, parking, signalétique."],
            ['icon_name' => 'Coffee', 'title' => 'Bar', 'description' => "Boissons disponibles avant et après la soirée."],
            ['icon_name' => 'Utensils', 'title' => 'Restauration', 'description' => "Option nourriture lors de l'inscription spectateurs."],
            ['icon_name' => 'Clock', 'title' => 'Horaires', 'description' => "Ouverture 18:00 — Début 19:00 — Fin 22:00."],
        ] as $idx => $modality) {
            PracticalModality::query()->create([
                'order' => $idx + 1,
                ...$modality,
            ]);
        }

        AfterMovie::query()->delete();
        for ($i = 0; $i < 6; $i++) {
            AfterMovie::query()->create([
                'date' => now()->subYears($i + 1)->subWeeks(2)->toDateString(),
                'location' => 'Liège',
                'theme' => 'Édition ' . (now()->subYears($i + 1)->format('Y')),
                'winner' => $faker->name(),
                'aftermovie_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            ]);
        }

        Partner::query()->delete();
        for ($i = 1; $i <= 12; $i++) {
            Partner::query()->create([
                'name' => 'Partenaire ' . $i,
                'url' => 'https://example.com',
                'description' => $faker->sentence(12),
                'logo_path' => 'https://placehold.co/256x256/png',
                'is_featured' => $i <= 8,
                'sort_order' => $i,
            ]);
        }

        JuryMember::query()->delete();
        for ($i = 1; $i <= 8; $i++) {
            JuryMember::query()->create([
                'name' => $faker->name(),
                'role' => $i === 1 ? 'Président·e du jury' : 'Membre du jury',
                'detail' => $faker->jobTitle(),
                'description' => $faker->paragraph(3),
                'photo_path' => 'https://placehold.co/640x640/jpg',
                'is_active' => true,
                'sort_order' => $i,
            ]);
        }

        // Inscriptions démo: on remplit à 100%.
        // Spectateurs: on crée des lignes dont (1 + accompanying_count) totalise exactement spectator_capacity.
        SpectatorRegistration::query()->delete();

        $targetSpectatorSeats = 200;
        $spectatorRows = 40;

        $remaining = $targetSpectatorSeats;
        for ($i = 1; $i <= $spectatorRows; $i++) {
            $isLast = $i === $spectatorRows;

            $maxAccompanying = min(4, max(0, $remaining - 1));
            $accompanyingCount = $isLast ? max(0, $remaining - 1) : random_int(0, $maxAccompanying);

            $seats = 1 + $accompanyingCount;
            $remaining -= $seats;

            if ($remaining < 0) {
                $accompanyingCount += $remaining;
                $remaining = 0;
            }

            $accompanyingPeople = [];
            for ($p = 0; $p < $accompanyingCount; $p++) {
                $accompanyingPeople[] = [
                    'first_name' => $faker->firstName(),
                    'last_name' => $faker->lastName(),
                ];
            }

            $foodWanted = (bool) random_int(0, 1);
            $foodOption = $foodWanted ? $foodOptions->random() : null;

            SpectatorRegistration::query()->create([
                'full_name' => $faker->name(),
                'email' => "spectateur{$i}@demo.local",
                'phone' => $faker->phoneNumber(),
                'accompanying_count' => $accompanyingCount,
                'accompanying_people' => $accompanyingCount > 0 ? $accompanyingPeople : null,
                'food_option_id' => $foodOption?->id,
                'food_option_label' => $foodOption?->label,
                'food_wanted' => $foodWanted,
                'food_quantities' => $foodWanted ? ['sandwich' => 1 + $accompanyingCount] : null,
                'accepted_rgpd' => true,
                'accepted_rules' => true,
            ]);

            if ($remaining === 0) {
                break;
            }
        }

        $spectatorSeatsUsed = (int) (SpectatorRegistration::query()
            ->selectRaw('COALESCE(SUM(1 + accompanying_count), 0) as seats_used')
            ->value('seats_used') ?? 0);

        // Candidats: on crée exactement candidate_capacity lignes.
        CandidateRegistration::query()->delete();

        $targetCandidates = 40;
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

        $studyYears = ['BAC 1', 'BAC 2', 'BAC 3', 'MASTER 0', 'MASTER 1', 'MASTER 2', 'MASTER 3', 'MASTER DE SPE', 'DOCTORAT', 'ERASMUS'];

        for ($i = 1; $i <= $targetCandidates; $i++) {
            $textPath = "candidates/texts/demo-{$i}.pdf";
            $proofPath = "candidates/proofs/demo-{$i}.pdf";

            $pdf = "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\ntrailer\n<<>>\n%%EOF\n";
            Storage::disk('local')->put($textPath, $pdf);
            Storage::disk('local')->put($proofPath, $pdf);

            CandidateRegistration::query()->create([
                'full_name' => $faker->name(),
                'email' => "candidat{$i}@demo.local",
                'phone' => $faker->phoneNumber(),
                'faculty' => $faculties[array_rand($faculties)],
                'study_year' => $studyYears[array_rand($studyYears)],
                'text_pdf_path' => $textPath,
                'photo_path' => $proofPath,
                'accepted_rgpd' => true,
                'accepted_rules' => true,
            ]);
        }

        $settings->refresh();
        $settings->spectator_capacity = max(0, $spectatorSeatsUsed);
        $settings->candidate_capacity = $targetCandidates;
        $settings->spectator_registrations_enabled = true;
        $settings->candidate_registrations_enabled = true;
        $settings->save();
    }
}
