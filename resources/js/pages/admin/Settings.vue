<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import FlashMessages from '@/components/FlashMessages.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Settings = {
    event_title: string | null;
    event_theme: string | null;
    event_date: string | null;
    event_location: string | null;
    instagram_url: string | null;
    aftermovie_url: string | null;
    privacy_policy_url: string | null;
    rules_url: string | null;
    spectator_capacity: number;
    candidate_capacity: number;
    spectator_registrations_enabled: boolean;
    candidate_registrations_enabled: boolean;
};

defineProps<{ settings: Settings }>();
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Admin', href: '/dashboard' }, { title: 'Paramètres', href: '/admin/settings' }]">
        <div class="p-4">
            <FlashMessages class="mb-4" />

            <Heading
                title="Paramètres de l'événement"
                description="Contenu public + quotas modifiables (spectateurs/candidats)."
            />

            <div class="max-w-3xl">
                <Form
                    action="/admin/settings"
                    method="post"
                    class="grid gap-6"
                    v-slot="{ errors, processing }"
                    :options="{ preserveScroll: true }"
                >
                    <input type="hidden" name="_method" value="PATCH" />

                    <div class="grid gap-3 rounded-xl border border-border/60 bg-card p-6">
                        <div class="grid gap-2">
                            <Label for="event_theme">Thème</Label>
                            <Input id="event_theme" name="event_theme" :default-value="settings.event_theme || ''" />
                            <InputError :message="errors.event_theme" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="event_title">Accroche / Description courte</Label>
                            <Input id="event_title" name="event_title" :default-value="settings.event_title || ''" />
                            <InputError :message="errors.event_title" />
                        </div>

                        <div class="grid gap-2 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="event_date">Date</Label>
                                <Input id="event_date" name="event_date" :default-value="settings.event_date || ''" />
                                <InputError :message="errors.event_date" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="event_location">Lieu</Label>
                                <Input id="event_location" name="event_location" :default-value="settings.event_location || ''" />
                                <InputError :message="errors.event_location" />
                            </div>
                        </div>

                        <div class="grid gap-2 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="instagram_url">Instagram URL</Label>
                                <Input id="instagram_url" name="instagram_url" :default-value="settings.instagram_url || ''" />
                                <InputError :message="errors.instagram_url" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="aftermovie_url">Aftermovie URL</Label>
                                <Input id="aftermovie_url" name="aftermovie_url" :default-value="settings.aftermovie_url || ''" />
                                <InputError :message="errors.aftermovie_url" />
                            </div>
                        </div>

                        <div class="grid gap-2 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="privacy_policy_url">Politique de confidentialité (PDF URL)</Label>
                                <Input id="privacy_policy_url" name="privacy_policy_url" :default-value="settings.privacy_policy_url || ''" />
                                <InputError :message="errors.privacy_policy_url" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="rules_url">Règlement (PDF URL)</Label>
                                <Input id="rules_url" name="rules_url" :default-value="settings.rules_url || ''" />
                                <InputError :message="errors.rules_url" />
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-3 rounded-xl border border-border/60 bg-card p-6">
                        <div class="grid gap-2 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="spectator_capacity">Jauge spectateurs</Label>
                                <Input id="spectator_capacity" name="spectator_capacity" type="number" min="0" :default-value="settings.spectator_capacity" />
                                <InputError :message="errors.spectator_capacity" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="candidate_capacity">Quota candidats</Label>
                                <Input id="candidate_capacity" name="candidate_capacity" type="number" min="0" :default-value="settings.candidate_capacity" />
                                <InputError :message="errors.candidate_capacity" />
                            </div>
                        </div>

                        <div class="grid gap-3">
                            <label class="flex items-center gap-3 text-sm">
                                <input
                                    type="checkbox"
                                    name="spectator_registrations_enabled"
                                    class="size-4"
                                    :checked="settings.spectator_registrations_enabled"
                                />
                                <span>Inscriptions spectateurs ouvertes</span>
                            </label>

                            <label class="flex items-center gap-3 text-sm">
                                <input
                                    type="checkbox"
                                    name="candidate_registrations_enabled"
                                    class="size-4"
                                    :checked="settings.candidate_registrations_enabled"
                                />
                                <span>Inscriptions candidats ouvertes</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <Button type="submit" :disabled="processing">Enregistrer</Button>
                        <span class="text-sm text-muted-foreground">/admin/settings</span>
                    </div>
                </Form>
            </div>
        </div>
    </AppLayout>
</template>
