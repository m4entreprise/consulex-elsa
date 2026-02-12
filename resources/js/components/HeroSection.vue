<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { ComponentPublicInstance, VNodeRef } from 'vue';
import { onMounted, onBeforeUnmount, ref } from 'vue';

type Props = {
    date?: string | null;
    location?: string | null;
    backgroundImageUrl?: string | null;
    backgroundImageAlt?: string;
};

const props = withDefaults(defineProps<Props>(), {
    date: null,
    location: null,
    backgroundImageUrl: null,
    backgroundImageAlt: 'Fond du concours',
});

const titleEl = ref<HTMLElement | null>(null);
const metaEl = ref<HTMLElement | null>(null);
const ctasEl = ref<HTMLElement | null>(null);
const editionEl = ref<HTMLElement | null>(null);
const bgEl = ref<HTMLElement | null>(null);

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
    const strength = 10;

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
        class="relative min-h-[72vh] overflow-hidden supports-[height:100svh]:min-h-[72svh] sm:min-h-[78vh] sm:supports-[height:100svh]:min-h-[78svh] lg:min-h-[86vh] lg:supports-[height:100svh]:min-h-[86svh]"
    >
        <div ref="bgEl" class="absolute inset-0">
            <div v-if="props.backgroundImageUrl" class="absolute inset-0">
                <img
                    :src="props.backgroundImageUrl"
                    :alt="props.backgroundImageAlt"
                    class="h-full w-full object-cover object-center opacity-40 sm:opacity-50 lg:opacity-55"
                    loading="lazy"
                />
            </div>
            <div
                v-else
                class="absolute inset-0 bg-[radial-gradient(1200px_circle_at_20%_10%,rgba(251,191,36,0.16),transparent_55%),radial-gradient(900px_circle_at_85%_10%,rgba(59,130,246,0.12),transparent_60%)]"
                aria-hidden="true"
            />

            <div
                class="absolute inset-0 bg-[linear-gradient(to_bottom,rgba(15,23,42,0.20),rgba(15,23,42,0.78),rgba(15,23,42,0.98))]"
                aria-hidden="true"
            />
            <div
                class="absolute inset-0 [background:radial-gradient(900px_circle_at_50%_0%,rgba(255,255,255,0.06),transparent_58%)]"
                aria-hidden="true"
            />
        </div>

        <div class="relative mx-auto max-w-7xl px-4 pt-16 pb-16 sm:pt-20 sm:pb-20 lg:pt-28 lg:pb-28">
            <div class="grid gap-10 lg:grid-cols-12 lg:items-end">
                <div class="lg:col-span-10">
                    <p class="text-xs font-medium tracking-[0.28em] text-zinc-300/80">
                        CONSULEX ELSA
                    </p>

                    <h1
                        ref="titleEl"
                        class="mt-5 break-words text-balance leading-[0.98] tracking-[-0.02em] text-zinc-50 sm:leading-[0.92]"
                        style="font-family: 'Playfair Display', ui-serif, Georgia, serif; font-size: clamp(2.85rem, 9.5vw, 9.5rem);"
                    >
                        CONCOURS D'ÉLOQUENCE 2026
                    </h1>

                    <div ref="metaEl" class="mt-8 grid gap-4 sm:grid-cols-2 lg:max-w-3xl">
                        <div
                            class="rounded-2xl border border-white/10 bg-white/5 px-5 py-4 backdrop-blur"
                        >
                            <div class="text-[11px] uppercase tracking-[0.24em] text-zinc-300/70">Date</div>
                            <div class="mt-2 text-sm font-medium text-zinc-50 sm:text-base">
                                {{ props.date || 'À confirmer' }}
                            </div>
                        </div>
                        <div
                            class="rounded-2xl border border-white/10 bg-white/5 px-5 py-4 backdrop-blur"
                        >
                            <div class="text-[11px] uppercase tracking-[0.24em] text-zinc-300/70">Lieu</div>
                            <div class="mt-2 text-sm font-medium text-zinc-50 sm:text-base">
                                {{ props.location || 'À confirmer' }}
                            </div>
                        </div>
                    </div>

                    <div ref="ctasEl" class="mt-10 flex flex-col gap-3 sm:flex-row sm:items-center">
                        <Link
                            :ref="setMagneticEl"
                            href="/inscription/candidats"
                            class="group relative inline-flex h-12 w-full items-center justify-center rounded-full px-6 text-sm font-semibold tracking-wide text-slate-950 transition will-change-transform sm:w-auto"
                        >
                            <span
                                class="absolute inset-0 rounded-full bg-gradient-to-r from-amber-200 via-amber-300 to-amber-200 opacity-95 transition group-hover:opacity-100"
                                aria-hidden="true"
                            />
                            <span
                                class="absolute inset-0 rounded-full shadow-[0_0_0_1px_rgba(251,191,36,0.35),0_20px_60px_rgba(251,191,36,0.12)]"
                                aria-hidden="true"
                            />
                            <span class="relative">Candidater</span>
                        </Link>

                        <Link
                            :ref="setMagneticEl"
                            href="/inscription/spectateurs"
                            class="group relative inline-flex h-12 w-full items-center justify-center overflow-hidden rounded-full border border-amber-200/35 bg-white/0 px-6 text-sm font-semibold tracking-wide text-zinc-50 transition will-change-transform hover:border-amber-200/60 sm:w-auto"
                        >
                            <span
                                class="pointer-events-none absolute inset-0 origin-left scale-x-0 bg-gradient-to-r from-white/0 via-amber-200/10 to-white/0 transition duration-500 group-hover:scale-x-100"
                                aria-hidden="true"
                            />
                            <span
                                class="pointer-events-none absolute inset-0 rounded-full shadow-[0_0_0_1px_rgba(255,255,255,0.04)]"
                                aria-hidden="true"
                            />
                            <span class="relative">Assister</span>
                        </Link>

                        <p class="text-xs leading-relaxed text-zinc-300/70 sm:ml-4">
                            Sélections, jury, scène — une soirée pensée comme une performance.
                        </p>
                    </div>
                </div>

                <div class="lg:col-span-2 lg:flex lg:self-start lg:justify-end lg:pt-2">
                    <div class="hidden lg:block">
                        <div ref="editionEl" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-6 text-center backdrop-blur">
                            <div class="text-[11px] uppercase tracking-[0.24em] text-zinc-300/70">Édition</div>
                            <div class="mt-2 text-3xl font-semibold text-zinc-50" style="font-family: 'Playfair Display', ui-serif, Georgia, serif;">2026</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="pointer-events-none absolute -left-40 top-10 h-[520px] w-[520px] rounded-full bg-amber-200/10 blur-[90px]"
            aria-hidden="true"
        />
        <div
            class="pointer-events-none absolute -right-40 bottom-[-120px] h-[560px] w-[560px] rounded-full bg-blue-500/10 blur-[110px]"
            aria-hidden="true"
        />

        <div class="pointer-events-none absolute bottom-7 left-1/2 hidden -translate-x-1/2 lg:block" aria-hidden="true">
            <div class="flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-[11px] uppercase tracking-[0.24em] text-zinc-200/80 backdrop-blur">
                <span>Scroll</span>
                <span class="inline-block h-4 w-px bg-white/15" />
                <span class="translate-y-[1px]">↓</span>
            </div>
        </div>
    </section>
</template>
