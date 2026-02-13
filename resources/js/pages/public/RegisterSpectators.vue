<script setup lang="ts">
import { Form, Link } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import PublicLayout from '@/layouts/PublicLayout.vue';

type Settings = {
    spectator_capacity: number;
    spectator_registrations_enabled: boolean;
    privacy_policy_url: string | null;
    rules_url: string | null;
    event_title?: string | null;
    event_theme?: string | null;
    event_date?: string | null;
    event_location?: string | null;
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
    <PublicLayout title="Inscription Spectateurs" :container="false">
        <section class="relative overflow-hidden bg-[#f7f4ee] py-16 sm:py-20 lg:py-24">
            <div class="absolute inset-0" aria-hidden="true">
                <div class="absolute inset-0 bg-[radial-gradient(1200px_circle_at_18%_12%,rgba(15,23,42,0.06),transparent_58%),radial-gradient(900px_circle_at_88%_6%,rgba(180,83,9,0.08),transparent_62%)]" />
                <div class="absolute inset-0 bg-[linear-gradient(to_bottom,rgba(247,244,238,0.70),rgba(247,244,238,0.86),rgba(247,244,238,0.96))]" />
            </div>

            <div class="relative mx-auto max-w-7xl px-4">
                <div class="grid gap-10 lg:grid-cols-12 lg:items-start">
                    <div class="lg:col-span-5">
                        <p class="text-xs font-semibold tracking-[0.34em] text-slate-700">
                            INSCRIPTION
                        </p>

                        <h1
                            class="mt-5 break-words text-balance leading-[0.98] tracking-[-0.02em] text-slate-950 sm:leading-[0.92]"
                            style="font-family: 'Playfair Display', ui-serif, Georgia, serif; font-size: clamp(2.2rem, 7vw, 4.75rem);"
                        >
                            Spectateurs
                        </h1>

                        <p class="mt-4 max-w-xl text-sm font-medium leading-relaxed text-slate-700 sm:text-base">
                            Réserve ta place. Les accompagnants sont limités à 5.
                        </p>

                        <div class="mt-8 overflow-hidden rounded-2xl border border-slate-900/10 bg-white/55 shadow-sm">
                            <div class="border-b border-slate-900/10 px-5 py-4">
                                <div class="text-[11px] uppercase tracking-[0.24em] text-slate-600">Jauge</div>
                                <div class="mt-2 text-base font-semibold text-slate-950">
                                    {{ seatsRemaining }} restantes
                                </div>
                                <div class="mt-1 text-sm text-slate-700">
                                    {{ seatsUsed }} occupées / {{ settings.spectator_capacity }}
                                </div>
                            </div>

                            <div class="px-5 py-4">
                                <div
                                    v-if="!settings.spectator_registrations_enabled || seatsRemaining === 0"
                                    class="rounded-xl border border-red-900/10 bg-red-500/10 p-4 text-sm text-red-900"
                                >
                                    Les inscriptions spectateurs sont clôturées.
                                </div>
                                <div v-else class="text-sm text-slate-600">
                                    Les places sont attribuées dans la limite de la jauge.
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 text-sm text-slate-600">
                            Tu préfères candidater ?
                            <Link href="/inscription/candidats" class="text-slate-700 underline underline-offset-4 hover:text-slate-950">
                                Inscription candidats
                            </Link>
                        </div>
                    </div>

                    <div class="lg:col-span-7">
                        <div class="overflow-hidden rounded-2xl border border-slate-900/10 bg-white/55 shadow-sm">
                            <div class="border-b border-slate-900/10 px-5 py-4">
                                <div class="text-[11px] uppercase tracking-[0.24em] text-slate-600">Formulaire</div>
                                <div class="mt-2 text-base font-semibold text-slate-950">
                                    Informations
                                </div>
                            </div>

                            <div class="p-5 sm:p-6">
                                <Form
                                    action="/inscription/spectateurs"
                                    method="post"
                                    class="grid gap-5"
                                    v-slot="{ errors, processing }"
                                    :options="{ preserveScroll: true }"
                                >
                                    <div class="grid gap-2">
                                        <Label for="full_name" class="text-sm font-semibold text-slate-950">Nom et prénom</Label>
                                        <Input
                                            id="full_name"
                                            name="full_name"
                                            required
                                            class="h-11 rounded-xl border-slate-900/15 bg-white/60 px-4 text-slate-950 shadow-sm focus-visible:border-slate-900/25 focus-visible:ring-slate-900/15"
                                        />
                                        <InputError :message="errors.full_name" />
                                    </div>

                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div class="grid gap-2">
                                            <Label for="email" class="text-sm font-semibold text-slate-950">Email</Label>
                                            <Input
                                                id="email"
                                                name="email"
                                                type="email"
                                                required
                                                class="h-11 rounded-xl border-slate-900/15 bg-white/60 px-4 text-slate-950 shadow-sm focus-visible:border-slate-900/25 focus-visible:ring-slate-900/15"
                                            />
                                            <InputError :message="errors.email" />
                                        </div>
                                        <div class="grid gap-2">
                                            <Label for="phone" class="text-sm font-semibold text-slate-950">Téléphone</Label>
                                            <Input
                                                id="phone"
                                                name="phone"
                                                required
                                                class="h-11 rounded-xl border-slate-900/15 bg-white/60 px-4 text-slate-950 shadow-sm focus-visible:border-slate-900/25 focus-visible:ring-slate-900/15"
                                            />
                                            <InputError :message="errors.phone" />
                                        </div>
                                    </div>

                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div class="grid gap-2">
                                            <Label for="accompanying_count" class="text-sm font-semibold text-slate-950">
                                                Accompagnants (0-5)
                                            </Label>
                                            <Input
                                                id="accompanying_count"
                                                name="accompanying_count"
                                                type="number"
                                                min="0"
                                                max="5"
                                                default-value="0"
                                                class="h-11 rounded-xl border-slate-900/15 bg-white/60 px-4 text-slate-950 shadow-sm focus-visible:border-slate-900/25 focus-visible:ring-slate-900/15"
                                            />
                                            <InputError :message="errors.accompanying_count" />
                                        </div>

                                        <div class="grid gap-2">
                                            <Label for="food_option_id" class="text-sm font-semibold text-slate-950">Nourriture</Label>
                                            <select
                                                id="food_option_id"
                                                name="food_option_id"
                                                class="h-11 w-full rounded-xl border border-slate-900/15 bg-white/60 px-4 text-sm text-slate-950 shadow-sm transition focus:outline-none focus-visible:border-slate-900/25 focus-visible:ring-2 focus-visible:ring-slate-900/15"
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
                                        <Label class="flex items-start gap-3">
                                            <input
                                                type="checkbox"
                                                name="accepted_rgpd"
                                                value="1"
                                                class="mt-0.5 size-4 rounded border-slate-900/20 bg-white/60 text-slate-950"
                                            />
                                            <span class="text-sm text-slate-700">
                                                J'accepte la politique de confidentialité
                                                <a
                                                    v-if="settings.privacy_policy_url"
                                                    :href="settings.privacy_policy_url"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="text-slate-700 underline underline-offset-4 hover:text-slate-950"
                                                >
                                                    (PDF)
                                                </a>
                                            </span>
                                        </Label>
                                        <InputError :message="errors.accepted_rgpd" />

                                        <Label class="flex items-start gap-3">
                                            <input
                                                type="checkbox"
                                                name="accepted_rules"
                                                value="1"
                                                class="mt-0.5 size-4 rounded border-slate-900/20 bg-white/60 text-slate-950"
                                            />
                                            <span class="text-sm text-slate-700">
                                                J'accepte le règlement
                                                <a
                                                    v-if="settings.rules_url"
                                                    :href="settings.rules_url"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="text-slate-700 underline underline-offset-4 hover:text-slate-950"
                                                >
                                                    (PDF)
                                                </a>
                                            </span>
                                        </Label>
                                        <InputError :message="errors.accepted_rules" />
                                    </div>

                                    <div class="pt-2">
                                        <Button
                                            type="submit"
                                            class="h-12 w-full rounded-full bg-slate-950 px-6 text-sm font-semibold tracking-wide text-[#f7f4ee] shadow-[0_0_0_1px_rgba(15,23,42,0.18),0_18px_40px_rgba(15,23,42,0.14)] hover:bg-slate-900"
                                            :disabled="processing || !settings.spectator_registrations_enabled || seatsRemaining === 0"
                                        >
                                            Valider l'inscription
                                        </Button>
                                    </div>

                                    <div class="text-sm text-slate-600">
                                        Tu veux candidater ?
                                        <Link href="/inscription/candidats" class="text-slate-700 underline underline-offset-4 hover:text-slate-950">
                                            Inscription candidats
                                        </Link>
                                    </div>
                                </Form>
                            </div>
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
