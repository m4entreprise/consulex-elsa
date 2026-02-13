<script setup lang="ts">
import FlashMessages from '@/components/FlashMessages.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';

type Settings = {
    spectator_capacity: number;
};

type Registration = {
    id: number;
    full_name: string;
    email: string;
    phone: string;
    accompanying_count: number;
    food_option_label: string | null;
    created_at: string;
};

defineProps<{
    settings: Settings;
    registrations: Registration[];
    seatsUsed: number;
}>();
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
                                <td class="px-4 py-3">{{ r.accompanying_count }}</td>
                                <td class="px-4 py-3">
                                    {{ r.food_option_label || '—' }}
                                </td>
                            </tr>

                            <tr v-if="registrations.length === 0">
                                <td class="px-4 py-8 text-muted-foreground" colspan="6">
                                    Aucune inscription.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
