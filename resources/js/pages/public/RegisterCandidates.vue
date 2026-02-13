<script setup lang="ts">
import { Form, Link } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import PublicLayout from '@/layouts/PublicLayout.vue';

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
    <PublicLayout title="Inscription Candidats" :container="false">
        <section class="relative overflow-hidden bg-[#f7f4ee] py-16 sm:py-20 lg:py-24">
            <div class="absolute inset-0" aria-hidden="true">
                <div
                    class="absolute inset-0 bg-[radial-gradient(1200px_circle_at_18%_12%,rgba(15,23,42,0.06),transparent_58%),radial-gradient(900px_circle_at_88%_6%,rgba(180,83,9,0.08),transparent_62%)]"
                />
                <div
                    class="absolute inset-0 bg-[linear-gradient(to_bottom,rgba(247,244,238,0.62),rgba(247,244,238,0.86),rgba(247,244,238,0.96))]"
                />
            </div>

            <div class="relative mx-auto max-w-7xl px-4">
                <div class="mb-10">
                    <p class="text-xs font-semibold tracking-[0.34em] text-slate-700">
                        ConsuLex ULiège x ELSA Liège
                    </p>
                    <h1
                        class="mt-5 text-balance leading-tight tracking-[-0.02em] text-slate-950"
                        style="font-family: 'Playfair Display', ui-serif, Georgia, serif; font-size: clamp(2.25rem, 5vw, 4.25rem);"
                    >
                        Inscription candidats
                    </h1>
                    <p class="mt-3 max-w-2xl text-sm font-medium leading-relaxed text-slate-700 sm:text-base">
                        Dépose ton texte (PDF) et ta photo.
                    </p>
                </div>

                <div class="grid gap-8 lg:grid-cols-12 lg:items-start">
                    <div class="lg:col-span-5">
                        <div class="rounded-2xl border border-slate-900/10 bg-white/55 p-6 shadow-sm">
                            <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-600">
                                Quota
                            </div>
                            <div class="mt-3 text-2xl font-semibold text-slate-950">
                                {{ candidatesRemaining }} restantes
                            </div>
                            <div class="mt-1 text-sm text-slate-600">
                                {{ candidatesUsed }} inscrits / {{ settings.candidate_capacity }}
                            </div>

                            <div
                                v-if="!settings.candidate_registrations_enabled || candidatesRemaining === 0"
                                class="mt-4 text-sm font-medium text-red-700"
                            >
                                Les inscriptions candidats sont clôturées.
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-7">
                        <div class="rounded-2xl border border-slate-900/10 bg-white/55 p-6 shadow-sm md:p-8">
                            <Form
                                action="/inscription/candidats"
                                method="post"
                                enctype="multipart/form-data"
                                class="grid gap-5"
                                v-slot="{ errors, processing }"
                                :options="{ preserveScroll: true }"
                            >
                                <div class="grid gap-2">
                                    <Label for="full_name" class="text-slate-900">Nom et prénom</Label>
                                    <Input
                                        id="full_name"
                                        name="full_name"
                                        required
                                        class="border-slate-900/10 bg-white/55 text-slate-900 placeholder:text-slate-500 shadow-sm"
                                    />
                                    <InputError :message="errors.full_name" />
                                </div>

                                <div class="grid gap-2 sm:grid-cols-2">
                                    <div class="grid gap-2">
                                        <Label for="email" class="text-slate-900">Email</Label>
                                        <Input
                                            id="email"
                                            name="email"
                                            type="email"
                                            required
                                            class="border-slate-900/10 bg-white/55 text-slate-900 placeholder:text-slate-500 shadow-sm"
                                        />
                                        <InputError :message="errors.email" />
                                    </div>
                                    <div class="grid gap-2">
                                        <Label for="phone" class="text-slate-900">Téléphone</Label>
                                        <Input
                                            id="phone"
                                            name="phone"
                                            required
                                            class="border-slate-900/10 bg-white/55 text-slate-900 placeholder:text-slate-500 shadow-sm"
                                        />
                                        <InputError :message="errors.phone" />
                                    </div>
                                </div>

                                <div class="grid gap-2">
                                    <Label for="faculty" class="text-slate-900">Faculté / établissement</Label>
                                    <Input
                                        id="faculty"
                                        name="faculty"
                                        required
                                        class="border-slate-900/10 bg-white/55 text-slate-900 placeholder:text-slate-500 shadow-sm"
                                    />
                                    <InputError :message="errors.faculty" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="text_pdf" class="text-slate-900">Texte (PDF)</Label>
                                    <input
                                        id="text_pdf"
                                        name="text_pdf"
                                        type="file"
                                        accept="application/pdf"
                                        required
                                        class="block w-full rounded-md border border-slate-900/10 bg-white/55 px-3 py-2 text-sm text-slate-900 shadow-sm file:mr-4 file:rounded-md file:border-0 file:bg-slate-950 file:px-3 file:py-2 file:text-xs file:font-semibold file:tracking-wide file:text-[#f7f4ee]"
                                    />
                                    <InputError :message="errors.text_pdf" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="photo" class="text-slate-900">Photo</Label>
                                    <input
                                        id="photo"
                                        name="photo"
                                        type="file"
                                        accept="image/*"
                                        required
                                        class="block w-full rounded-md border border-slate-900/10 bg-white/55 px-3 py-2 text-sm text-slate-900 shadow-sm file:mr-4 file:rounded-md file:border-0 file:bg-slate-950 file:px-3 file:py-2 file:text-xs file:font-semibold file:tracking-wide file:text-[#f7f4ee]"
                                    />
                                    <InputError :message="errors.photo" />
                                </div>

                                <div class="grid gap-3">
                                    <Label class="flex items-center gap-3">
                                        <input
                                            type="checkbox"
                                            name="accepted_rgpd"
                                            value="1"
                                            class="size-4 accent-slate-950"
                                        />
                                        <span class="text-sm text-slate-700">
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
                                            class="size-4 accent-slate-950"
                                        />
                                        <span class="text-sm text-slate-700">
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
                                    class="group relative mt-2 h-12 w-full justify-center rounded-full px-6 text-sm font-semibold tracking-wide text-[#f7f4ee]"
                                    :disabled="processing || !settings.candidate_registrations_enabled || candidatesRemaining === 0"
                                >
                                    <span
                                        class="absolute inset-0 rounded-full bg-slate-950 transition group-hover:bg-slate-900"
                                        aria-hidden="true"
                                    />
                                    <span
                                        class="absolute inset-0 rounded-full shadow-[0_0_0_1px_rgba(15,23,42,0.18),0_18px_40px_rgba(15,23,42,0.14)]"
                                        aria-hidden="true"
                                    />
                                    <span class="relative">Valider la candidature</span>
                                </Button>

                                <div class="text-sm text-slate-700">
                                    Tu veux juste assister ?
                                    <Link href="/inscription/spectateurs" class="underline underline-offset-4">
                                        Inscription spectateurs
                                    </Link>
                                </div>
                            </Form>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="pointer-events-none absolute left-6 top-6 hidden h-12 w-12 border-l border-t border-slate-900/20 lg:block"
                aria-hidden="true"
            />
            <div
                class="pointer-events-none absolute right-6 top-6 hidden h-12 w-12 border-r border-t border-slate-900/20 lg:block"
                aria-hidden="true"
            />
            <div
                class="pointer-events-none absolute left-6 bottom-6 hidden h-12 w-12 border-l border-b border-slate-900/20 lg:block"
                aria-hidden="true"
            />
            <div
                class="pointer-events-none absolute right-6 bottom-6 hidden h-12 w-12 border-r border-b border-slate-900/20 lg:block"
                aria-hidden="true"
            />
        </section>
    </PublicLayout>
</template>
