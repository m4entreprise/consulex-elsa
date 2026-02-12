<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import FlashMessages from '@/components/FlashMessages.vue';

type Props = {
    title: string;
};

const props = defineProps<Props>();

const page = usePage();
const appName = computed(() => page.props.name);
</script>

<template>
    <Head :title="props.title" />

    <div class="min-h-screen bg-background text-foreground">
        <header class="sticky top-0 z-20 border-b border-border/60 bg-background/80 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4">
                <Link href="/" class="text-sm font-semibold tracking-tight">
                    {{ appName }}
                </Link>

                <nav class="flex items-center gap-3 text-sm">
                    <Link
                        href="/"
                        class="rounded-md px-3 py-2 text-muted-foreground hover:text-foreground"
                    >
                        Accueil
                    </Link>
                    <Link
                        href="/partenaires"
                        class="rounded-md px-3 py-2 text-muted-foreground hover:text-foreground"
                    >
                        Partenaires
                    </Link>
                    <Link
                        href="/inscription/spectateurs"
                        class="rounded-md px-3 py-2 text-muted-foreground hover:text-foreground"
                    >
                        Spectateurs
                    </Link>
                    <Link
                        href="/inscription/candidats"
                        class="rounded-md bg-primary px-3 py-2 text-primary-foreground hover:bg-primary/90"
                    >
                        Candidats
                    </Link>
                </nav>
            </div>
        </header>

        <main class="mx-auto w-full max-w-7xl px-4 py-10">
            <FlashMessages class="mb-6" />
            <slot />
        </main>

        <footer class="border-t border-border/60">
            <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-8 text-sm text-muted-foreground md:flex-row md:items-center md:justify-between">
                <div>
                    © {{ new Date().getFullYear() }} {{ appName }}
                </div>
                <div class="flex gap-4">
                    <a href="/mentions-legales" class="hover:text-foreground">Mentions</a>
                    <a href="/confidentialite" class="hover:text-foreground">Confidentialité</a>
                </div>
            </div>
        </footer>
    </div>
</template>
