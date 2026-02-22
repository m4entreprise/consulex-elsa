<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import FlashMessages from '@/components/FlashMessages.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import Draggable from 'vuedraggable';
import { GripVertical } from 'lucide-vue-next';

type FoodOption = {
    id: number;
    label: string;
    sort_order: number;
    is_active: boolean;
};

const props = defineProps<{ foodOptions: FoodOption[] }>();

const foodOptionItems = ref<FoodOption[]>([...props.foodOptions]);
const foodOptionIds = computed(() => foodOptionItems.value.map((o) => o.id));

watch(
    () => props.foodOptions,
    (next) => {
        foodOptionItems.value = [...next];
    },
    { deep: true },
);
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
                            <label class="flex items-center gap-3 text-sm">
                                <input type="checkbox" name="is_active" class="size-4" checked />
                                <span>Actif</span>
                            </label>
                        </div>

                        <Button type="submit" :disabled="processing">Créer</Button>
                    </Form>
                </div>

                <div class="rounded-xl border border-border/60 bg-card p-6">
                    <div class="flex items-center justify-between gap-3">
                        <div class="text-sm font-medium">Liste</div>

                        <Form
                            action="/admin/food-options/reorder"
                            method="post"
                            class="flex items-center gap-2"
                            v-slot="{ processing }"
                            :options="{ preserveScroll: true }"
                        >
                            <input v-for="id in foodOptionIds" :key="id" type="hidden" name="ids[]" :value="id" />
                            <Button type="submit" variant="outline" size="sm" :disabled="processing || foodOptionItems.length === 0">
                                Enregistrer l'ordre
                            </Button>
                        </Form>
                    </div>

                    <Draggable
                        v-if="foodOptionItems.length"
                        v-model="foodOptionItems"
                        item-key="id"
                        handle=".drag-handle"
                        class="mt-4 grid gap-3"
                    >
                        <template #item="{ element: o }">
                            <div class="rounded-xl border border-border/60 bg-background/40 p-4">
                                <div class="grid gap-3">
                                    <button
                                        type="button"
                                        class="drag-handle inline-flex w-fit items-center gap-2 rounded-md border border-border/60 bg-background px-2 py-1 text-xs text-muted-foreground"
                                    >
                                        <GripVertical class="h-4 w-4" />
                                        Glisser pour réordonner
                                    </button>

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
                                        <label class="flex items-center gap-3 text-sm">
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

                        </template>
                    </Draggable>

                    <div v-else class="mt-4 text-sm text-muted-foreground">
                        Aucune option.
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
