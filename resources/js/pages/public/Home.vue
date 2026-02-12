<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import Layout from '@/layouts/Layout.vue';
import HeroSection from '@/components/HeroSection.vue';
import { computed, onBeforeUnmount, onMounted, ref, toRefs } from 'vue';

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

const props = defineProps<{
    settings: Settings;
    featuredPartners: Partner[];
}>();

const { settings, featuredPartners } = toRefs(props);

const juryPlaceholders = [
    {
        name: 'Nom du jury',
        role: 'Présidence du jury',
        detail: 'Avocat / Professeur / Orateur',
        image: '/storage/jury1.jpeg',
    },
    {
        name: 'Nom du jury',
        role: 'Membre du jury',
        detail: 'Éloquence / Art oratoire',
        image: '/storage/jury2.jpg',
    },
    {
        name: 'Nom du jury',
        role: 'Membre du jury',
        detail: 'Droit / Rhétorique',
        image: '/storage/jury3.jpg',
    },
    {
        name: 'Nom du jury',
        role: 'Membre du jury',
        detail: 'Scène / Performance',
        image: '/storage/jury4.jpg',
    },
    {
        name: 'Nom du jury',
        role: 'Membre du jury',
        detail: 'Culture / Expression',
        image: '/storage/jury5.jpg',
    },
];

const partnerPlaceholders = Array.from({ length: 6 }, (_, i) => ({
    id: i + 1,
}));

const mainPartners = computed(() => featuredPartners.value.slice(0, 3));
const otherPartners = computed(() => featuredPartners.value.slice(3));
const marqueePartners = computed(() => {
    if (otherPartners.value.length > 0) return otherPartners.value;
    if (featuredPartners.value.length > 0) return featuredPartners.value;
    return partnerPlaceholders;
});

const modPratImages = ['/storage/ModPrat_img.jpg', '/storage/ModPrat1_img.jpg', '/storage/ModPrat2_img.jpg'];
const modPratActive = ref(0);
let modPratTimer: number | null = null;

