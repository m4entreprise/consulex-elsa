<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import type { ComponentPublicInstance, VNodeRef } from 'vue';
import { computed, onMounted, onBeforeUnmount, ref } from 'vue';

type Props = {
    date?: string | null;
    location?: string | null;
    theme?: string | null;
    description?: string | null;
    editionYear?: number | null;
    backgroundImageUrl?: string | null;
    backgroundImageAlt?: string;
};

const props = withDefaults(defineProps<Props>(), {
    date: null,
    location: null,
    theme: null,
    description: null,
    editionYear: null,
    backgroundImageUrl: null,
    backgroundImageAlt: 'Fond du concours',
});

const page = usePage();
const settings = computed(() => (page.props as any)?.settings ?? null);

const spectatorRegistrationUrl = computed(() => {
    const s = settings.value as any;
    if (s?.spectator_custom_form_enabled && s?.spectator_custom_form_url) {
        return String(s.spectator_custom_form_url);
    }
    return '/inscription/spectateurs';
});

const candidateRegistrationUrl = computed(() => {
    const s = settings.value as any;
    if (s?.candidate_custom_form_enabled && s?.candidate_custom_form_url) {
        return String(s.candidate_custom_form_url);
    }
    return '/inscription/candidats';
});

const spectatorRegistrationIsExternal = computed(() => spectatorRegistrationUrl.value.startsWith('http'));
const candidateRegistrationIsExternal = computed(() => candidateRegistrationUrl.value.startsWith('http'));

const titleEl = ref<HTMLElement | null>(null);
const metaEl = ref<HTMLElement | null>(null);
const ctasEl = ref<HTMLElement | null>(null);
const editionEl = ref<HTMLElement | null>(null);
const bgEl = ref<HTMLElement | null>(null);
const sectionEl = ref<HTMLElement | null>(null);

const magneticEls = ref<HTMLElement[]>([]);

const setMagneticEl: VNodeRef = (node) => {
    if (!node) return;

    if (node instanceof HTMLElement) {
        if (!magneticEls.value.includes(node)) {
            magneticEls.value.push(node);
        }
        return;
    }

    const instance = node as ComponentPublicInstance;
    const el = (instance.$el ?? null) as unknown;

    if (el instanceof HTMLElement) {
        if (!magneticEls.value.includes(el)) {
            magneticEls.value.push(el);
        }
    }
};

function supportsMagnetic() {
    if (typeof window === 'undefined') return false;
    if (!window.matchMedia) return false;

    return window.matchMedia('(hover: hover) and (pointer: fine)').matches;
}

function applyMagnetic(el: HTMLElement) {
    const strength = 6;

    const onMove = (e: PointerEvent) => {
        const rect = el.getBoundingClientRect();
        const x = e.clientX - (rect.left + rect.width / 2);
        const y = e.clientY - (rect.top + rect.height / 2);

        const dx = (x / rect.width) * strength;
        const dy = (y / rect.height) * strength;

        el.style.transform = `translate3d(${dx}px, ${dy}px, 0)`;
    };

    const onLeave = () => {
        el.style.transform = 'translate3d(0, 0, 0)';
    };

    el.addEventListener('pointermove', onMove);
    el.addEventListener('pointerleave', onLeave);

    return () => {
        el.removeEventListener('pointermove', onMove);
        el.removeEventListener('pointerleave', onLeave);
    };
}

