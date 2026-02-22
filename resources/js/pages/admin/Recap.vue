<script setup lang="ts">
import FlashMessages from '@/components/FlashMessages.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { computed } from 'vue';

type Settings = {
    spectator_capacity: number;
    candidate_capacity: number;
};

type SpectatorRegistration = {
    id: number;
    created_at: string | null;
    full_name: string;
    email: string;
    phone: string;
    accompanying_count: number;
    accompanying_people: Array<{ first_name: string; last_name: string }> | null;
    food_wanted: boolean;
    food_option_label: string | null;
    food_quantities: Record<number, number> | null;
};

type CandidateRegistration = {
    id: number;
    created_at: string | null;
    full_name: string;
    email: string;
    phone: string;
    faculty: string;
    study_year: string | null;
};

type FoodOptionRecap = {
    id: number;
    label: string;
    is_active: boolean;
    ordered_quantity: number;
};

const props = defineProps<{
    settings: Settings;
    spectators: {
        registrations: SpectatorRegistration[];
        seats_used: number;
        seats_remaining: number;
    };
    candidates: {
        registrations: CandidateRegistration[];
        used: number;
        remaining: number;
        by_faculty: Record<string, number>;
        by_year: Record<string, number>;
    };
    food: {
        options: FoodOptionRecap[];
        total_ordered: number;
    };
}>();

const spectatorRegistrationsCount = computed(() => props.spectators.registrations.length);
const spectatorAccompanyingTotal = computed(() => props.spectators.registrations.reduce((sum, r) => sum + (r.accompanying_count ?? 0), 0));
const spectatorFoodWantedCount = computed(() => props.spectators.registrations.filter((r) => r.food_wanted).length);

const candidateRegistrationsCount = computed(() => props.candidates.registrations.length);

const candidatesByFacultyEntries = computed(() => {
    return Object.entries(props.candidates.by_faculty ?? {}).filter(([key]) => String(key).trim() !== '');
});

