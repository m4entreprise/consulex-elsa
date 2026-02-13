<script setup lang="ts">
import PublicLayout from '@/layouts/PublicLayout.vue';

type Settings = {
    event_title: string | null;
};

type Partner = {
    id: number;
    name: string;
    url: string | null;
    description: string | null;
    logo_path: string | null;
    logo_url?: string | null;
};

defineProps<{
    settings: Settings;
    partners: Partner[];
}>();
</script>

<template>
    <PublicLayout title="Partenaires" :container="false">
        <section
            class="relative overflow-hidden bg-[#f7f4ee] py-16 supports-[height:100svh]:py-16 sm:py-20 lg:py-28"
        >
            <div class="mx-auto max-w-7xl px-4">
                <div class="grid gap-10 lg:grid-cols-12 lg:items-end">
                    <div class="lg:col-span-10">
                        <p class="text-xs font-semibold tracking-[0.34em] text-slate-700">
                            PARTENAIRES
                        </p>

                        <h1
                            class="mt-5 break-words text-balance leading-[0.98] tracking-[-0.02em] text-slate-950 sm:leading-[0.92]"
                            style="font-family: 'Playfair Display', ui-serif, Georgia, serif; font-size: clamp(2.2rem, 7vw, 5.25rem);"
                        >
                            Nos partenaires
                        </h1>

                        <p class="mt-4 max-w-3xl text-sm font-medium leading-relaxed text-slate-700 sm:text-base">
                            Merci à celles et ceux qui rendent l'édition possible.
                        </p>
                    </div>
                </div>

                <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="p in partners"
                        :key="p.id"
                        class="group rounded-2xl border border-slate-900/10 bg-white/55 p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-slate-900/20 sm:p-6"
                    >
                        <div class="flex items-center justify-between gap-4">
                            <div class="text-[11px] uppercase tracking-[0.24em] text-slate-600">
                                Partenaire
                            </div>
                        </div>

                        <div class="mt-5 flex items-center justify-center rounded-2xl border border-slate-900/10 bg-white/60 p-6 sm:p-8">
                            <template v-if="p.logo_url">
                                <img
                                    :src="p.logo_url"
                                    :alt="p.name"
                                    class="max-h-20 w-auto max-w-full opacity-90 transition group-hover:opacity-100 sm:max-h-24"
                                    loading="lazy"
                                />
                            </template>
                            <template v-else>
                                <div class="h-14 w-40 rounded-xl bg-slate-900/5 ring-1 ring-slate-900/10 animate-pulse sm:h-16 sm:w-48" aria-hidden="true" />
                            </template>
                        </div>

                        <div class="mt-5">
                            <div class="text-sm font-semibold text-slate-950">
                                {{ p.name }}
                            </div>
                            <div class="mt-2 line-clamp-3 text-sm text-slate-700">
                                {{ p.description || '—' }}
                            </div>
                            <div class="mt-4">
                                <a
                                    v-if="p.url"
                                    :href="p.url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-sm text-slate-600 transition hover:text-slate-950"
                                >
                                    Visiter
                                </a>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="partners.length === 0"
                        class="rounded-2xl border border-slate-900/10 bg-white/55 p-6 text-sm text-slate-600"
                    >
                        Aucun partenaire n'est encore renseigné.
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
