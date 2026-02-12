<script setup lang="ts">
import { Form, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Settings = {
    spectator_capacity: number;
    spectator_registrations_enabled: boolean;
    privacy_policy_url: string | null;
    rules_url: string | null;
};

type FoodOption = {
    id: number;
    label: string;
};

defineProps<{
    settings: Settings;
    foodOptions: FoodOption[];
    seatsUsed: number;
    seatsRemaining: number;
}>();
</script>

<template>
    <PublicLayout title="Inscription Spectateurs">
        <div class="grid gap-8 lg:grid-cols-12">
            <div class="lg:col-span-5">
                <h1 class="text-3xl font-semibold tracking-tight">Spectateurs</h1>
                <p class="mt-2 text-sm text-muted-foreground">
                    Réserve ta place. Les accompagnants sont limités à 5.
                </p>

                <div class="mt-6 rounded-2xl border border-border/60 bg-card p-6">
                    <div class="text-xs uppercase tracking-wide text-muted-foreground">
                        Jauge
                    </div>
                    <div class="mt-2 text-2xl font-semibold">
                        {{ seatsRemaining }} restantes
                    </div>
                    <div class="mt-1 text-sm text-muted-foreground">
                        {{ seatsUsed }} occupées / {{ settings.spectator_capacity }}
                    </div>

                    <div
                        v-if="!settings.spectator_registrations_enabled || seatsRemaining === 0"
                        class="mt-4 text-sm text-red-600"
                    >
                        Les inscriptions spectateurs sont clôturées.
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7">
                <div class="rounded-2xl border border-border/60 bg-card p-6 md:p-8">
                    <Form
                        action="/inscription/spectateurs"
                        method="post"
                        class="grid gap-5"
                        v-slot="{ errors, processing }"
                        :options="{ preserveScroll: true }"
                    >
                        <div class="grid gap-2">
                            <Label for="full_name">Nom et prénom</Label>
                            <Input id="full_name" name="full_name" required />
                            <InputError :message="errors.full_name" />
                        </div>

                        <div class="grid gap-2 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="email">Email</Label>
                                <Input id="email" name="email" type="email" required />
                                <InputError :message="errors.email" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="phone">Téléphone</Label>
                                <Input id="phone" name="phone" required />
                                <InputError :message="errors.phone" />
                            </div>
                        </div>

                        <div class="grid gap-2 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="accompanying_count">Accompagnants (0-5)</Label>
                                <Input
                                    id="accompanying_count"
                                    name="accompanying_count"
                                    type="number"
                                    min="0"
                                    max="5"
                                    default-value="0"
                                />
                                <InputError :message="errors.accompanying_count" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="food_option_id">Nourriture</Label>
                                <select
                                    id="food_option_id"
                                    name="food_option_id"
                                    class="h-9 w-full rounded-md border border-input bg-transparent px-3 text-sm"
                                >
                                    <option value="">Aucune</option>
                                    <option v-for="o in foodOptions" :key="o.id" :value="o.id">
                                        {{ o.label }}
                                    </option>
                                </select>
                                <InputError :message="errors.food_option_id" />
                            </div>
                        </div>

                        <div class="grid gap-3">
                            <Label class="flex items-center gap-3">
                                <input
                                    type="checkbox"
                                    name="accepted_rgpd"
                                    value="1"
                                    class="size-4"
                                />
                                <span class="text-sm text-muted-foreground">
                                    J'accepte la politique de confidentialité
                                    <a
                                        v-if="settings.privacy_policy_url"
                                        :href="settings.privacy_policy_url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="underline underline-offset-4"
                                    >
                                        (PDF)
                                    </a>
                                </span>
                            </Label>
                            <InputError :message="errors.accepted_rgpd" />

                            <Label class="flex items-center gap-3">
                                <input
                                    type="checkbox"
                                    name="accepted_rules"
                                    value="1"
                                    class="size-4"
                                />
                                <span class="text-sm text-muted-foreground">
                                    J'accepte le règlement
                                    <a
                                        v-if="settings.rules_url"
                                        :href="settings.rules_url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="underline underline-offset-4"
                                    >
                                        (PDF)
                                    </a>
                                </span>
                            </Label>
                            <InputError :message="errors.accepted_rules" />
                        </div>

                        <Button
                            type="submit"
                            class="mt-2 h-11 rounded-xl"
                            :disabled="processing || !settings.spectator_registrations_enabled || seatsRemaining === 0"
                        >
                            Valider l'inscription
                        </Button>

                        <div class="text-sm text-muted-foreground">
                            Tu préfères candidater ?
                            <Link href="/inscription/candidats" class="underline underline-offset-4">
                                Inscription candidats
                            </Link>
                        </div>
                    </Form>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
