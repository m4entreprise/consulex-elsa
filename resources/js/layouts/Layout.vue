<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

type Props = {
    title?: string;
};

const props = withDefaults(defineProps<Props>(), {
    title: undefined,
});

const page = usePage();
const appName = computed(() => page.props.name);
</script>

<template>
    <Head :title="props.title || undefined">
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link
            href="https://fonts.bunny.net/css?family=inter:400,500,600,700&family=playfair-display:400,600,700"
            rel="stylesheet"
        />
    </Head>

    <div
        class="relative min-h-screen overflow-x-hidden bg-slate-900 text-zinc-50 selection:bg-amber-200/20 selection:text-amber-100"
        style="font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;"
    >
        <div
            class="noise pointer-events-none fixed inset-0 z-50 opacity-[0.075] mix-blend-overlay"
            aria-hidden="true"
        />
        <div
            class="pointer-events-none fixed inset-0 z-10 bg-[radial-gradient(1200px_circle_at_15%_0%,rgba(251,191,36,0.14),transparent_60%),radial-gradient(900px_circle_at_85%_10%,rgba(59,130,246,0.10),transparent_62%)]"
            aria-hidden="true"
        />

        <header class="relative z-20">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4">
                <Link
                    href="/"
                    class="text-xs font-medium tracking-[0.24em] text-zinc-200/90 transition hover:text-zinc-50"
                >
                    {{ appName }}
                </Link>

                <nav class="hidden items-center gap-6 text-xs tracking-wide text-zinc-300/90 md:flex">
                    <Link href="/" class="transition hover:text-zinc-50">Accueil</Link>
                    <Link href="/partenaires" class="transition hover:text-zinc-50">Partenaires</Link>
                    <Link
                        href="/inscription/candidats"
                        class="rounded-full border border-amber-200/30 bg-white/5 px-4 py-2 text-zinc-100 transition hover:border-amber-200/60 hover:bg-white/10"
                    >
                        Candidater
                    </Link>
                </nav>
            </div>
        </header>

        <main class="relative z-20">
            <slot />
        </main>

        <footer class="relative z-20 mt-16 border-t border-white/10">
            <div class="mx-auto max-w-7xl px-4 py-10 text-xs text-zinc-400">
                © {{ new Date().getFullYear() }} {{ appName }}
            </div>
        </footer>
    </div>
</template>

<style>
.noise {
    background-image: url("data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='180'%20height='180'%3E%3Cfilter%20id='n'%3E%3CfeTurbulence%20type='fractalNoise'%20baseFrequency='0.8'%20numOctaves='4'%20stitchTiles='stitch'/%3E%3C/filter%3E%3Crect%20width='180'%20height='180'%20filter='url(%23n)'%20opacity='0.4'/%3E%3C/svg%3E");
    background-repeat: repeat;
    background-size: 180px 180px;
}
</style>
