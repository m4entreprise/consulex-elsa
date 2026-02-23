<script setup lang="ts">
import { Form, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import PublicLayout from '@/layouts/PublicLayout.vue';

type Settings = {
    candidate_capacity: number;
    candidate_registrations_enabled: boolean;
    spectator_custom_form_enabled?: boolean;
    spectator_custom_form_url?: string | null;
    privacy_policy_url: string | null;
    rules_url: string | null;
    event_title?: string | null;
    event_description?: string | null;
    event_theme?: string | null;
    event_date?: string | null;
    event_location?: string | null;
    venue_room_name?: string | null;
    venue_room_title?: string | null;
    map_place_label?: string | null;
    map_address?: string | null;
    map_open_url?: string | null;
    access_text?: string | null;
    network_text?: string | null;
    timeline?: Array<{ time?: string; label?: string; description?: string }> | null;
};

const props = defineProps<{
    settings: Settings;
    candidatesUsed: number;
    candidatesRemaining: number;
}>();

const isClosed = computed(() => !props.settings.candidate_registrations_enabled || props.candidatesRemaining === 0);

const spectatorRegistrationUrl = computed(() => {
    const s = props.settings as any;
    if (s?.spectator_custom_form_enabled && s?.spectator_custom_form_url) {
        return String(s.spectator_custom_form_url);
    }
    return '/inscription/spectateurs';
});

const spectatorRegistrationIsExternal = computed(() => spectatorRegistrationUrl.value.startsWith('http'));

const faculties = [
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

const studyYears = ['BAC 1', 'BAC 2', 'BAC 3', 'MASTER 0', 'MASTER 1', 'MASTER 2', 'MASTER 3', 'MASTER DE SPE', 'DOCTORAT', 'ERASMUS'];

const timelineSteps = computed(() => {
    if (!Array.isArray(props.settings.timeline)) return [];

    return props.settings.timeline
        .map((s) => ({
            time: typeof s?.time === 'string' ? s.time : '',
            label: typeof s?.label === 'string' ? s.label : '',
            description: typeof s?.description === 'string' ? s.description : '',
        }))
        .filter((s) => s.time || s.label || s.description);
});
</script>

<template>
    <PublicLayout title="Inscription Candidats" :container="false">
        <section class="relative overflow-hidden bg-[#f7f4ee] py-16 sm:py-20 lg:py-24">
            <div class="absolute inset-0" aria-hidden="true">
                <div
                    class="absolute inset-0 bg-[radial-gradient(1200px_circle_at_18%_12%,rgba(15,23,42,0.06),transparent_58%),radial-gradient(900px_circle_at_88%_6%,rgba(180,83,9,0.08),transparent_62%)]"
                />
                <div
                    class="absolute inset-0 bg-[linear-gradient(to_bottom,rgba(247,244,238,0.62),rgba(247,244,238,0.86),rgba(247,244,238,0.96))]"
                />
            </div>

            <div class="relative mx-auto max-w-7xl px-4">
                <div class="grid gap-10 lg:grid-cols-12 lg:items-start">
                    <div class="lg:col-span-5">
                        <div class="inline-flex items-center rounded-full border border-slate-900/10 bg-white/55 px-4 py-2 text-[11px] font-semibold tracking-[0.34em] text-slate-700 shadow-sm">
                            INSCRIPTION
                        </div>

                        <h1
                            class="mt-5 break-words text-balance leading-[0.98] tracking-[-0.02em] text-slate-950 sm:leading-[0.92]"
                            style="font-family: 'Playfair Display', ui-serif, Georgia, serif; font-size: clamp(2.2rem, 7vw, 4.75rem);"
                        >
                            Candidats
                        </h1>

                        <p class="mt-4 max-w-xl text-sm font-medium leading-relaxed text-slate-700 sm:text-base">
                            Dépose ton texte (PDF) et ta preuve d'inscription à l'ULG (PDF).
                        </p>

                        <div class="mt-8 grid gap-4">
                            <div class="rounded-2xl border border-slate-900/10 bg-white/55 p-5 shadow-sm">
                                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-600">Événement</div>

                                <div class="mt-3 grid gap-3">
                                    <div v-if="settings.event_theme" class="text-base font-semibold text-slate-950">
                                        {{ settings.event_theme }}
                                    </div>
                                    <div v-if="settings.event_title" class="text-sm text-slate-700">
                                        {{ settings.event_title }}
                                    </div>
                                    <div v-if="settings.event_description" class="text-sm leading-relaxed text-slate-600">
                                        {{ settings.event_description }}
                                    </div>

                                    <div class="grid gap-2 text-sm text-slate-700">
                                        <div v-if="settings.event_date" class="flex items-start justify-between gap-3">
                                            <div class="text-slate-600">Date</div>
                                            <div class="font-medium text-slate-950">{{ settings.event_date }}</div>
                                        </div>
                                        <div v-if="settings.event_location" class="flex items-start justify-between gap-3">
                                            <div class="text-slate-600">Lieu</div>
                                            <div class="font-medium text-slate-950">{{ settings.event_location }}</div>
                                        </div>
                                        <div v-if="settings.venue_room_title || settings.venue_room_name" class="flex items-start justify-between gap-3">
                                            <div class="text-slate-600">Salle</div>
                                            <div class="text-right font-medium text-slate-950">
                                                <div v-if="settings.venue_room_title">{{ settings.venue_room_title }}</div>
                                                <div v-if="settings.venue_room_name" class="text-sm font-normal text-slate-700">{{ settings.venue_room_name }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="settings.map_address || settings.map_open_url || settings.access_text || settings.network_text" class="rounded-2xl border border-slate-900/10 bg-white/55 p-5 shadow-sm">
                                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-600">Infos pratiques</div>

                                <div class="mt-3 grid gap-4">
                                    <div v-if="settings.map_address || settings.map_place_label" class="grid gap-1">
                                        <div class="text-sm font-semibold text-slate-950">
                                            {{ settings.map_place_label || 'Adresse' }}
                                        </div>
                                        <div v-if="settings.map_address" class="text-sm whitespace-pre-line text-slate-700">
                                            {{ settings.map_address }}
                                        </div>
                                        <a
                                            v-if="settings.map_open_url"
                                            :href="settings.map_open_url"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="mt-2 inline-flex w-fit items-center rounded-full border border-slate-900/15 bg-white/70 px-4 py-2 text-xs font-semibold text-slate-900 shadow-sm hover:bg-white"
                                        >
                                            Ouvrir sur la carte
                                        </a>
                                    </div>

                                    <div v-if="settings.access_text" class="grid gap-1">
                                        <div class="text-sm font-semibold text-slate-950">Accès</div>
                                        <div class="text-sm whitespace-pre-line leading-relaxed text-slate-700">
                                            {{ settings.access_text }}
                                        </div>
                                    </div>

                                    <div v-if="settings.network_text" class="grid gap-1">
                                        <div class="text-sm font-semibold text-slate-950">Réseau</div>
                                        <div class="text-sm leading-relaxed text-slate-700">
                                            {{ settings.network_text }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="timelineSteps.length" class="rounded-2xl border border-slate-900/10 bg-white/55 p-5 shadow-sm">
                                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-600">Timeline</div>

                                <div class="mt-3 grid gap-3">
                                    <div v-for="(s, idx) in timelineSteps" :key="idx" class="rounded-xl border border-slate-900/10 bg-white/45 p-4">
                                        <div class="flex flex-wrap items-baseline justify-between gap-2">
                                            <div class="text-sm font-semibold text-slate-950">
                                                {{ s.label || `Étape ${idx + 1}` }}
                                            </div>
                                            <div v-if="s.time" class="text-xs font-semibold tracking-wide text-slate-600">
                                                {{ s.time }}
                                            </div>
                                        </div>
                                        <div v-if="s.description" class="mt-2 text-sm whitespace-pre-line leading-relaxed text-slate-700">
                                            {{ s.description }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 text-sm text-slate-600">
                            Tu veux juste assister ?
                            <a
                                v-if="spectatorRegistrationIsExternal"
                                :href="spectatorRegistrationUrl"
                                class="text-slate-700 underline underline-offset-4 hover:text-slate-950"
                            >
                                Inscription spectateurs
                            </a>
                            <Link
                                v-else
                                :href="spectatorRegistrationUrl"
                                class="text-slate-700 underline underline-offset-4 hover:text-slate-950"
                            >
                                Inscription spectateurs
                            </Link>
                        </div>
                    </div>

                    <div class="lg:col-span-7">
                        <div class="overflow-hidden rounded-2xl border border-slate-900/10 bg-white/55 shadow-sm">
                            <div class="border-b border-slate-900/10 px-5 py-4">
                                <div class="text-[11px] uppercase tracking-[0.24em] text-slate-600">Formulaire</div>
                                <div class="mt-2 text-base font-semibold text-slate-950">
                                    Informations
                                </div>
                            </div>

                            <div class="p-5 sm:p-6">
                                <div
                                    v-if="isClosed"
                                    class="mb-5 rounded-2xl border border-red-900/10 bg-red-500/10 p-4 text-sm text-red-900"
                                >
                                    Les inscriptions candidats sont clôturées.
                                </div>

                            <Form
                                action="/inscription/candidats"
                                method="post"
                                enctype="multipart/form-data"
                                class="grid gap-5"
                                v-slot="{ errors, processing }"
                                :options="{ preserveScroll: true }"
                            >
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="grid gap-2">
                                        <Label for="first_name" class="text-sm font-semibold text-slate-950">Prénom</Label>
                                        <Input
                                            id="first_name"
                                            name="first_name"
                                            required
                                            class="border-slate-900/10 bg-white/55 text-slate-900 placeholder:text-slate-500 shadow-sm"
                                        />
                                        <InputError :message="errors.first_name" />
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="last_name" class="text-sm font-semibold text-slate-950">Nom</Label>
                                        <Input
                                            id="last_name"
                                            name="last_name"
                                            required
                                            class="border-slate-900/10 bg-white/55 text-slate-900 placeholder:text-slate-500 shadow-sm"
                                        />
                                        <InputError :message="errors.last_name" />
                                    </div>
                                </div>

                                <div class="grid gap-2 sm:grid-cols-2">
                                    <div class="grid gap-2">
                                        <Label for="email" class="text-slate-900">Email</Label>
                                        <Input
                                            id="email"
                                            name="email"
                                            type="email"
                                            required
                                            class="border-slate-900/10 bg-white/55 text-slate-900 placeholder:text-slate-500 shadow-sm"
                                        />
                                        <InputError :message="errors.email" />
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="phone" class="text-slate-900">Téléphone</Label>
                                        <Input
                                            id="phone"
                                            name="phone"
                                            required
                                            class="border-slate-900/10 bg-white/55 text-slate-900 placeholder:text-slate-500 shadow-sm"
                                        />
                                        <InputError :message="errors.phone" />
                                    </div>
                                </div>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="grid gap-2">
                                        <Label for="faculty" class="text-sm font-semibold text-slate-950">Faculté</Label>
                                        <select
                                            id="faculty"
                                            name="faculty"
                                            required
                                            class="h-11 w-full rounded-xl border border-slate-900/15 bg-white/60 px-4 text-sm text-slate-950 shadow-sm transition focus:outline-none focus-visible:border-slate-900/25 focus-visible:ring-2 focus-visible:ring-slate-900/15"
                                        >
                                            <option value="" disabled selected>Sélectionner…</option>
                                            <option v-for="f in faculties" :key="f" :value="f">
                                                {{ f }}
                                            </option>
                                        </select>
                                        <InputError :message="errors.faculty" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="study_year" class="text-sm font-semibold text-slate-950">Année</Label>
                                        <select
                                            id="study_year"
                                            name="study_year"
                                            required
                                            class="h-11 w-full rounded-xl border border-slate-900/15 bg-white/60 px-4 text-sm text-slate-950 shadow-sm transition focus:outline-none focus-visible:border-slate-900/25 focus-visible:ring-2 focus-visible:ring-slate-900/15"
                                        >
                                            <option value="" disabled selected>Sélectionner…</option>
                                            <option v-for="y in studyYears" :key="y" :value="y">
                                                {{ y }}
                                            </option>
                                        </select>
                                        <InputError :message="errors.study_year" />
                                    </div>
                                </div>

                                <div class="grid gap-2">
                                    <Label for="text_pdf" class="text-sm font-semibold text-slate-950">Texte (PDF)</Label>
                                    <input
                                        id="text_pdf"
                                        name="text_pdf"
                                        type="file"
                                        accept="application/pdf"
                                        required
                                        class="block w-full rounded-md border border-slate-900/10 bg-white/55 px-3 py-2 text-sm text-slate-900 shadow-sm file:mr-4 file:rounded-md file:border-0 file:bg-slate-950 file:px-3 file:py-2 file:text-xs file:font-semibold file:tracking-wide file:text-[#f7f4ee]"
                                    />
                                    <InputError :message="errors.text_pdf" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="proof_pdf" class="text-sm font-semibold text-slate-950">Preuve d'inscription à l'ULG (PDF)</Label>
                                    <input
                                        id="proof_pdf"
                                        name="proof_pdf"
                                        type="file"
                                        accept="application/pdf"
                                        required
                                        class="block w-full rounded-md border border-slate-900/10 bg-white/55 px-3 py-2 text-sm text-slate-900 shadow-sm file:mr-4 file:rounded-md file:border-0 file:bg-slate-950 file:px-3 file:py-2 file:text-xs file:font-semibold file:tracking-wide file:text-[#f7f4ee]"
                                    />
                                    <InputError :message="errors.proof_pdf" />
                                </div>

                                <div class="grid gap-3">
                                    <Label class="flex items-center gap-3">
                                        <input
                                            type="checkbox"
                                            name="accepted_rgpd"
                                            value="1"
                                            class="size-4 accent-slate-950"
                                        />
                                        <span class="text-sm text-slate-700">
                                            J'accepte la politique de confidentialité
                                            <a
                                                v-if="settings.privacy_policy_url"
                                                :href="settings.privacy_policy_url"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="underline underline-offset-4"
                                            >
                                                (PDF)
                                            </a>
                                        </span>
                                    </Label>
                                    <InputError :message="errors.accepted_rgpd" />

                                    <Label class="flex items-center gap-3">
                                        <input
                                            type="checkbox"
                                            name="accepted_rules"
                                            value="1"
                                            class="size-4 accent-slate-950"
                                        />
                                        <span class="text-sm text-slate-700">
                                            J'accepte le règlement
                                            <a
                                                v-if="settings.rules_url"
                                                :href="settings.rules_url"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="underline underline-offset-4"
                                            >
                                                (PDF)
                                            </a>
                                        </span>
                                    </Label>
                                    <InputError :message="errors.accepted_rules" />
                                </div>

                                <Button
                                    type="submit"
                                    class="group relative mt-2 h-12 w-full justify-center rounded-full px-6 text-sm font-semibold tracking-wide text-[#f7f4ee]"
                                    :disabled="processing || isClosed"
                                >
                                    <span
                                        class="absolute inset-0 rounded-full bg-slate-950 transition group-hover:bg-slate-900"
                                        aria-hidden="true"
                                    />
                                    <span
                                        class="absolute inset-0 rounded-full shadow-[0_0_0_1px_rgba(15,23,42,0.18),0_18px_40px_rgba(15,23,42,0.14)]"
                                        aria-hidden="true"
                                    />
                                    <span class="relative">Valider la candidature</span>
                                </Button>
                            </Form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="pointer-events-none absolute left-6 top-6 hidden h-12 w-12 border-l border-t border-slate-900/20 lg:block"
                aria-hidden="true"
            />
            <div
                class="pointer-events-none absolute right-6 top-6 hidden h-12 w-12 border-r border-t border-slate-900/20 lg:block"
                aria-hidden="true"
            />
            <div
                class="pointer-events-none absolute left-6 bottom-6 hidden h-12 w-12 border-l border-b border-slate-900/20 lg:block"
                aria-hidden="true"
            />
            <div
                class="pointer-events-none absolute right-6 bottom-6 hidden h-12 w-12 border-r border-b border-slate-900/20 lg:block"
                aria-hidden="true"
            />
        </section>
    </PublicLayout>
</template>
