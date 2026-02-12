<script setup lang="ts">
import PublicLayout from '@/layouts/PublicLayout.vue';

type Settings = {
    event_title: string | null;
    privacy_policy_url: string | null;
};

defineProps<{ settings: Settings }>();
</script>

<template>
    <PublicLayout title="Confidentialité">
        <article class="prose prose-neutral max-w-none dark:prose-invert">
            <h1>Confidentialité</h1>
            <p>
                Cette page résume le traitement des données lié aux inscriptions.
            </p>

            <h2>Données collectées</h2>
            <ul>
                <li>Identité (nom, email, téléphone)</li>
                <li>Pour candidats : établissement + fichiers (PDF, photo)</li>
                <li>Consentements (RGPD, règlement)</li>
            </ul>

            <h2>Finalités</h2>
            <p>
                Gestion des inscriptions, organisation de l’événement et
                communications nécessaires.
            </p>

            <h2>Durée de conservation</h2>
            <p>
                À définir selon le cadre de l’événement.
            </p>

            <h2>Document complet</h2>
            <p v-if="settings.privacy_policy_url">
                Politique complète (PDF) :
                <a
                    :href="settings.privacy_policy_url"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    télécharger
                </a>
            </p>
        </article>
    </PublicLayout>
</template>
