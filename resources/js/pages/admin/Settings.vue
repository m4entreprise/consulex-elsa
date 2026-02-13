<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import FlashMessages from '@/components/FlashMessages.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';

type Settings = {
    event_title: string | null;
    event_theme: string | null;
    event_date: string | null;
    event_location: string | null;
    instagram_url: string | null;
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
                    enctype="multipart/form-data"
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

                        <div class="grid gap-2">
                            <Label for="instagram_url">Instagram URL</Label>
                            <Input id="instagram_url" name="instagram_url" :default-value="settings.instagram_url || ''" />
                            <InputError :message="errors.instagram_url" />
                        </div>

                        <div class="grid gap-2 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="privacy_policy_pdf">Politique de confidentialité (PDF)</Label>
                                <input
                                    id="privacy_policy_pdf"
                                    name="privacy_policy_pdf"
                                    type="file"
                                    accept="application/pdf"
                                    class="block w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                                />
                                <InputError :message="errors.privacy_policy_pdf" />
                                <div v-if="settings.privacy_policy_url" class="text-xs text-muted-foreground">
                                    <a :href="settings.privacy_policy_url" target="_blank" rel="noopener noreferrer" class="underline underline-offset-4">
                                        Voir le PDF actuel
                                    </a>
                                </div>
                            </div>
                            <div class="grid gap-2">
                                <Label for="rules_pdf">Règlement (PDF)</Label>
                                <input
                                    id="rules_pdf"
                                    name="rules_pdf"
                                    type="file"
                                    accept="application/pdf"
                                    class="block w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                                />
                                <InputError :message="errors.rules_pdf" />
                                <div v-if="settings.rules_url" class="text-xs text-muted-foreground">
                                    <a :href="settings.rules_url" target="_blank" rel="noopener noreferrer" class="underline underline-offset-4">
                                        Voir le PDF actuel
                                    </a>
                                </div>
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