function prefersReducedMotion() {
    if (typeof window === 'undefined') return true;
    if (!window.matchMedia) return false;
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

onMounted(() => {
    if (prefersReducedMotion()) return;

    modPratTimer = window.setInterval(() => {
        modPratActive.value = (modPratActive.value + 1) % modPratImages.length;
    }, 5200);
});

onBeforeUnmount(() => {
    if (modPratTimer) {
        window.clearInterval(modPratTimer);
        modPratTimer = null;
    }
});
</script>

<template>
    <Layout title="Accueil">
        <HeroSection
            :date="settings.event_date"
            :location="settings.event_location"
            background-image-url="/storage/hero_background_img.jpg"
        />

        <section class="mt-14">
            <div class="mx-auto max-w-7xl px-4">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <h2
                            class="text-lg font-semibold tracking-tight text-zinc-50 sm:text-xl"
                            style="font-family: 'Playfair Display', ui-serif, Georgia, serif;"
                        >
                            Jury
                        </h2>
                        <p class="mt-2 max-w-2xl text-sm text-zinc-300/75">
                            Le jury sera dévoilé prochainement. Voici des emplacements temporaires pour la mise en page.
                        </p>
                    </div>
                    <div class="hidden text-xs text-zinc-300/70 sm:block">
                        Édition 2026
                    </div>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
                    <div
                        v-for="(j, idx) in juryPlaceholders"
                        :key="idx"
                        class="group overflow-hidden rounded-2xl border border-white/10 bg-white/5 backdrop-blur transition hover:-translate-y-0.5 hover:border-amber-200/30"
                    >
                        <div class="relative aspect-[4/5] overflow-hidden">
                            <img
                                :src="j.image"
                                :alt="j.name"
                                class="absolute inset-0 h-full w-full object-cover object-center opacity-70 transition duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:scale-[1.02] group-hover:opacity-80"
                                loading="lazy"
                            />
                            <div
                                class="absolute inset-0 bg-[radial-gradient(120%_90%_at_50%_10%,rgba(251,191,36,0.20),transparent_55%),radial-gradient(90%_80%_at_20%_90%,rgba(59,130,246,0.14),transparent_60%)]"
                                aria-hidden="true"
                            />
                            <div
                                class="absolute inset-0 bg-[linear-gradient(to_top,rgba(2,6,23,0.92),rgba(2,6,23,0.35),rgba(2,6,23,0.15))]"
                                aria-hidden="true"
                            />
                            <div
                                class="absolute inset-0 opacity-0 transition duration-500 group-hover:opacity-100 bg-[radial-gradient(600px_circle_at_50%_40%,rgba(251,191,36,0.14),transparent_55%)]"
                                aria-hidden="true"
                            />

                            <div class="absolute bottom-0 left-0 right-0 p-5">
                                <div class="text-[11px] uppercase tracking-[0.24em] text-amber-200/80">
                                    {{ j.role }}
                                </div>
                                <div class="mt-2 text-base font-semibold text-zinc-50">
                                    {{ j.name }}
                                </div>
                                <div class="mt-1 text-sm text-zinc-300/75">
                                    {{ j.detail }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-14">
            <div class="mx-auto max-w-7xl px-4">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <h2
                            class="text-lg font-semibold tracking-tight text-zinc-50 sm:text-xl"
                            style="font-family: 'Playfair Display', ui-serif, Georgia, serif;"
                        >
                            Modalités pratiques
                        </h2>
                        <p class="mt-2 max-w-2xl text-sm text-zinc-300/75">
                            Informations essentielles (placeholders) — horaires, accès, tenue, contacts et conditions.
                        </p>
                    </div>
                    <div class="hidden text-xs text-zinc-300/70 sm:block">
                        Dernières infos à J-7
                    </div>
                </div>

                <div class="mt-6 grid gap-4 lg:grid-cols-12">
                    <div class="lg:col-span-7">
                        <div class="group relative h-full overflow-hidden rounded-2xl border border-white/10 bg-white/5 backdrop-blur">
                            <div
                                class="pointer-events-none absolute inset-0 bg-[radial-gradient(900px_circle_at_30%_0%,rgba(251,191,36,0.16),transparent_55%),radial-gradient(700px_circle_at_90%_30%,rgba(59,130,246,0.12),transparent_60%)]"
                                aria-hidden="true"
                            />
                            <div class="relative flex min-h-[320px] flex-col lg:min-h-0 lg:h-full">
                                <div class="relative flex-1">
                                    <div class="absolute inset-0">
                                        <img
                                            v-for="(src, idx) in modPratImages"
                                            :key="src"
                                            :src="src"
                                            alt="Salle académique – Université de Liège"
                                            class="absolute inset-0 h-full w-full object-cover object-center transition-opacity duration-1000 ease-in-out"
                                            :class="modPratActive === idx ? 'opacity-70' : 'opacity-0'"
                                            loading="lazy"
                                        />
                                    </div>
                                    <div
                                        class="absolute inset-0 bg-[linear-gradient(to_top,rgba(2,6,23,0.88),rgba(2,6,23,0.30),rgba(2,6,23,0.15))]"
                                        aria-hidden="true"
                                    />
                                    <div class="absolute bottom-0 left-0 right-0 p-6">
                                        <div class="text-[11px] uppercase tracking-[0.24em] text-amber-200/80">
                                            Salle académique
                                        </div>
                                        <div class="mt-2 text-base font-semibold text-zinc-50">
                                            Université de Liège
                                        </div>
                                        <div class="mt-1 text-sm text-zinc-300/75">
                                            Galerie (fade) — {{ modPratActive + 1 }}/{{ modPratImages.length }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-5">
                        <div class="flex h-full flex-col overflow-hidden rounded-2xl border border-white/10 bg-white/5 backdrop-blur">
                            <div class="flex items-center justify-between gap-4 border-b border-white/10 px-5 py-4">
                                <div>
                                    <div class="text-[11px] uppercase tracking-[0.24em] text-amber-200/80">
                                        Plan
                                    </div>
                                    <div class="mt-1 text-sm font-semibold text-zinc-50">Université de Liège (placeholder)</div>
                                </div>
                                <a
                                    href="https://www.openstreetmap.org/search?query=universit%C3%A9%20de%20li%C3%A8ge"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-xs text-zinc-300/80 transition hover:text-zinc-50"
                                >
                                    Ouvrir
                                </a>
                            </div>

                            <div class="relative min-h-[280px] flex-1">
                                <iframe
                                    class="absolute inset-0 h-full w-full"
                                    title="Carte – Université de Liège"
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"
                                    src="https://www.openstreetmap.org/export/embed.html?bbox=5.5586%2C50.6326%2C5.5798%2C50.6452&layer=mapnik"
                                />
                                <div
                                    class="pointer-events-none absolute inset-0 bg-[linear-gradient(to_top,rgba(2,6,23,0.65),transparent_55%)]"
                                    aria-hidden="true"
                                />
                            </div>

                            <div class="px-5 py-4 text-sm text-zinc-300/75">
                                <div>
                                    <span class="font-semibold text-zinc-50">Adresse:</span>
                                    Place du 20-Août, 4000 Liège (placeholder)
                                </div>
                                <div class="mt-2">
                                    <span class="font-semibold text-zinc-50">Accès:</span>
                                    Métro / Bus / Parking (placeholder)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <div class="text-[11px] uppercase tracking-[0.24em] text-amber-200/80">
                                Timeline
                            </div>
                            <div class="mt-2 text-sm text-zinc-300/75">
                                Une lecture rapide de la soirée (placeholders).
                            </div>
                        </div>
                        <div class="text-xs text-zinc-300/70">
                            Merci d'arriver 20 minutes avant le début.
                        </div>
                    </div>

                    <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-xl border border-white/10 bg-black/10 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div class="text-xs text-zinc-300/70">Ouverture</div>
                                <div class="h-2 w-2 rounded-full bg-amber-200/80" aria-hidden="true" />
                            </div>
                            <div class="mt-2 text-lg font-semibold text-zinc-50">19:00</div>
                            <div class="mt-1 text-xs text-zinc-300/70">Contrôle & placement</div>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/10 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div class="text-xs text-zinc-300/70">Début</div>
                                <div class="h-2 w-2 rounded-full bg-amber-200/80" aria-hidden="true" />
                            </div>
                            <div class="mt-2 text-lg font-semibold text-zinc-50">20:00</div>
                            <div class="mt-1 text-xs text-zinc-300/70">Entrée des candidats</div>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/10 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div class="text-xs text-zinc-300/70">Entracte</div>
                                <div class="h-2 w-2 rounded-full bg-amber-200/80" aria-hidden="true" />
                            </div>
                            <div class="mt-2 text-lg font-semibold text-zinc-50">20:45</div>
                            <div class="mt-1 text-xs text-zinc-300/70">Pause 10–15 min</div>
                        </div>

                        <div class="rounded-xl border border-white/10 bg-black/10 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div class="text-xs text-zinc-300/70">Fin</div>
                                <div class="h-2 w-2 rounded-full bg-amber-200/80" aria-hidden="true" />
                            </div>
                            <div class="mt-2 text-lg font-semibold text-zinc-50">22:30</div>
                            <div class="mt-1 text-xs text-zinc-300/70">Remise des prix</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mt-14">
            <div class="mx-auto max-w-7xl px-4">
                <div class="flex items-end justify-between gap-4">
                    <h2
                        class="text-lg font-semibold tracking-tight text-zinc-50 sm:text-xl"
                        style="font-family: 'Playfair Display', ui-serif, Georgia, serif;"
                    >
                        Nos partenaires
                    </h2>
                    <Link href="/partenaires" class="text-xs text-zinc-300/80 transition hover:text-zinc-50">
                        Voir tout
                    </Link>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2 md:grid-cols-3">
                    <div
                        v-for="(p, idx) in (featuredPartners.length === 0 ? [1, 2, 3] : mainPartners)"
                        :key="typeof p === 'number' ? `ph-${p}` : p.id"
                    >
                        <div
                            class="group h-full rounded-2xl border border-white/10 bg-white/5 p-5 backdrop-blur transition hover:-translate-y-0.5 hover:border-amber-200/30 sm:p-6"
                        >
                            <div class="flex items-center justify-between gap-4">
                                <div class="text-[11px] uppercase tracking-[0.24em] text-amber-200/80">
                                    Partenaire principal
                                </div>
                                <div class="text-xs text-zinc-300/70">#{{ idx + 1 }}</div>
                            </div>

                            <div class="mt-5 flex items-center justify-center rounded-2xl border border-white/10 bg-black/10 p-6 sm:p-8">
                                <template v-if="typeof p !== 'number' && p.logo_path">
                                    <img
                                        :src="p.logo_path"
                                        :alt="p.name"
                                        class="max-h-20 w-auto max-w-full opacity-90 transition group-hover:opacity-100 sm:max-h-24"
                                    />
                                </template>
                                <template v-else>
                                    <div class="h-14 w-40 rounded-xl bg-white/10 ring-1 ring-white/10 animate-pulse sm:h-16 sm:w-48" aria-hidden="true" />
                                </template>
                            </div>

                            <div class="mt-5">
                                <div class="text-sm font-semibold text-zinc-50">
                                    {{ typeof p === 'number' ? 'Nom du partenaire' : p.name }}
                                </div>
                                <div class="mt-2 line-clamp-2 text-sm text-zinc-300/75">
                                    {{ typeof p === 'number' ? 'Description placeholder (à venir).' : (p.description || '—') }}
                                </div>

                                <div class="mt-4">
                                    <a
                                        v-if="typeof p !== 'number' && p.url"
                                        :href="p.url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-sm text-zinc-300/80 transition hover:text-zinc-50"
                                    >
                                        Site partenaire
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 overflow-hidden rounded-2xl border border-white/10 bg-white/5 backdrop-blur">
                    <div class="flex items-center justify-between gap-4 border-b border-white/10 px-5 py-4">
                        <div class="text-[11px] uppercase tracking-[0.24em] text-amber-200/80">
                            Tous les partenaires
                        </div>
                        <div class="text-xs text-zinc-300/70">
                            Banderole
                        </div>
                    </div>

                    <div
                        class="marquee relative px-4 py-5"
                        aria-label="Liste des partenaires"
                    >
                        <div class="marquee-track flex w-max items-center gap-4">
                            <div
                                v-for="p in marqueePartners"
                                :key="`a-${p.id}`"
                                class="flex items-center gap-3 rounded-full border border-white/10 bg-black/10 px-4 py-2"
                            >
                                <div class="h-6 w-6 rounded-md bg-white/10 ring-1 ring-white/10" aria-hidden="true" />
                                <div class="text-xs font-medium text-zinc-200">
                                    {{ 'name' in p ? p.name : 'Partenaire' }}
                                </div>
                            </div>

                            <div
                                v-for="p in marqueePartners"
                                :key="`b-${p.id}`"
                                class="flex items-center gap-3 rounded-full border border-white/10 bg-black/10 px-4 py-2"
                                aria-hidden="true"
                            >
                                <div class="h-6 w-6 rounded-md bg-white/10 ring-1 ring-white/10" aria-hidden="true" />
                                <div class="text-xs font-medium text-zinc-200">
                                    {{ 'name' in p ? p.name : 'Partenaire' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </Layout>
</template>

<style scoped>
.marquee {
    -webkit-mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
    mask-image: linear-gradient(to right, transparent, black 10%, black 90%, transparent);
}

.marquee-track {
    animation: marquee 28s linear infinite;
}

@keyframes marquee {
    from {
        transform: translate3d(0, 0, 0);
    }
    to {
        transform: translate3d(-50%, 0, 0);
    }
}

@media (prefers-reduced-motion: reduce) {
    .marquee-track {
        animation: none;
        transform: none;
    }
}
</style>
