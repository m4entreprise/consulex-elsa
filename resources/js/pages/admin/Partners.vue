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

type Partner = {
    id: number;
    name: string;
    url: string | null;
    description: string | null;
    logo_path: string | null;
    is_featured: boolean;
    sort_order: number;
};

const props = defineProps<{ partners: Partner[] }>();

const partnerItems = ref<Partner[]>([...props.partners]);
const partnerIds = computed(() => partnerItems.value.map((p) => p.id));

watch(
    () => props.partners,
    (next) => {
        partnerItems.value = [...next];
    },
    { deep: true },
);
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Admin', href: '/dashboard' }, { title: 'Partenaires', href: '/admin/partners' }]">
        <div class="p-4">
            <FlashMessages class="mb-4" />

            <Heading
                title="Partenaires"
                description="Gestion des partenaires affichés sur la page publique."
            />

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-xl border border-border/60 bg-card p-6">
                    <div class="text-sm font-medium">Ajouter</div>

                    <Form
                        action="/admin/partners"
                        method="post"
                        enctype="multipart/form-data"
                        class="mt-4 grid gap-4"
                        v-slot="{ errors, processing }"
                        :options="{ preserveScroll: true }"
                    >
                        <div class="grid gap-2">
                            <Label for="name">Nom</Label>
                            <Input id="name" name="name" required />
                            <InputError :message="errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="url">URL</Label>
                            <Input id="url" name="url" />
                            <InputError :message="errors.url" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="description">Description</Label>
                            <textarea
                                id="description"
                                name="description"
                                class="min-h-24 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                            ></textarea>
                            <InputError :message="errors.description" />
                        </div>

                        <div class="grid gap-2 sm:grid-cols-2">
                            <label class="flex items-center gap-3 text-sm">
                                <input type="checkbox" name="is_featured" class="size-4" />
                                <span>Mis en avant</span>
                            </label>
                        </div>

                        <div class="grid gap-2">
                            <Label for="logo">Logo (optionnel)</Label>
                            <input
                                id="logo"
                                name="logo"
                                type="file"
                                accept="image/*,.svg"
                                class="block w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                            />
                            <InputError :message="errors.logo" />
                        </div>

                        <Button type="submit" :disabled="processing">Créer</Button>
                    </Form>

                    
                </div>

                <div class="rounded-xl border border-border/60 bg-card p-6">
                    <div class="flex items-center justify-between gap-3">
                        <div class="text-sm font-medium">Liste</div>

                        <Form
                            action="/admin/partners/reorder"
                            method="post"
                            class="flex items-center gap-2"
                            v-slot="{ processing }"
                            :options="{ preserveScroll: true }"
                        >
                            <input v-for="id in partnerIds" :key="id" type="hidden" name="ids[]" :value="id" />
                            <Button type="submit" variant="outline" size="sm" :disabled="processing || partnerItems.length === 0">
                                Enregistrer l'ordre
                            </Button>
                        </Form>
                    </div>

                    <Draggable
                        v-if="partnerItems.length"
                        v-model="partnerItems"
                        item-key="id"
                        handle=".drag-handle"
                        class="mt-4 grid gap-3"
                    >
                        <template #item="{ element: p }">
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
                                    :action="`/admin/partners/${p.id}`"
                                    method="post"
                                    enctype="multipart/form-data"
                                    class="grid gap-3"
                                    v-slot="{ errors, processing }"
                                    :options="{ preserveScroll: true }"
                                >
                                    <input type="hidden" name="_method" value="PATCH" />

                                    <div class="grid gap-2">
                                        <Label :for="`name_${p.id}`">Nom</Label>
                                        <Input :id="`name_${p.id}`" name="name" :default-value="p.name" required />
                                        <InputError :message="errors.name" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label :for="`url_${p.id}`">URL</Label>
                                        <Input :id="`url_${p.id}`" name="url" :default-value="p.url || ''" />
                                        <InputError :message="errors.url" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label :for="`desc_${p.id}`">Description</Label>
                                        <textarea
                                            :id="`desc_${p.id}`"
                                            name="description"
                                            class="min-h-24 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                                            :value="p.description || ''"
                                        ></textarea>
                                        <InputError :message="errors.description" />
                                    </div>

                                    <div class="grid gap-2 sm:grid-cols-2">
                                        <label class="flex items-center gap-3 text-sm">
                                            <input type="checkbox" name="is_featured" class="size-4" :checked="p.is_featured" />
                                            <span>Mis en avant</span>
                                        </label>
                                    </div>

                                    <div class="grid gap-2">
                                        <Label :for="`logo_${p.id}`">Logo (optionnel)</Label>
                                        <input
                                            :id="`logo_${p.id}`"
                                            name="logo"
                                            type="file"
                                            accept="image/*,.svg"
                                            class="block w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                                        />
                                        <InputError :message="errors.logo" />
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <Button type="submit" :disabled="processing">Mettre à jour</Button>
                                    </div>
                                </Form>

                                <Form
                                    :action="`/admin/partners/${p.id}`"
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
                        Aucun partenaire.
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
