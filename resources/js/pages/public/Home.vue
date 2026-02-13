<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref, toRefs } from 'vue';
import HeroSection from '@/components/HeroSection.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

type Settings = {
    event_title: string | null;
    event_theme: string | null;
    event_date: string | null;
    event_location: string | null;
    instagram_url: string | null;
};

type Partner = {
    id: number;
    name: string;
    url: string | null;
    description: string | null;
    logo_path: string | null;
    logo_url?: string | null;
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

const partnerPlaceholders = Array.from({ length: 6 }, (_, i) => i + 1);

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
    <PublicLayout title="Accueil" :container="false">
        <HeroSection
            :date="settings.event_date"
            :location="settings.event_location"
            :theme="settings.event_theme"
            :description="settings.event_title"
            background-image-url="/storage/hero_background_img.jpg"
        />

        <section
            class="relative overflow-hidden bg-[#f7f4ee] py-16 supports-[height:100svh]:py-16 sm:py-20 lg:py-28"
        >
            <div class="mx-auto max-w-7xl px-4">
                <div class="grid gap-10 lg:grid-cols-12 lg:items-end">
                    <div class="lg:col-span-10">
                        <p class="text-xs font-semibold tracking-[0.34em] text-slate-700">
                            JURY
                        </p>

                        <h2
                            class="mt-5 break-words text-balance leading-[0.98] tracking-[-0.02em] text-slate-950 sm:leading-[0.92]"
                            style="font-family: 'Playfair Display', ui-serif, Georgia, serif; font-size: clamp(2.2rem, 7vw, 5.25rem);"
                        >
                            Un jury d'exception
                        </h2>

                        <p class="mt-4 max-w-3xl text-sm font-medium leading-relaxed text-slate-700 sm:text-base">
                            Le jury sera dévoilé prochainement. Voici des emplacements temporaires pour la mise en page.
                        </p>
                    </div>

                    <div class="hidden lg:col-span-2 lg:flex lg:justify-end">
                        <div class="rounded-2xl border border-slate-900/10 bg-white/55 px-4 py-6 text-center shadow-sm">
                            <div class="text-[11px] uppercase tracking-[0.24em] text-slate-600">Édition</div>
                            <div class="mt-2 text-3xl font-semibold text-slate-950" style="font-family: 'Playfair Display', ui-serif, Georgia, serif;">2026</div>
                        </div>
                    </div>
                </div>

                <div class="mt-10 grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
                    <div
                        v-for="(j, idx) in juryPlaceholders"
                        :key="idx"
                        class="group overflow-hidden rounded-2xl border border-slate-900/10 bg-white/55 shadow-sm transition hover:-translate-y-0.5 hover:border-slate-900/20"
                    >
                        <div class="relative aspect-[4/5] overflow-hidden">
                            <img
                                :src="j.image"
                                :alt="j.name"
                                class="absolute inset-0 h-full w-full object-cover object-center opacity-90 transition duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:scale-[1.02]"
                                loading="lazy"
                            />
                            <div
                                class="absolute inset-0 bg-[linear-gradient(to_top,rgba(247,244,238,0.92),rgba(247,244,238,0.30),rgba(247,244,238,0.10))]"
                                aria-hidden="true"
                            />
                        </div>

                        <div class="p-5">
                            <div class="text-[11px] uppercase tracking-[0.24em] text-slate-600">
                                {{ j.role }}
                            </div>
                            <div class="mt-2 text-base font-semibold text-slate-950">
                                {{ j.name }}
                            </div>
                            <div class="mt-1 text-sm text-slate-700">
                                {{ j.detail }}
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

        <section
            class="relative overflow-hidden bg-[#f7f4ee] py-16 supports-[height:100svh]:py-16 sm:py-20 lg:py-28"
        >
            <div class="mx-auto max-w-7xl px-4">
                <div class="grid gap-10 lg:grid-cols-12 lg:items-end">
                    <div class="lg:col-span-10">
                        <p class="text-xs font-semibold tracking-[0.34em] text-slate-700">
                            MODALITÉS PRATIQUES
                        </p>

                        <h2
                            class="mt-5 break-words text-balance leading-[0.98] tracking-[-0.02em] text-slate-950 sm:leading-[0.92]"
                            style="font-family: 'Playfair Display', ui-serif, Georgia, serif; font-size: clamp(2.1rem, 6.5vw, 4.75rem);"
                        >
                            Modalités pratiques
                        </h2>

                        <p class="mt-4 max-w-3xl text-sm font-medium leading-relaxed text-slate-700 sm:text-base">
                            Informations essentielles (placeholders) — horaires, accès, tenue, contacts et conditions.
                        </p>
                    </div>

                    <div class="hidden lg:col-span-2 lg:flex lg:justify-end">
                        <div class="rounded-2xl border border-slate-900/10 bg-white/55 px-4 py-6 text-center shadow-sm">
                            <div class="text-[11px] uppercase tracking-[0.24em] text-slate-600">Dernières infos</div>
                            <div class="mt-2 text-3xl font-semibold text-slate-950" style="font-family: 'Playfair Display', ui-serif, Georgia, serif;">J-7</div>
                        </div>
                    </div>
                </div>

                <div class="mt-10 grid gap-4 lg:grid-cols-12">
                    <div class="lg:col-span-7">
                        <div class="group relative h-full overflow-hidden rounded-2xl border border-slate-900/10 bg-white/55 shadow-sm">
                            <div class="relative flex min-h-[340px] flex-col lg:min-h-0 lg:h-full">
                                <div class="relative flex-1">
                                    <div class="absolute inset-0">
                                        <img
                                            v-for="(src, idx) in modPratImages"
                                            :key="src"
                                            :src="src"
                                            alt="Salle académique – Université de Liège"
                                            class="absolute inset-0 h-full w-full object-cover object-center transition-opacity duration-1000 ease-in-out"
                                            :class="modPratActive === idx ? 'opacity-90' : 'opacity-0'"
                                            loading="lazy"
                                        />
                                    </div>
                                    <div
                                        class="absolute inset-0 bg-[linear-gradient(to_top,rgba(247,244,238,0.92),rgba(247,244,238,0.25),rgba(247,244,238,0.06))]"
                                        aria-hidden="true"
                                    />
                                    <div class="absolute bottom-0 left-0 right-0 p-6">
                                        <div class="text-[11px] uppercase tracking-[0.24em] text-slate-600">
                                            Salle académique
                                        </div>
                                        <div class="mt-2 text-base font-semibold text-slate-950">
                                            Université de Liège
                                        </div>
                                        <div class="mt-1 text-sm text-slate-700">
                                            Galerie (fade) — {{ modPratActive + 1 }}/{{ modPratImages.length }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-5">
                        <div class="flex h-full flex-col overflow-hidden rounded-2xl border border-slate-900/10 bg-white/55 shadow-sm">
                            <div class="flex items-center justify-between gap-4 border-b border-slate-900/10 px-5 py-4">
                                <div>
                                    <div class="text-[11px] uppercase tracking-[0.24em] text-slate-600">
                                        Plan
                                    </div>
                                    <div class="mt-1 text-sm font-semibold text-slate-950">Université de Liège (placeholder)</div>
                                </div>
                                <a
                                    href="https://www.openstreetmap.org/search?query=universit%C3%A9%20de%20li%C3%A8ge"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-xs text-slate-600 transition hover:text-slate-950"
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
                                    class="pointer-events-none absolute inset-0 bg-[linear-gradient(to_top,rgba(247,244,238,0.40),transparent_65%)]"
                                    aria-hidden="true"
                                />
                            </div>

                            <div class="px-5 py-4 text-sm text-slate-700">
                                <div>
                                    <span class="font-semibold text-slate-950">Adresse:</span>
                                    Place du 20-Août, 4000 Liège (placeholder)
                                </div>
                                <div class="mt-2">
                                    <span class="font-semibold text-slate-950">Accès:</span>
                                    Métro / Bus / Parking (placeholder)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 rounded-2xl border border-slate-900/10 bg-white/55 p-6 shadow-sm">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <div class="text-[11px] uppercase tracking-[0.24em] text-slate-600">
                                Timeline
                            </div>
                            <div class="mt-2 text-sm text-slate-700">
                                Une lecture rapide de la soirée (placeholders).
                            </div>
                        </div>
                        <div class="text-xs text-slate-600">
                            Merci d'arriver 20 minutes avant le début.
                        </div>
                    </div>

                    <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-xl border border-slate-900/10 bg-white/60 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div class="text-xs text-slate-600">Ouverture</div>
                                <div class="h-2 w-2 rounded-full bg-slate-900/15" aria-hidden="true" />
                            </div>
                            <div class="mt-2 text-lg font-semibold text-slate-950">19:00</div>
                            <div class="mt-1 text-xs text-slate-600">Contrôle & placement</div>
                        </div>

                        <div class="rounded-xl border border-slate-900/10 bg-white/60 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div class="text-xs text-slate-600">Début</div>
                                <div class="h-2 w-2 rounded-full bg-slate-900/15" aria-hidden="true" />
                            </div>
                            <div class="mt-2 text-lg font-semibold text-slate-950">20:00</div>
                            <div class="mt-1 text-xs text-slate-600">Entrée des candidats</div>
                        </div>

                        <div class="rounded-xl border border-slate-900/10 bg-white/60 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div class="text-xs text-slate-600">Entracte</div>
                                <div class="h-2 w-2 rounded-full bg-slate-900/15" aria-hidden="true" />
                            </div>
                            <div class="mt-2 text-lg font-semibold text-slate-950">20:45</div>
                            <div class="mt-1 text-xs text-slate-600">Pause 10–15 min</div>
                        </div>

                        <div class="rounded-xl border border-slate-900/10 bg-white/60 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div class="text-xs text-slate-600">Fin</div>
                                <div class="h-2 w-2 rounded-full bg-slate-900/15" aria-hidden="true" />
                            </div>
                            <div class="mt-2 text-lg font-semibold text-slate-950">22:30</div>
                            <div class="mt-1 text-xs text-slate-600">Remise des prix</div>
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

        <section
            class="relative overflow-hidden bg-[#f7f4ee] py-16 supports-[height:100svh]:py-16 sm:py-20 lg:py-28"
        >
            <div class="mx-auto max-w-7xl px-4">
                <div class="grid gap-10 lg:grid-cols-12 lg:items-end">
                    <div class="lg:col-span-10">
                        <p class="text-xs font-semibold tracking-[0.34em] text-slate-700">
                            PARTENAIRES
                        </p>

                        <h2
                            class="mt-5 break-words text-balance leading-[0.98] tracking-[-0.02em] text-slate-950 sm:leading-[0.92]"
                            style="font-family: 'Playfair Display', ui-serif, Georgia, serif; font-size: clamp(2.1rem, 6.5vw, 4.75rem);"
                        >
                            Nos partenaires
                        </h2>

                        <p class="mt-4 max-w-3xl text-sm font-medium leading-relaxed text-slate-700 sm:text-base">
                            Ils accompagnent le concours et contribuent à faire de cette soirée un moment d'exception.
                        </p>
                    </div>

                    <div class="hidden lg:col-span-2 lg:flex lg:justify-end">
                        <div class="rounded-2xl border border-slate-900/10 bg-white/55 px-4 py-6 text-center shadow-sm">
                            <div class="text-[11px] uppercase tracking-[0.24em] text-slate-600">Réseau</div>
                            <div class="mt-2 text-3xl font-semibold text-slate-950" style="font-family: 'Playfair Display', ui-serif, Georgia, serif;">ULiège</div>
                        </div>
                    </div>
                </div>

                <div class="mt-10 flex items-center justify-between gap-4">
                    <div class="text-sm font-semibold text-slate-950">Partenaires principaux</div>
                    <Link href="/partenaires" class="text-sm text-slate-600 transition hover:text-slate-950">
                        Voir tout
                    </Link>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2 md:grid-cols-3">
                    <div
                        v-for="(p, idx) in (featuredPartners.length === 0 ? [1, 2, 3] : mainPartners)"
                        :key="typeof p === 'number' ? `ph-${p}` : p.id"
                    >
                        <div
                            class="group h-full rounded-2xl border border-slate-900/10 bg-white/55 p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-slate-900/20 sm:p-6"
                        >
                            <div class="flex items-center justify-between gap-4">
                                <div class="text-[11px] uppercase tracking-[0.24em] text-slate-600">
                                    Partenaire principal
                                </div>
                                <div class="text-xs text-slate-600">#{{ idx + 1 }}</div>
                            </div>

                            <div class="mt-5 flex items-center justify-center rounded-2xl border border-slate-900/10 bg-white/60 p-6 sm:p-8">
                                <template v-if="typeof p !== 'number' && p.logo_url">
                                    <img
                                        :src="p.logo_url"
                                        :alt="p.name"
                                        class="max-h-20 w-auto max-w-full opacity-90 transition group-hover:opacity-100 sm:max-h-24"
                                    />
                                </template>
                                <template v-else>
                                    <div class="h-14 w-40 rounded-xl bg-slate-900/5 ring-1 ring-slate-900/10 animate-pulse sm:h-16 sm:w-48" aria-hidden="true" />
                                </template>
                            </div>

                            <div class="mt-5">
                                <div class="text-sm font-semibold text-slate-950">
                                    {{ typeof p === 'number' ? 'Nom du partenaire' : p.name }}
                                </div>
                                <div class="mt-2 line-clamp-2 text-sm text-slate-700">
                                    {{ typeof p === 'number' ? 'Description placeholder (à venir).' : (p.description || '—') }}
                                </div>

                                <div class="mt-4">
                                    <a
                                        v-if="typeof p !== 'number' && p.url"
                                        :href="p.url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-sm text-slate-600 transition hover:text-slate-950"
                                    >
                                        Site partenaire
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 overflow-hidden rounded-2xl border border-slate-900/10 bg-white/55 shadow-sm">
                    <div class="flex items-center justify-between gap-4 border-b border-slate-900/10 px-5 py-4">
                        <div class="text-[11px] uppercase tracking-[0.24em] text-slate-600">
                            Tous les partenaires
                        </div>
                        <div class="text-xs text-slate-600">
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
                                :key="typeof p === 'number' ? `a-ph-${p}` : `a-${p.id}`"
                                class="flex items-center justify-center rounded-full border border-slate-900/10 bg-white/60 px-5 py-3"
                            >
                                <template v-if="typeof p !== 'number' && p.logo_url">
                                    <img
                                        :src="p.logo_url"
                                        :alt="p.name"
                                        class="h-8 w-auto max-w-[140px] object-contain opacity-90"
                                        loading="lazy"
                                    />
                                </template>
                                <template v-else>
                                    <div
                                        class="h-8 w-28 rounded-lg bg-slate-900/5 ring-1 ring-slate-900/10 animate-pulse"
                                        aria-hidden="true"
                                    />
                                    <span class="sr-only">Logo partenaire</span>
                                </template>
                            </div>

                            <div
                                v-for="p in marqueePartners"
                                :key="typeof p === 'number' ? `b-ph-${p}` : `b-${p.id}`"
                                class="flex items-center justify-center rounded-full border border-slate-900/10 bg-white/60 px-5 py-3"
                                aria-hidden="true"
                            >
                                <template v-if="typeof p !== 'number' && p.logo_url">
                                    <img
                                        :src="p.logo_url"
                                        :alt="p.name"
                                        class="h-8 w-auto max-w-[140px] object-contain opacity-90"
                                        loading="lazy"
                                    />
                                </template>
                                <template v-else>
                                    <div
                                        class="h-8 w-28 rounded-lg bg-slate-900/5 ring-1 ring-slate-900/10 animate-pulse"
                                        aria-hidden="true"
                                    />
                                </template>
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
