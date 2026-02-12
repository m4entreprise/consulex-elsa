<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import FlashMessages from '@/components/FlashMessages.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type FoodOption = {
    id: number;
    label: string;
    sort_order: number;
    is_active: boolean;
};

defineProps<{ foodOptions: FoodOption[] }>();
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Admin', href: '/dashboard' }, { title: 'Nourriture', href: '/admin/food-options' }]">
        <div class="p-4">
            <FlashMessages class="mb-4" />

            <Heading
                title="Options de nourriture"
                description="Options utilisées dans le formulaire spectateurs."
            />

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-xl border border-border/60 bg-card p-6">
                    <div class="text-sm font-medium">Ajouter</div>

                    <Form
                        action="/admin/food-options"
                        method="post"
                        class="mt-4 grid gap-4"
                        v-slot="{ errors, processing }"
                        :options="{ preserveScroll: true }"
                    >
                        <div class="grid gap-2">
                            <Label for="label">Label</Label>
                            <Input id="label" name="label" required />
                            <InputError :message="errors.label" />
                        </div>

                        <div class="grid gap-2 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="sort_order">Ordre</Label>
                                <Input id="sort_order" name="sort_order" type="number" min="0" default-value="0" />
                                <InputError :message="errors.sort_order" />
                            </div>

                            <label class="mt-7 flex items-center gap-3 text-sm">
                                <input type="checkbox" name="is_active" class="size-4" checked />
                                <span>Actif</span>
                            </label>
                        </div>

                        <Button type="submit" :disabled="processing">Créer</Button>
                    </Form>
                </div>

                <div class="rounded-xl border border-border/60 bg-card p-6">
                    <div class="text-sm font-medium">Liste</div>

                    <div class="mt-4 grid gap-3">
                        <div
                            v-for="o in foodOptions"
                            :key="o.id"
                            class="rounded-xl border border-border/60 bg-background/40 p-4"
                        >
                            <div class="grid gap-3">
                                <Form
                                    :action="`/admin/food-options/${o.id}`"
                                    method="post"
                                    class="grid gap-3"
                                    v-slot="{ errors, processing }"
                                    :options="{ preserveScroll: true }"
                                >
                                    <input type="hidden" name="_method" value="PATCH" />

                                    <div class="grid gap-2">
                                        <Label :for="`label_${o.id}`">Label</Label>
                                        <Input :id="`label_${o.id}`" name="label" :default-value="o.label" required />
                                        <InputError :message="errors.label" />
                                    </div>

                                    <div class="grid gap-2 sm:grid-cols-2">
                                        <div class="grid gap-2">
                                            <Label :for="`sort_${o.id}`">Ordre</Label>
                                            <Input :id="`sort_${o.id}`" name="sort_order" type="number" min="0" :default-value="o.sort_order" />
                                            <InputError :message="errors.sort_order" />
                                        </div>

                                        <label class="mt-7 flex items-center gap-3 text-sm">
                                            <input type="checkbox" name="is_active" class="size-4" :checked="o.is_active" />
                                            <span>Actif</span>
                                        </label>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <Button type="submit" :disabled="processing">Mettre à jour</Button>
                                    </div>
                                </Form>

                                <Form
                                    :action="`/admin/food-options/${o.id}`"
                                    method="post"
                                    v-slot="{ processing: deleting }"
                                    :options="{ preserveScroll: true }"
                                >
                                    <input type="hidden" name="_method" value="DELETE" />
                                    <Button type="submit" variant="destructive" :disabled="deleting">Supprimer</Button>
                                </Form>
                            </div>
                        </div>

                        <div v-if="foodOptions.length === 0" class="text-sm text-muted-foreground">
                            Aucune option.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
