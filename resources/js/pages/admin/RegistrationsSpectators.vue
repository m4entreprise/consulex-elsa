<script setup lang="ts">
import FlashMessages from '@/components/FlashMessages.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

type Settings = {
    spectator_capacity: number;
};

type Registration = {
    id: number;
    full_name: string;
    email: string;
    phone: string;
    accompanying_count: number;
    accompanying_people: Array<{ first_name: string; last_name: string }> | null;
    food_option_label: string | null;
    created_at: string;
};

defineProps<{
    settings: Settings;
    registrations: Registration[];
    seatsUsed: number;
}>();

const deleteRegistration = (id: number) => {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette inscription ?')) {
        return;
    }

    router.delete(`/admin/registrations/spectators/${id}`, {
        preserveScroll: true,
    });
};

const showAccompanyingDialog = ref(false);
const selectedAccompanying = ref<Array<{ first_name: string; last_name: string }>>([]);

const openAccompanyingDialog = (people: Array<{ first_name: string; last_name: string }> | null) => {
    if (!people || people.length === 0) {
        return;
    }
    selectedAccompanying.value = people;
    showAccompanyingDialog.value = true;
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Admin', href: '/dashboard' }, { title: 'Inscriptions', href: '/admin/registrations/spectators' }, { title: 'Spectateurs' }]">
        <div class="p-4">
            <FlashMessages class="mb-4" />

            <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <Heading
                    title="Inscriptions spectateurs"
                    :description="`${seatsUsed} places occupées / ${settings.spectator_capacity}`"
                />
                <div class="flex items-center gap-3">
                    <Button as-child variant="secondary">
                        <a href="/admin/registrations/spectators/export">Exporter CSV</a>
                    </Button>
                </div>
            </div>

            <div class="mt-6 overflow-hidden rounded-xl border border-border/60 bg-card">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-muted/50 text-left">
                            <tr>
                                <th class="px-4 py-3">Date</th>
                                <th class="px-4 py-3">Nom</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Téléphone</th>
                                <th class="px-4 py-3">Accompagnants</th>
                                <th class="px-4 py-3">Nourriture</th>
                                <th class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="r in registrations"
                                :key="r.id"
                                class="border-t border-border/60"
                            >
                                <td class="px-4 py-3 text-muted-foreground">
                                    {{ r.created_at }}
                                </td>
                                <td class="px-4 py-3 font-medium">{{ r.full_name }}</td>
                                <td class="px-4 py-3">{{ r.email }}</td>
                                <td class="px-4 py-3">{{ r.phone }}</td>
                                <td class="px-4 py-3">
                                    <button
                                        v-if="r.accompanying_count > 0 && r.accompanying_people && r.accompanying_people.length > 0"
                                        class="cursor-pointer font-medium underline underline-offset-4 hover:text-primary"
                                        @click="openAccompanyingDialog(r.accompanying_people)"
                                    >
                                        {{ r.accompanying_count }}
                                    </button>
                                    <span v-else>{{ r.accompanying_count }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    {{ r.food_option_label || '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <Button
                                        variant="destructive"
                                        size="sm"
                                        @click="deleteRegistration(r.id)"
                                    >
                                        Supprimer
                                    </Button>
                                </td>
                            </tr>

                            <tr v-if="registrations.length === 0">
                                <td class="px-4 py-8 text-muted-foreground" colspan="7">
                                    Aucune inscription.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <Dialog v-model:open="showAccompanyingDialog">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Liste des accompagnants</DialogTitle>
                    <DialogDescription>
                        Détails des personnes accompagnantes pour cette inscription.
                    </DialogDescription>
                </DialogHeader>
                <div class="mt-4">
                    <ul class="space-y-2">
                        <li
                            v-for="(person, idx) in selectedAccompanying"
                            :key="idx"
                            class="flex items-center gap-2 rounded-lg border border-border/60 bg-muted/30 px-4 py-2"
                        >
                            <span class="font-medium">{{ idx + 1 }}.</span>
                            <span>{{ person.first_name }} {{ person.last_name }}</span>
                        </li>
                    </ul>
                </div>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