function scrollToNextSection() {
    if (typeof window === 'undefined') return;

    const current = sectionEl.value;
    if (!current) return;

    let next = current.nextElementSibling;
    while (next && next.tagName.toLowerCase() !== 'section') {
        next = next.nextElementSibling;
    }

    if (next instanceof HTMLElement) {
        next.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

let cleanupMagnetic: Array<() => void> = [];

onMounted(() => {
    if (bgEl.value) {
        bgEl.value.animate(
            [
                { opacity: 0, transform: 'scale(1.03)' },
                { opacity: 1, transform: 'scale(1)' },
            ],
            {
                duration: 1200,
                easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
                fill: 'both',
            },
        );
    }

    if (titleEl.value) {
        titleEl.value.animate(
            [
                { transform: 'translate3d(0, 48px, 0)', opacity: 0 },
                { transform: 'translate3d(0, 0, 0)', opacity: 1 },
            ],
            {
                duration: 950,
                easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
                fill: 'both',
            },
        );
    }

    if (editionEl.value) {
        editionEl.value.animate(
            [
                { transform: 'translate3d(0, 18px, 0)', opacity: 0 },
                { transform: 'translate3d(0, 0, 0)', opacity: 1 },
            ],
            {
                duration: 860,
                delay: 260,
                easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
                fill: 'both',
            },
        );
    }

    if (metaEl.value) {
        metaEl.value.animate(
            [
                { transform: 'translate3d(0, 18px, 0)', opacity: 0 },
                { transform: 'translate3d(0, 0, 0)', opacity: 1 },
            ],
            {
                duration: 800,
                delay: 120,
                easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
                fill: 'both',
            },
        );
    }

    if (ctasEl.value) {
        ctasEl.value.animate(
            [
                { transform: 'translate3d(0, 14px, 0)', opacity: 0 },
                { transform: 'translate3d(0, 0, 0)', opacity: 1 },
            ],
            {
                duration: 820,
                delay: 220,
                easing: 'cubic-bezier(0.22, 1, 0.36, 1)',
                fill: 'both',
            },
        );
    }

    if (supportsMagnetic()) {
        cleanupMagnetic = magneticEls.value.map((el) => applyMagnetic(el));
    }
});

onBeforeUnmount(() => {
    cleanupMagnetic.forEach((fn) => fn());
    cleanupMagnetic = [];
});
</script>

<template>
    <section
        ref="sectionEl"
        class="relative min-h-[72vh] overflow-hidden bg-[#f7f4ee] supports-[height:100svh]:min-h-[72svh] sm:min-h-[78vh] sm:supports-[height:100svh]:min-h-[78svh] lg:min-h-[86vh] lg:supports-[height:100svh]:min-h-[86svh]"
    >
        <div ref="bgEl" class="absolute inset-0 bg-[#f7f4ee]">
            <div v-if="props.backgroundImageUrl" class="absolute inset-0">
                <img
                    :src="props.backgroundImageUrl"
                    :alt="props.backgroundImageAlt"
                    class="h-full w-full object-cover object-center opacity-25 saturate-75 contrast-125 mix-blend-multiply sm:opacity-30 lg:opacity-36"
                    loading="lazy"
                />
            </div>
            <div
                v-else
                class="absolute inset-0 bg-[radial-gradient(1200px_circle_at_18%_12%,rgba(15,23,42,0.06),transparent_58%),radial-gradient(900px_circle_at_88%_6%,rgba(180,83,9,0.08),transparent_62%)]"
                aria-hidden="true"
            />

            <div
                class="absolute inset-0 bg-[linear-gradient(to_bottom,rgba(247,244,238,0.58),rgba(247,244,238,0.78),rgba(247,244,238,0.94))]"
                aria-hidden="true"
            />
            <div
                class="absolute inset-0 [background:radial-gradient(900px_circle_at_50%_0%,rgba(15,23,42,0.06),transparent_62%)]"
                aria-hidden="true"
            />
        </div>

        <div class="relative mx-auto max-w-7xl px-4 pt-16 pb-16 sm:pt-20 sm:pb-20 lg:pt-28 lg:pb-28">
            <div class="grid gap-10 lg:grid-cols-12 lg:items-end">
                <div class="lg:col-span-10">
                    <p class="text-xs font-semibold tracking-[0.34em] text-slate-700">
                        ConsuLex ULiège x ELSA Liège
                    </p>

                    <h1
                        ref="titleEl"
                        class="mt-5 break-words text-balance leading-[0.98] tracking-[-0.02em] text-slate-950 sm:leading-[0.92]"
                        style="font-family: 'Playfair Display', ui-serif, Georgia, serif; font-size: clamp(2.85rem, 9.5vw, 9.5rem);"
                    >
                        <span class="block whitespace-nowrap break-normal">CONCOURS</span>
                        <span class="block whitespace-nowrap break-normal">D'ÉLOQUENCE</span>
                    </h1>

                    <p
                        v-if="props.theme"
                        class="mt-4 max-w-3xl text-sm font-semibold tracking-wide text-slate-800"
                    >
                        <span class="uppercase tracking-[0.22em] text-slate-600">Thème</span>
                        <span class="px-2 text-slate-500">—</span>
                        <span class="text-slate-900">{{ props.theme }}</span>
                    </p>

                    <p
                        v-if="props.description"
                        class="mt-4 max-w-3xl text-sm font-medium leading-relaxed text-slate-700 sm:text-base"
                    >
                        {{ props.description }}
                    </p>

                    <div ref="metaEl" class="mt-8 grid gap-4 sm:grid-cols-2 lg:max-w-3xl">
                        <div
                            class="rounded-2xl border border-slate-900/10 bg-white/55 px-5 py-4 shadow-sm"
                        >
                            <div class="text-[11px] uppercase tracking-[0.24em] text-slate-600">Date</div>
                            <div class="mt-2 text-sm font-semibold text-slate-950 sm:text-base">
                                {{ props.date || 'À confirmer' }}
                            </div>
                        </div>
                        <div
                            class="rounded-2xl border border-slate-900/10 bg-white/55 px-5 py-4 shadow-sm"
                        >
                            <div class="text-[11px] uppercase tracking-[0.24em] text-slate-600">Lieu</div>
                            <div class="mt-2 text-sm font-semibold text-slate-950 sm:text-base">
                                {{ props.location || 'À confirmer' }}
                            </div>
                        </div>
                    </div>

                    <div ref="ctasEl" class="mt-10 flex flex-col gap-3 sm:flex-row sm:items-center">
                        <a
                            v-if="candidateRegistrationIsExternal"
                            :ref="setMagneticEl"
                            :href="candidateRegistrationUrl"
                            class="group relative inline-flex h-12 w-full items-center justify-center rounded-full px-6 text-sm font-semibold tracking-wide text-[#f7f4ee] transition will-change-transform sm:w-auto"
                        >
                            <span
                                class="absolute inset-0 rounded-full bg-slate-950 transition group-hover:bg-slate-900"
                                aria-hidden="true"
                            />
                            <span
                                class="absolute inset-0 rounded-full shadow-[0_0_0_1px_rgba(15,23,42,0.18),0_18px_40px_rgba(15,23,42,0.14)]"
                                aria-hidden="true"
                            />
                            <span class="relative">Candidater</span>
                        </a>
                        <Link
                            v-else
                            :ref="setMagneticEl"
                            :href="candidateRegistrationUrl"
                            class="group relative inline-flex h-12 w-full items-center justify-center rounded-full px-6 text-sm font-semibold tracking-wide text-[#f7f4ee] transition will-change-transform sm:w-auto"
                        >
                            <span
                                class="absolute inset-0 rounded-full bg-slate-950 transition group-hover:bg-slate-900"
                                aria-hidden="true"
                            />
                            <span
                                class="absolute inset-0 rounded-full shadow-[0_0_0_1px_rgba(15,23,42,0.18),0_18px_40px_rgba(15,23,42,0.14)]"
                                aria-hidden="true"
                            />
                            <span class="relative">Candidater</span>
                        </Link>

                        <a
                            v-if="spectatorRegistrationIsExternal"
                            :ref="setMagneticEl"
                            :href="spectatorRegistrationUrl"
                            class="group relative inline-flex h-12 w-full items-center justify-center overflow-hidden rounded-full border border-slate-900/20 bg-white/0 px-6 text-sm font-semibold tracking-wide text-slate-950 transition will-change-transform hover:border-slate-900/35 sm:w-auto"
                        >
                            <span
                                class="pointer-events-none absolute inset-0 origin-left scale-x-0 bg-slate-900/5 transition duration-500 group-hover:scale-x-100"
                                aria-hidden="true"
                            />
                            <span
                                class="pointer-events-none absolute inset-0 rounded-full shadow-[0_0_0_1px_rgba(15,23,42,0.06)]"
                                aria-hidden="true"
                            />
                            <span class="relative">Assister</span>
                        </a>
                        <Link
                            v-else
                            :ref="setMagneticEl"
                            :href="spectatorRegistrationUrl"
                            class="group relative inline-flex h-12 w-full items-center justify-center overflow-hidden rounded-full border border-slate-900/20 bg-white/0 px-6 text-sm font-semibold tracking-wide text-slate-950 transition will-change-transform hover:border-slate-900/35 sm:w-auto"
                        >
                            <span
                                class="pointer-events-none absolute inset-0 origin-left scale-x-0 bg-slate-900/5 transition duration-500 group-hover:scale-x-100"
                                aria-hidden="true"
                            />
                            <span
                                class="pointer-events-none absolute inset-0 rounded-full shadow-[0_0_0_1px_rgba(15,23,42,0.06)]"
                                aria-hidden="true"
                            />
                            <span class="relative">Assister</span>
                        </Link>

                        <p class="text-xs leading-relaxed text-slate-600 sm:ml-4">
                            Concours d'éloquence de l'Université de Liège
                        </p>
                    </div>
                </div>

                <div class="lg:col-span-2 lg:flex lg:self-start lg:justify-end lg:pt-2">
                    <div class="hidden lg:block">
                        <div ref="editionEl" class="rounded-2xl border border-slate-900/10 bg-white/55 px-4 py-6 text-center shadow-sm">
                            <div class="text-[11px] uppercase tracking-[0.24em] text-slate-600">Édition</div>
                            <div class="mt-2 text-3xl font-semibold text-slate-950" style="font-family: 'Playfair Display', ui-serif, Georgia, serif;">{{ props.editionYear ?? '' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="pointer-events-none absolute left-6 top-6 h-12 w-12 border-l border-t border-slate-900/20"
            aria-hidden="true"
        />
        <div
            class="pointer-events-none absolute right-6 top-6 h-12 w-12 border-r border-t border-slate-900/20"
            aria-hidden="true"
        />
        <div
            class="pointer-events-none absolute left-6 bottom-6 h-12 w-12 border-l border-b border-slate-900/20"
            aria-hidden="true"
        />
        <div
            class="pointer-events-none absolute right-6 bottom-6 h-12 w-12 border-r border-b border-slate-900/20"
            aria-hidden="true"
        />

        <button
            type="button"
            class="absolute bottom-7 left-1/2 hidden -translate-x-1/2 lg:block"
            aria-label="Aller à la section suivante"
            @click="scrollToNextSection"
        >
            <span
                class="flex items-center gap-2 rounded-full border border-slate-900/10 bg-white/55 px-4 py-2 text-[11px] uppercase tracking-[0.24em] text-slate-700 shadow-sm transition hover:bg-white/70 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-900/20 focus-visible:ring-offset-2 focus-visible:ring-offset-[#f7f4ee]"
            >
                <span>Scroll</span>
                <span class="inline-block h-4 w-px bg-slate-900/15" />
                <span class="translate-y-[1px]">↓</span>
            </span>
        </button>
    </section>
</template>
