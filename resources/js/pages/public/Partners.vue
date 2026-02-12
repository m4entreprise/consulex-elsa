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
};

defineProps<{
    settings: Settings;
    partners: Partner[];
}>();
</script>

<template>
    <PublicLayout title="Partenaires">
        <header class="mb-8">
            <h1 class="text-3xl font-semibold tracking-tight">Partenaires</h1>
            <p class="mt-2 text-sm text-muted-foreground">
                Merci à celles et ceux qui rendent l'édition possible.
            </p>
        </header>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="p in partners"
                :key="p.id"
                class="rounded-2xl border border-border/60 bg-card p-6"
            >
                <div class="text-sm font-semibold">{{ p.name }}</div>
                <div class="mt-2 text-sm text-muted-foreground">
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
                        Visiter
                    </a>
                </div>
            </div>

            <div
                v-if="partners.length === 0"
                class="rounded-2xl border border-border/60 bg-card p-6 text-sm text-muted-foreground"
            >
                Aucun partenaire n'est encore renseigné.
            </div>
        </div>
    </PublicLayout>
</template>
