<script setup lang="ts">
import PublicLayout from '@/layouts/PublicLayout.vue';

type Settings = {
    event_title: string | null;
};

defineProps<{ settings: Settings }>();
</script>

<template>
    <PublicLayout title="Mentions légales">
        <article class="prose prose-neutral max-w-none dark:prose-invert">
            <h1>Mentions légales</h1>
            <p>
                Cette page est un gabarit. Complète-la avec les informations
                légales de l’association / l’organisateur.
            </p>

            <h2>Éditeur</h2>
            <p>
                Nom / raison sociale<br />
                Adresse<br />
                Email de contact
            </p>

            <h2>Hébergement</h2>
            <p>
                Nom de l’hébergeur<br />
                Adresse<br />
                Contact
            </p>

            <h2>Propriété intellectuelle</h2>
            <p>
                Les contenus (textes, visuels, logos) sont protégés.
            </p>
        </article>
    </PublicLayout>
</template>
