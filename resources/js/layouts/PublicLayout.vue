<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Menu, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import FlashMessages from '@/components/FlashMessages.vue';
import { useCurrentUrl } from '@/composables/useCurrentUrl';

type Props = {
    title: string;
    container?: boolean;
};

const props = withDefaults(defineProps<Props>(), {
    container: true,
});

const page = usePage();
const appName = computed(() => page.props.name);
const instagramUrl = computed(() => (page.props as any)?.settings?.instagram_url ?? null);
const hasFlash = computed(() => Boolean((page.props as any)?.flash?.success || (page.props as any)?.flash?.error));

const { isCurrentUrl } = useCurrentUrl();
const mobileMenuOpen = ref(false);

const navItems = [
    {
        label: 'Accueil',
        href: '/',
    },
    {
        label: 'Partenaires',
        href: '/partenaires',
    },
    {
        label: 'Spectateurs',
        href: '/inscription/spectateurs',
    },
];

function closeMobileMenu() {
    mobileMenuOpen.value = false;
}
</script>

<template>
    <Head :title="props.title">
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link
            href="https://fonts.bunny.net/css?family=inter:400,500,600,700&family=playfair-display:400,600,700"
            rel="stylesheet"
        />
    </Head>

    <div class="min-h-screen bg-background text-foreground">
        <header class="sticky top-0 z-20 border-b border-border/60 bg-background/80 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4">
                <div class="flex min-w-0 items-center gap-3">
                    <Link href="/" class="truncate text-sm font-semibold tracking-tight">
                        {{ appName }}
                    </Link>
                </div>

                <nav class="hidden items-center gap-1 text-sm md:flex">
                    <Link
                        v-for="item in navItems"
                        :key="item.href"
                        :href="item.href"
                        class="rounded-md px-3 py-2 transition"
                        :class="
                            isCurrentUrl(item.href)
                                ? 'bg-muted text-foreground'
                                : 'text-muted-foreground hover:text-foreground'
                        "
                    >
                        {{ item.label }}
                    </Link>

                    <Link
                        href="/inscription/candidats"
                        class="ml-2 rounded-md bg-primary px-3 py-2 text-primary-foreground hover:bg-primary/90"
                        :class="isCurrentUrl('/inscription/candidats') ? 'ring-2 ring-primary/30 ring-offset-2 ring-offset-background' : ''"
                    >
                        Candidats
                    </Link>
                </nav>

                <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-md border border-border/60 bg-background/60 p-2 text-muted-foreground hover:text-foreground md:hidden"
                    :aria-expanded="mobileMenuOpen"
                    aria-label="Ouvrir le menu"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                >
                    <Menu v-if="!mobileMenuOpen" class="h-5 w-5" />
                    <X v-else class="h-5 w-5" />
                </button>
            </div>

            <div v-if="mobileMenuOpen" class="border-t border-border/60 md:hidden">
                <div class="mx-auto max-w-7xl px-4 py-3">
                    <nav class="grid gap-1 text-sm">
                        <Link
                            v-for="item in navItems"
                            :key="item.href"
                            :href="item.href"
                            class="rounded-md px-3 py-2 transition"
                            :class="
                                isCurrentUrl(item.href)
                                    ? 'bg-muted text-foreground'
                                    : 'text-muted-foreground hover:text-foreground'
                            "
                            @click="closeMobileMenu"
                        >
                            {{ item.label }}
                        </Link>
                        <Link
                            href="/inscription/candidats"
                            class="mt-1 rounded-md bg-primary px-3 py-2 text-center text-primary-foreground hover:bg-primary/90"
                            @click="closeMobileMenu"
                        >
                            Candidats
                        </Link>
                    </nav>
                </div>
            </div>
        </header>

        <main v-if="props.container" class="mx-auto w-full max-w-7xl px-4 py-10">
            <FlashMessages class="mb-6" />
            <slot />
        </main>

        <main v-else class="w-full">
            <div v-if="hasFlash" class="mx-auto w-full max-w-7xl px-4 pt-6">
                <FlashMessages class="mb-6" />
            </div>
            <slot />
        </main>

        <footer class="border-t border-border/60">
            <div class="mx-auto max-w-7xl px-4 py-10">
                <div class="grid gap-8 md:grid-cols-12">
                    <div class="md:col-span-5">
                        <div class="text-sm font-semibold text-foreground">
                            {{ appName }}
                        </div>
                        <div class="mt-2 text-sm text-muted-foreground">
                            © {{ new Date().getFullYear() }}
                        </div>
                    </div>

                    <div class="md:col-span-4">
                        <div class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                            Navigation
                        </div>
                        <div class="mt-3 grid gap-2 text-sm">
                            <Link href="/" class="text-muted-foreground hover:text-foreground">Accueil</Link>
                            <Link href="/partenaires" class="text-muted-foreground hover:text-foreground">Partenaires</Link>
                            <Link href="/inscription/spectateurs" class="text-muted-foreground hover:text-foreground">Spectateurs</Link>
                            <Link href="/inscription/candidats" class="text-muted-foreground hover:text-foreground">Candidats</Link>
                        </div>
                    </div>

                    <div class="md:col-span-3">
                        <div class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                            Informations
                        </div>
                        <div class="mt-3 grid gap-2 text-sm">
                            <Link href="/mentions-legales" class="text-muted-foreground hover:text-foreground">Mentions légales</Link>
                            <Link href="/confidentialite" class="text-muted-foreground hover:text-foreground">Confidentialité</Link>
                            <a
                                v-if="instagramUrl"
                                :href="instagramUrl"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-2 text-muted-foreground hover:text-foreground"
                            >
                                Instagram
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
