<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';

type Settings = {
    spectator_capacity: number;
    candidate_capacity: number;
    spectator_registrations_enabled: boolean;
    candidate_registrations_enabled: boolean;
};

type Stats = {
    seatsUsed: number;
    spectatorRemaining: number;
    candidatesUsed: number;
    candidatesRemaining: number;
    foodOptionsActive: number;
};

defineProps<{
    settings: Settings;
    stats: Stats;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-xl border border-sidebar-border/70 p-4">
                    <div class="text-sm text-muted-foreground">Spectateurs</div>
                    <div class="mt-2 text-2xl font-semibold">
                        {{ stats.spectatorRemaining }} restantes
                    </div>
                    <div class="mt-1 text-sm text-muted-foreground">
                        {{ stats.seatsUsed }} occupées / {{ settings.spectator_capacity }}
                    </div>
                    <div class="mt-3 text-xs text-muted-foreground">
                        {{
                            settings.spectator_registrations_enabled
                                ? 'Inscriptions ouvertes'
                                : 'Inscriptions clôturées'
                        }}
                    </div>
                </div>

                <div class="rounded-xl border border-sidebar-border/70 p-4">
                    <div class="text-sm text-muted-foreground">Candidats</div>
                    <div class="mt-2 text-2xl font-semibold">
                        {{ stats.candidatesRemaining }} restantes
                    </div>
                    <div class="mt-1 text-sm text-muted-foreground">
                        {{ stats.candidatesUsed }} inscrits / {{ settings.candidate_capacity }}
                    </div>
                    <div class="mt-3 text-xs text-muted-foreground">
                        {{
                            settings.candidate_registrations_enabled
                                ? 'Inscriptions ouvertes'
                                : 'Inscriptions clôturées'
                        }}
                    </div>
                </div>

                <div class="rounded-xl border border-sidebar-border/70 p-4">
                    <div class="text-sm text-muted-foreground">Nourriture</div>
                    <div class="mt-2 text-2xl font-semibold">
                        {{ stats.foodOptionsActive }} options actives
                    </div>
                    <div class="mt-1 text-sm text-muted-foreground">
                        Gérer les options pour le formulaire spectateurs.
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-sidebar-border/70 p-4">
                <div class="text-sm font-medium">Raccourcis</div>
                <div class="mt-3 grid gap-2 md:grid-cols-2">
                    <a
                        href="/admin/settings"
                        class="rounded-lg border border-sidebar-border/70 px-4 py-3 text-sm hover:bg-accent"
                    >
                        Paramètres & quotas
                    </a>
                    <a
                        href="/admin/food-options"
                        class="rounded-lg border border-sidebar-border/70 px-4 py-3 text-sm hover:bg-accent"
                    >
                        Options nourriture
                    </a>
                    <a
                        href="/admin/registrations/spectators"
                        class="rounded-lg border border-sidebar-border/70 px-4 py-3 text-sm hover:bg-accent"
                    >
                        Inscriptions spectateurs
                    </a>
                    <a
                        href="/admin/registrations/candidates"
                        class="rounded-lg border border-sidebar-border/70 px-4 py-3 text-sm hover:bg-accent"
                    >
                        Inscriptions candidats
                    </a>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
