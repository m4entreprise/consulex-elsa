<script setup lang="ts">
import { Form, Link } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
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

const props = defineProps<{
    settings: Settings;
    foodOptions: FoodOption[];
    seatsUsed: number;
    seatsRemaining: number;
}>();

const accompanyingCount = ref('0');
const accompanyingCountNumber = computed(() => {
    const parsed = Number.parseInt(accompanyingCount.value, 10);
    if (Number.isNaN(parsed)) return 0;
    return Math.max(0, Math.min(5, parsed));
});

type AccompanyingPerson = {
    first_name: string;
    last_name: string;
};

const accompanyingPeople = ref<AccompanyingPerson[]>([]);

watch(
    accompanyingCountNumber,
    (count) => {
        const next: AccompanyingPerson[] = [];

        for (let i = 0; i < count; i += 1) {
            next.push(
                accompanyingPeople.value[i] ?? {
                    first_name: '',
                    last_name: '',
                },
            );
        }

        accompanyingPeople.value = next;
    },
    { immediate: true },
);

const foodWantedChoice = ref<'0' | '1'>('0');

const foodQuantities = ref<Record<number, number>>({});

watch(
    () => props.foodOptions,
    (options) => {
        const next: Record<number, number> = { ...foodQuantities.value };

        for (const o of options) {
            if (typeof next[o.id] !== 'number') {
                next[o.id] = 0;
            }
        }

        foodQuantities.value = next;
    },
    { immediate: true, deep: true },
);

watch(foodWantedChoice, (choice) => {
    if (choice !== '1') {
        const next: Record<number, number> = { ...foodQuantities.value };
        for (const key of Object.keys(next)) {
            next[Number.parseInt(key, 10)] = 0;
        }
        foodQuantities.value = next;
    }
});

function incrementFood(id: number) {
    const current = foodQuantities.value[id] ?? 0;
    foodQuantities.value = {
        ...foodQuantities.value,
        [id]: Math.min(20, current + 1),
    };
}

function decrementFood(id: number) {
    const current = foodQuantities.value[id] ?? 0;
    foodQuantities.value = {
        ...foodQuantities.value,
        [id]: Math.max(0, current - 1),
    };
}
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
                                                v-model="accompanyingCount"
                                                class="h-11 rounded-xl border-slate-900/15 bg-white/60 px-4 text-slate-950 shadow-sm focus-visible:border-slate-900/25 focus-visible:ring-slate-900/15"
                                            />
                                            <InputError :message="errors.accompanying_count" />
                                            <InputError :message="errors.accompanying_people" />
                                        </div>

                                        <div class="grid gap-2">
                                            <div class="text-sm font-semibold text-slate-950">Nourriture</div>

                                            <div class="grid gap-2">
                                                <label class="flex items-center gap-2 text-sm text-slate-700">
                                                    <input
                                                        type="radio"
                                                        name="food_wanted"
                                                        value="0"
                                                        v-model="foodWantedChoice"
                                                        class="size-4 rounded border-slate-900/20 bg-white/60 text-slate-950"
                                                    />
                                                    <span>Non</span>
                                                </label>

                                                <label class="flex items-center gap-2 text-sm text-slate-700">
                                                    <input
                                                        type="radio"
                                                        name="food_wanted"
                                                        value="1"
                                                        v-model="foodWantedChoice"
                                                        class="size-4 rounded border-slate-900/20 bg-white/60 text-slate-950"
                                                    />
                                                    <span>Oui</span>
                                                </label>
                                            </div>

                                            <InputError :message="errors.food_wanted" />
                                            <InputError :message="errors.food_quantities" />
                                        </div>
                                    </div>

                                    <div v-if="accompanyingCountNumber > 0" class="grid gap-4 rounded-2xl border border-slate-900/10 bg-white/50 p-4">
                                        <div>
                                            <div class="text-sm font-semibold text-slate-950">Accompagnants</div>
                                            <div class="mt-1 text-sm text-slate-600">
                                                Renseigne le nom et le prénom de chaque accompagnant.
                                            </div>
                                        </div>

                                        <div class="grid gap-4">
                                            <div
                                                v-for="(p, idx) in accompanyingPeople"
                                                :key="idx"
                                                class="grid gap-3 rounded-xl border border-slate-900/10 bg-white/40 p-4"
                                            >
                                                <div class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-600">
                                                    Accompagnant {{ idx + 1 }}
                                                </div>

                                                <div class="grid gap-3 sm:grid-cols-2">
                                                    <div class="grid gap-2">
                                                        <Label :for="`acc_first_${idx}`" class="text-sm font-semibold text-slate-950">Prénom</Label>
                                                        <Input
                                                            :id="`acc_first_${idx}`"
                                                            :name="`accompanying_people[${idx}][first_name]`"
                                                            v-model="p.first_name"
                                                            required
                                                            class="h-11 rounded-xl border-slate-900/15 bg-white/60 px-4 text-slate-950 shadow-sm focus-visible:border-slate-900/25 focus-visible:ring-slate-900/15"
                                                        />
                                                        <InputError :message="errors[`accompanying_people.${idx}.first_name`]" />
                                                    </div>

                                                    <div class="grid gap-2">
                                                        <Label :for="`acc_last_${idx}`" class="text-sm font-semibold text-slate-950">Nom</Label>
                                                        <Input
                                                            :id="`acc_last_${idx}`"
                                                            :name="`accompanying_people[${idx}][last_name]`"
                                                            v-model="p.last_name"
                                                            required
                                                            class="h-11 rounded-xl border-slate-900/15 bg-white/60 px-4 text-slate-950 shadow-sm focus-visible:border-slate-900/25 focus-visible:ring-slate-900/15"
                                                        />
                                                        <InputError :message="errors[`accompanying_people.${idx}.last_name`]" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="foodWantedChoice === '1'" class="grid gap-4 rounded-2xl border border-slate-900/10 bg-white/50 p-4">
                                        <div>
                                            <div class="text-sm font-semibold text-slate-950">Choix nourriture</div>
                                            <div class="mt-1 text-sm text-slate-600">
                                                Indique les quantités pour chaque option.
                                            </div>
                                        </div>

                                        <div class="grid gap-3">
                                            <div
                                                v-for="o in foodOptions"
                                                :key="o.id"
                                                class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-900/10 bg-white/40 px-4 py-3"
                                            >
                                                <div class="min-w-[12rem] text-sm font-medium text-slate-950">
                                                    {{ o.label }}
                                                </div>

                                                <div class="flex items-center gap-2">
                                                    <button
                                                        type="button"
                                                        class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-900/15 bg-white/70 text-slate-950 shadow-sm hover:bg-white"
                                                        @click="decrementFood(o.id)"
                                                    >
                                                        -
                                                    </button>

                                                    <div class="w-10 text-center text-sm font-semibold tabular-nums text-slate-950">
                                                        {{ foodQuantities[o.id] ?? 0 }}
                                                    </div>

                                                    <button
                                                        type="button"
                                                        class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-900/15 bg-white/70 text-slate-950 shadow-sm hover:bg-white"
                                                        @click="incrementFood(o.id)"
                                                    >
                                                        +
                                                    </button>
                                                </div>

                                                <input type="hidden" :name="`food_quantities[${o.id}]`" :value="foodQuantities[o.id] ?? 0" />
                                            </div>
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
