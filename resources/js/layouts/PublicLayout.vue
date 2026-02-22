<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Instagram, Menu, X } from 'lucide-vue-next';
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
const settings = computed(() => (page.props as any)?.settings ?? null);
const instagramUrl = computed(() => (settings.value as any)?.instagram_url ?? null);
const privacyPolicyUrl = computed(() => (settings.value as any)?.privacy_policy_url ?? null);
const rulesUrl = computed(() => (settings.value as any)?.rules_url ?? null);
const footerBrand = computed(() => (settings.value as any)?.footer_brand || appName.value);
const footerDescription = computed(() => (settings.value as any)?.footer_description || '');
const footerCopyright = computed(() => (settings.value as any)?.footer_copyright || `© ${new Date().getFullYear()}`);
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
        <header class="sticky top-0 z-30 border-b border-border/60 bg-background/70 backdrop-blur supports-[backdrop-filter]:bg-background/60">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4">
                <div class="flex min-w-0 items-center gap-3">
                    <Link
                        href="/"
                        class="truncate text-base font-semibold tracking-tight text-foreground"
                        style="font-family: 'Playfair Display', ui-serif, Georgia, serif;"
                    >
                        {{ appName }}
                    </Link>
                </div>

                <nav class="hidden items-center gap-1 text-sm md:flex">
                    <Link
                        v-for="item in navItems"
                        :key="item.href"
                        :href="item.href"
                        class="rounded-full px-4 py-2 font-medium transition"
                        :class="
                            isCurrentUrl(item.href)
                                ? 'bg-muted text-foreground shadow-sm'
                                : 'text-muted-foreground hover:bg-muted/60 hover:text-foreground'
                        "
                    >
                        {{ item.label }}
                    </Link>

                    <Link
                        href="/inscription/candidats"
                        class="ml-2 rounded-full bg-primary px-4 py-2 font-semibold text-primary-foreground shadow-sm ring-1 ring-primary/20 hover:bg-primary/90"
                        :class="isCurrentUrl('/inscription/candidats') ? 'ring-2 ring-primary/30 ring-offset-2 ring-offset-background' : ''"
                    >
                        Candidats
                    </Link>
                </nav>

                <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-full border border-border/60 bg-background/60 p-2 text-muted-foreground shadow-sm hover:bg-muted/60 hover:text-foreground md:hidden"
                    :aria-expanded="mobileMenuOpen"
                    aria-label="Ouvrir le menu"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                >
                    <Menu v-if="!mobileMenuOpen" class="h-5 w-5" />
                    <X v-else class="h-5 w-5" />
                </button>
            </div>

            <div v-if="mobileMenuOpen" class="border-t border-border/60 bg-background/80 backdrop-blur md:hidden">
                <div class="mx-auto max-w-7xl px-4 py-4">
                    <nav class="grid gap-1 text-sm">
                        <Link
                            v-for="item in navItems"
                            :key="item.href"
                            :href="item.href"
                            class="rounded-xl px-4 py-3 font-medium transition"
                            :class="
                                isCurrentUrl(item.href)
                                    ? 'bg-muted text-foreground'
                                    : 'text-muted-foreground hover:bg-muted/60 hover:text-foreground'
                            "
                            @click="closeMobileMenu"
                        >
                            {{ item.label }}
                        </Link>
                        <Link
                            href="/inscription/candidats"
                            class="mt-2 rounded-xl bg-primary px-4 py-3 text-center font-semibold text-primary-foreground shadow-sm ring-1 ring-primary/20 hover:bg-primary/90"
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

        <footer class="border-t border-border/60 bg-gradient-to-b from-background to-muted/20">
            <div class="mx-auto max-w-7xl px-4 py-12">
                <div class="grid gap-10 md:grid-cols-12">
                    <div class="min-w-0 md:col-span-5">
                        <div
                            class="text-base font-semibold text-foreground"
                            style="font-family: 'Playfair Display', ui-serif, Georgia, serif;"
                        >
                            {{ footerBrand }}
                        </div>
                        <div v-if="footerDescription" class="mt-2 break-words text-sm text-muted-foreground [overflow-wrap:anywhere]">
                            {{ footerDescription }}
                        </div>
                        <div class="mt-2 break-words text-sm text-muted-foreground [overflow-wrap:anywhere]">
                            {{ footerCopyright }}
                        </div>
                    </div>

                    <div class="min-w-0 md:col-span-4">
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

                    <div class="min-w-0 md:col-span-3">
                        <div class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                            Informations
                        </div>
                        <div class="mt-3 grid gap-2 text-sm">
                            <a
                                v-if="rulesUrl"
                                :href="rulesUrl"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-muted-foreground hover:text-foreground"
                            >
                                Mentions légales
                            </a>
                            <Link v-else href="/mentions-legales" class="text-muted-foreground hover:text-foreground">Mentions légales</Link>

                            <a
                                v-if="privacyPolicyUrl"
                                :href="privacyPolicyUrl"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-muted-foreground hover:text-foreground"
                            >
                                Confidentialité
                            </a>
                            <Link v-else href="/confidentialite" class="text-muted-foreground hover:text-foreground">Confidentialité</Link>
                            <a
                                v-if="instagramUrl"
                                :href="instagramUrl"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-2 text-muted-foreground transition hover:text-foreground"
                            >
                                <Instagram class="h-4 w-4" />
                                Instagram
                            </a>
                        </div>
                    </div>
                </div>

                <div class="mt-10 border-t border-border/60 pt-6">
                    <div class="flex flex-col gap-2 text-xs text-muted-foreground sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            Tous droits réservés.
                        </div>
                        <a
                            href="https://www.m4entreprise.be"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="m4-credit w-fit"
                        >
                            <span class="m4-credit__label">Un site web développé par</span>
                            <span class="m4-credit__brand">M4 Entreprise</span>
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
.m4-credit {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    border-radius: 9999px;
    padding: 0.5rem 0.8rem;
    color: rgba(51, 65, 85, 0.95);
    text-decoration: none;
    background: rgba(255, 255, 255, 0.55);
    border: 1px solid rgba(15, 23, 42, 0.12);
    box-shadow:
        0 10px 22px rgba(15, 23, 42, 0.06),
        0 2px 10px rgba(15, 23, 42, 0.04);
    backdrop-filter: blur(8px);
    transform: translateZ(0);
    transition:
        transform 180ms ease,
        box-shadow 180ms ease,
        border-color 180ms ease;
}

