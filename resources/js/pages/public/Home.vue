<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Button } from '@/components/ui/button';

type Settings = {
    event_title: string | null;
    event_theme: string | null;
    event_date: string | null;
    event_location: string | null;
    instagram_url: string | null;
    aftermovie_url: string | null;
};

type Partner = {
    id: number;
    name: string;
    url: string | null;
    description: string | null;
    logo_path: string | null;
};

defineProps<{
    settings: Settings;
    featuredPartners: Partner[];
}>();
</script>

<template>
    <PublicLayout title="Accueil">
        <section class="relative overflow-hidden rounded-2xl border border-border/60 bg-card p-8 md:p-14">
            <div class="pointer-events-none absolute inset-0 opacity-70 [background:radial-gradient(60%_50%_at_50%_0%,hsl(var(--primary)/0.20),transparent_70%)]" />
            <div class="pointer-events-none absolute -left-24 -top-24 size-80 rounded-full bg-gradient-to-tr from-fuchsia-500/15 via-amber-500/10 to-cyan-500/15 blur-3xl" />

            <div class="relative grid gap-10 md:grid-cols-12 md:items-end">
                <div class="md:col-span-8">
                    <p class="text-sm tracking-wide text-muted-foreground">
                        ConsuLex ELSA
                    </p>
                    <h1 class="mt-3 text-balance text-4xl font-semibold leading-[1.05] md:text-6xl">
                        {{ settings.event_theme || 'Concours d\'éloquence' }}
                    </h1>
                    <p class="mt-5 max-w-2xl text-pretty text-base text-muted-foreground md:text-lg">
                        {{
                            settings.event_title ||
                            "Une soirée premium, pensée comme une scène: voix, présence, et précision." 
                        }}
                    </p>

                    <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                        <Button as-child class="h-11 rounded-xl px-5">
                            <Link href="/inscription/candidats">S'inscrire (Candidat)</Link>
                        </Button>
                        <Button as-child variant="secondary" class="h-11 rounded-xl px-5">
                            <Link href="/inscription/spectateurs">Réserver (Spectateur)</Link>
                        </Button>
                    </div>

                    <div class="mt-8 grid gap-3 text-sm text-muted-foreground sm:grid-cols-2">
                        <div class="rounded-xl border border-border/60 bg-background/60 p-4">
                            <div class="text-xs uppercase tracking-wide">Date</div>
                            <div class="mt-1 text-foreground">
                                {{ settings.event_date || 'À confirmer' }}
                            </div>
                        </div>
                        <div class="rounded-xl border border-border/60 bg-background/60 p-4">
                            <div class="text-xs uppercase tracking-wide">Lieu</div>
                            <div class="mt-1 text-foreground">
                                {{ settings.event_location || 'À confirmer' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-4">
                    <div class="rounded-2xl border border-border/60 bg-background/40 p-5">
                        <div class="text-sm font-medium">Raccourcis</div>
                        <div class="mt-4 grid gap-2">
                            <a
                                v-if="settings.instagram_url"
                                :href="settings.instagram_url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="rounded-xl border border-border/60 bg-background/60 px-4 py-3 text-sm text-muted-foreground hover:text-foreground"
                            >
                                Instagram
                            </a>
                            <a
                                v-if="settings.aftermovie_url"
                                :href="settings.aftermovie_url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="rounded-xl border border-border/60 bg-background/60 px-4 py-3 text-sm text-muted-foreground hover:text-foreground"
                            >
                                Aftermovie
                            </a>
                            <Link
                                href="/partenaires"
                                class="rounded-xl border border-border/60 bg-background/60 px-4 py-3 text-sm text-muted-foreground hover:text-foreground"
                            >
                                Partenaires
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-10">
            <div class="flex items-end justify-between gap-4">
                <h2 class="text-xl font-semibold tracking-tight">Partenaires</h2>
                <Link href="/partenaires" class="text-sm text-muted-foreground hover:text-foreground">Voir tout</Link>
            </div>

            <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="p in featuredPartners"
                    :key="p.id"
                    class="group rounded-2xl border border-border/60 bg-card p-6 transition hover:-translate-y-0.5 hover:border-border"
                >
                    <div class="text-sm font-semibold">{{ p.name }}</div>
                    <div class="mt-2 line-clamp-3 text-sm text-muted-foreground">
                        {{ p.description || '—' }}
                    </div>
                    <div class="mt-4">
                        <a
                            v-if="p.url"
                            :href="p.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-sm text-muted-foreground hover:text-foreground"
                        >
                            Site partenaire
                        </a>
                    </div>
                </div>

                <div
                    v-if="featuredPartners.length === 0"
                    class="rounded-2xl border border-border/60 bg-card p-6 text-sm text-muted-foreground"
                >
                    Les partenaires seront affichés ici dès qu'ils sont ajoutés.
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
