<script setup lang="ts">
import { Form, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Settings = {
    candidate_capacity: number;
    candidate_registrations_enabled: boolean;
    privacy_policy_url: string | null;
    rules_url: string | null;
};

defineProps<{
    settings: Settings;
    candidatesUsed: number;
    candidatesRemaining: number;
}>();
</script>

<template>
    <PublicLayout title="Inscription Candidats">
        <div class="grid gap-8 lg:grid-cols-12">
            <div class="lg:col-span-5">
                <h1 class="text-3xl font-semibold tracking-tight">Candidats</h1>
                <p class="mt-2 text-sm text-muted-foreground">
                    Dépose ton texte (PDF) et ta photo.
                </p>

                <div class="mt-6 rounded-2xl border border-border/60 bg-card p-6">
                    <div class="text-xs uppercase tracking-wide text-muted-foreground">
                        Quota
                    </div>
                    <div class="mt-2 text-2xl font-semibold">
                        {{ candidatesRemaining }} restantes
                    </div>
                    <div class="mt-1 text-sm text-muted-foreground">
                        {{ candidatesUsed }} inscrits / {{ settings.candidate_capacity }}
                    </div>

                    <div
                        v-if="!settings.candidate_registrations_enabled || candidatesRemaining === 0"
                        class="mt-4 text-sm text-red-600"
                    >
                        Les inscriptions candidats sont clôturées.
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7">
                <div class="rounded-2xl border border-border/60 bg-card p-6 md:p-8">
                    <Form
                        action="/inscription/candidats"
                        method="post"
                        enctype="multipart/form-data"
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

                        <div class="grid gap-2">
                            <Label for="faculty">Faculté / établissement</Label>
                            <Input id="faculty" name="faculty" required />
                            <InputError :message="errors.faculty" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="text_pdf">Texte (PDF)</Label>
                            <input
                                id="text_pdf"
                                name="text_pdf"
                                type="file"
                                accept="application/pdf"
                                required
                                class="block w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                            />
                            <InputError :message="errors.text_pdf" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="photo">Photo</Label>
                            <input
                                id="photo"
                                name="photo"
                                type="file"
                                accept="image/*"
                                required
                                class="block w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                            />
                            <InputError :message="errors.photo" />
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
                            :disabled="processing || !settings.candidate_registrations_enabled || candidatesRemaining === 0"
                        >
                            Valider la candidature
                        </Button>

                        <div class="text-sm text-muted-foreground">
                            Tu veux juste assister ?
                            <Link href="/inscription/spectateurs" class="underline underline-offset-4">
                                Inscription spectateurs
                            </Link>
                        </div>
                    </Form>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