.m4-credit::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    padding: 1px;
    background: linear-gradient(
        90deg,
        rgba(15, 23, 42, 0.08),
        rgba(180, 83, 9, 0.16),
        rgba(15, 23, 42, 0.08)
    );
    background-size: 220% 100%;
    opacity: 0.95;
    pointer-events: none;
    -webkit-mask:
        linear-gradient(#000 0 0) content-box,
        linear-gradient(#000 0 0);
    mask:
        linear-gradient(#000 0 0) content-box,
        linear-gradient(#000 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    animation: m4-border 7.5s ease-in-out infinite;
}

.m4-credit::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.35), transparent);
    background-size: 220px 100%;
    opacity: 0.35;
    pointer-events: none;
    mask-image: linear-gradient(to right, transparent, black 15%, black 85%, transparent);
    animation: m4-shimmer 3.2s linear infinite;
}

.m4-credit:hover {
    transform: translateY(-1px) scale(1.01);
    border-color: rgba(15, 23, 42, 0.18);
    box-shadow:
        0 14px 30px rgba(15, 23, 42, 0.10),
        0 4px 14px rgba(180, 83, 9, 0.08);
}

.m4-credit:focus-visible {
    outline: 2px solid rgba(180, 83, 9, 0.40);
    outline-offset: 3px;
}

.m4-credit__label {
    font-weight: 600;
    letter-spacing: 0.04em;
    opacity: 0.92;
}

.m4-credit__brand {
    font-weight: 800;
    letter-spacing: 0.02em;
    color: rgba(15, 23, 42, 0.92);
    text-shadow: 0 1px 0 rgba(255, 255, 255, 0.65);
}

@keyframes m4-shimmer {
    from {
        background-position: -220px 0;
    }
    to {
        background-position: calc(100% + 220px) 0;
    }
}

@keyframes m4-border {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

@media (prefers-reduced-motion: reduce) {
    .m4-credit,
    .m4-credit::before,
    .m4-credit::after {
        animation: none;
    }
}
</style>