const candidatesByYearEntries = computed(() => {
    return Object.entries(props.candidates.by_year ?? {}).filter(([key]) => String(key).trim() !== '');
});
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Admin', href: '/dashboard' }, { title: 'Récap', href: '/admin/recap' }]">
        <div class="p-4">
            <FlashMessages class="mb-4" />

            <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <Heading
                    title="Récap organisateurs"
                    description="Vue d'ensemble des inscriptions + stats utiles (spectateurs, candidats, nourriture)."
                />
                <div class="flex flex-wrap items-center gap-2">
                    <Button as-child variant="secondary">
                        <a href="/admin/registrations/spectators/export">Exporter spectateurs (CSV)</a>
                    </Button>
                    <Button as-child variant="secondary">
                        <a href="/admin/registrations/candidates/export">Exporter candidats (CSV)</a>
                    </Button>
                    <Button as-child variant="secondary">
                        <a href="/admin/registrations/candidates/download-archive">Télécharger ZIP candidats</a>
                    </Button>
                </div>
            </div>

            <div class="mt-6 grid gap-4">
                <div class="grid gap-4 lg:grid-cols-3">
                    <div>
                        <div class="rounded-xl border border-border/60 bg-card p-5">
                            <div class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Spectateurs</div>
                            <div class="mt-3 grid gap-1 text-sm">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="text-muted-foreground">Inscriptions</div>
                                    <div class="font-medium">{{ spectatorRegistrationsCount }}</div>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <div class="text-muted-foreground">Accompagnants</div>
                                    <div class="font-medium">{{ spectatorAccompanyingTotal }}</div>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <div class="text-muted-foreground">Places occupées</div>
                                    <div class="font-medium">{{ spectators.seats_used }} / {{ settings.spectator_capacity }}</div>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <div class="text-muted-foreground">Places restantes</div>
                                    <div class="font-medium">{{ spectators.seats_remaining }}</div>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <div class="text-muted-foreground">Nourriture demandée</div>
                                    <div class="font-medium">{{ spectatorFoodWantedCount }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="rounded-xl border border-border/60 bg-card p-5">
                            <div class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Candidats</div>
                            <div class="mt-3 grid gap-1 text-sm">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="text-muted-foreground">Inscriptions</div>
                                    <div class="font-medium">{{ candidateRegistrationsCount }}</div>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <div class="text-muted-foreground">Places occupées</div>
                                    <div class="font-medium">{{ candidates.used }} / {{ settings.candidate_capacity }}</div>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <div class="text-muted-foreground">Places restantes</div>
                                    <div class="font-medium">{{ candidates.remaining }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="rounded-xl border border-border/60 bg-card p-5">
                            <div class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Nourriture</div>
                            <div class="mt-3 grid gap-1 text-sm">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="text-muted-foreground">Total commandé</div>
                                    <div class="font-medium">{{ food.total_ordered }}</div>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <div class="text-muted-foreground">Options</div>
                                    <div class="font-medium">{{ food.options.length }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-border/60 bg-card">
                    <div class="border-b border-border/60 px-5 py-4">
                        <div class="text-sm font-medium">Candidats par faculté</div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-muted/50 text-left">
                                <tr>
                                    <th class="px-4 py-3">Faculté</th>
                                    <th class="px-4 py-3 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="([label, count], idx) in candidatesByFacultyEntries" :key="`${label}-${idx}`" class="border-t border-border/60">
                                    <td class="px-4 py-3">{{ label || '—' }}</td>
                                    <td class="px-4 py-3 text-right font-medium">{{ count }}</td>
                                </tr>
                                <tr v-if="candidatesByFacultyEntries.length === 0">
                                    <td class="px-4 py-8 text-muted-foreground" colspan="2">Aucune donnée.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-xl border border-border/60 bg-card">
                    <div class="border-b border-border/60 px-5 py-4">
                        <div class="text-sm font-medium">Candidats par année</div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-muted/50 text-left">
                                <tr>
                                    <th class="px-4 py-3">Année</th>
                                    <th class="px-4 py-3 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="([label, count], idx) in candidatesByYearEntries" :key="`${label}-${idx}`" class="border-t border-border/60">
                                    <td class="px-4 py-3">{{ label || '—' }}</td>
                                    <td class="px-4 py-3 text-right font-medium">{{ count }}</td>
                                </tr>
                                <tr v-if="candidatesByYearEntries.length === 0">
                                    <td class="px-4 py-8 text-muted-foreground" colspan="2">Aucune donnée.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-xl border border-border/60 bg-card">
                    <div class="border-b border-border/60 px-5 py-4">
                        <div class="text-sm font-medium">Nourriture — quantités par option</div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-muted/50 text-left">
                                <tr>
                                    <th class="px-4 py-3">Option</th>
                                    <th class="px-4 py-3">Statut</th>
                                    <th class="px-4 py-3 text-right">Commandé</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="o in food.options" :key="o.id" class="border-t border-border/60">
                                    <td class="px-4 py-3 font-medium">{{ o.label }}</td>
                                    <td class="px-4 py-3">
                                        <span class="text-muted-foreground">{{ o.is_active ? 'Actif' : 'Inactif' }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium">{{ o.ordered_quantity }}</td>
                                </tr>
                                <tr v-if="food.options.length === 0">
                                    <td class="px-4 py-8 text-muted-foreground" colspan="3">Aucune option.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-xl border border-border/60 bg-card">
                    <div class="border-b border-border/60 px-5 py-4">
                        <div class="text-sm font-medium">Liste complète — spectateurs</div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-muted/50 text-left">
                                <tr>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3">Nom</th>
                                    <th class="px-4 py-3">Email</th>
                                    <th class="px-4 py-3">Téléphone</th>
                                    <th class="px-4 py-3 text-right">Accompagnants</th>
                                    <th class="px-4 py-3">Nourriture</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="r in spectators.registrations" :key="r.id" class="border-t border-border/60">
                                    <td class="px-4 py-3 text-muted-foreground">{{ r.created_at || '—' }}</td>
                                    <td class="px-4 py-3 font-medium">{{ r.full_name }}</td>
                                    <td class="px-4 py-3">{{ r.email }}</td>
                                    <td class="px-4 py-3">{{ r.phone }}</td>
                                    <td class="px-4 py-3 text-right">{{ r.accompanying_count }}</td>
                                    <td class="px-4 py-3">
                                        <div class="grid gap-1">
                                            <div class="text-muted-foreground">
                                                {{ r.food_option_label || (r.food_wanted ? 'Oui' : 'Non') }}
                                            </div>
                                            <div
                                                v-if="Array.isArray(r.accompanying_people) && r.accompanying_people.length"
                                                class="text-xs text-muted-foreground"
                                            >
                                                Accompagnants :
                                                {{ r.accompanying_people.map((p) => `${p.first_name} ${p.last_name}`).join(', ') }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="spectators.registrations.length === 0">
                                    <td class="px-4 py-8 text-muted-foreground" colspan="6">Aucune inscription.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-xl border border-border/60 bg-card">
                    <div class="border-b border-border/60 px-5 py-4">
                        <div class="text-sm font-medium">Liste complète — candidats</div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-muted/50 text-left">
                                <tr>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3">Nom</th>
                                    <th class="px-4 py-3">Email</th>
                                    <th class="px-4 py-3">Téléphone</th>
                                    <th class="px-4 py-3">Faculté</th>
                                    <th class="px-4 py-3">Année</th>
                                    <th class="px-4 py-3">Fichiers</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="r in candidates.registrations" :key="r.id" class="border-t border-border/60">
                                    <td class="px-4 py-3 text-muted-foreground">{{ r.created_at || '—' }}</td>
                                    <td class="px-4 py-3 font-medium">{{ r.full_name }}</td>
                                    <td class="px-4 py-3">{{ r.email }}</td>
                                    <td class="px-4 py-3">{{ r.phone }}</td>
                                    <td class="px-4 py-3">{{ r.faculty }}</td>
                                    <td class="px-4 py-3">{{ r.study_year || '—' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex gap-2">
                                            <a class="underline underline-offset-4" :href="`/admin/registrations/candidates/${r.id}/download-text`">PDF</a>
                                            <a class="underline underline-offset-4" :href="`/admin/registrations/candidates/${r.id}/download-photo`">Preuve ULG</a>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="candidates.registrations.length === 0">
                                    <td class="px-4 py-8 text-muted-foreground" colspan="7">Aucune inscription.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
