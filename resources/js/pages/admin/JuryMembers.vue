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

type JuryMember = {
    id: number;
    name: string;
    role: string;
    detail: string | null;
    description?: string | null;
    photo_path: string | null;
    photo_url?: string | null;
    is_active: boolean;
    sort_order: number;
};

const props = defineProps<{ juryMembers: JuryMember[] }>();

const juryItems = ref<JuryMember[]>([...props.juryMembers]);
const juryIds = computed(() => juryItems.value.map((j) => j.id));

watch(
    () => props.juryMembers,
    (next) => {
        juryItems.value = [...next];
    },
    { deep: true },
);
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Admin', href: '/dashboard' }, { title: 'Jury', href: '/admin/jury-members' }]">
        <div class="p-4">
            <FlashMessages class="mb-4" />

            <Heading
                title="Membres du jury"
                description="Crée et maintiens les membres du jury affichés sur la page d'accueil."
            />

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-xl border border-border/60 bg-card p-6">
                    <div class="text-sm font-medium">Ajouter</div>

                    <Form
                        action="/admin/jury-members"
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
                            <Label for="role">Rôle</Label>
                            <Input id="role" name="role" required />
                            <InputError :message="errors.role" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="detail">Détail</Label>
                            <Input id="detail" name="detail" />
                            <InputError :message="errors.detail" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="description">Description</Label>
                            <textarea
                                id="description"
                                name="description"
                                rows="4"
                                class="block w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs"
                            ></textarea>
                            <InputError :message="errors.description" />
                        </div>

                        <div class="grid gap-2 sm:grid-cols-2">
                            <label class="flex items-center gap-3 text-sm">
                                <input type="checkbox" name="is_active" class="size-4" checked />
                                <span>Actif</span>
                            </label>
                        </div>

                        <div class="grid gap-2">
                            <Label for="photo">Photo (optionnel)</Label>
                            <input
                                id="photo"
                                name="photo"
                                type="file"
                                accept="image/*,.svg"
                                class="block w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                            />
                            <InputError :message="errors.photo" />
                        </div>

                        <Button type="submit" :disabled="processing">Créer</Button>
                    </Form>

                    <div class="mt-3 text-xs text-muted-foreground">
                        Pense à exécuter <code>php artisan storage:link</code> pour servir les photos.
                    </div>
                </div>

                <div class="rounded-xl border border-border/60 bg-card p-6">
                    <div class="flex items-center justify-between gap-3">
                        <div class="text-sm font-medium">Liste</div>

                        <Form
                            action="/admin/jury-members/reorder"
                            method="post"
                            class="flex items-center gap-2"
                            v-slot="{ processing }"
                            :options="{ preserveScroll: true }"
                        >
                            <input v-for="id in juryIds" :key="id" type="hidden" name="ids[]" :value="id" />
                            <Button type="submit" variant="outline" size="sm" :disabled="processing || juryItems.length === 0">
                                Enregistrer l'ordre
                            </Button>
                        </Form>
                    </div>

                    <Draggable
                        v-if="juryItems.length"
                        v-model="juryItems"
                        item-key="id"
                        handle=".drag-handle"
                        class="mt-4 grid gap-3"
                    >
                        <template #item="{ element: j }">
                            <div class="rounded-xl border border-border/60 bg-background/40 p-4">
                                <div class="grid gap-3">
                                    <button
                                        type="button"
                                        class="drag-handle inline-flex w-fit items-center gap-2 rounded-md border border-border/60 bg-background px-2 py-1 text-xs text-muted-foreground"
                                    >
                                        <GripVertical class="h-4 w-4" />
                                        Glisser pour réordonner
                                    </button>

                                    <div v-if="j.photo_url" class="overflow-hidden rounded-lg border border-border/60 bg-background/60">
                                        <img :src="j.photo_url" :alt="j.name" class="h-44 w-full object-cover" loading="lazy" />
                                    </div>

                                <Form
                                    :action="`/admin/jury-members/${j.id}`"
                                    method="post"
                                    enctype="multipart/form-data"
                                    class="grid gap-3"
                                    v-slot="{ errors, processing }"
                                    :options="{ preserveScroll: true }"
                                >
                                    <input type="hidden" name="_method" value="PATCH" />

                                    <div class="grid gap-2">
                                        <Label :for="`name_${j.id}`">Nom</Label>
                                        <Input :id="`name_${j.id}`" name="name" :default-value="j.name" required />
                                        <InputError :message="errors.name" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label :for="`role_${j.id}`">Rôle</Label>
                                        <Input :id="`role_${j.id}`" name="role" :default-value="j.role" required />
                                        <InputError :message="errors.role" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label :for="`detail_${j.id}`">Détail</Label>
                                        <Input :id="`detail_${j.id}`" name="detail" :default-value="j.detail || ''" />
                                        <InputError :message="errors.detail" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label :for="`description_${j.id}`">Description</Label>
                                        <textarea
                                            :id="`description_${j.id}`"
                                            name="description"
                                            rows="4"
                                            class="block w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs"
                                        >{{ j.description || '' }}</textarea>
                                        <InputError :message="errors.description" />
                                    </div>

                                    <div class="grid gap-2 sm:grid-cols-2">
                                        <label class="flex items-center gap-3 text-sm">
                                            <input type="checkbox" name="is_active" class="size-4" :checked="j.is_active" />
                                            <span>Actif</span>
                                        </label>
                                    </div>

                                    <div class="grid gap-2">
                                        <Label :for="`photo_${j.id}`">Photo (optionnel)</Label>
                                        <input
                                            :id="`photo_${j.id}`"
                                            name="photo"
                                            type="file"
                                            accept="image/*,.svg"
                                            class="block w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                                        />
                                        <InputError :message="errors.photo" />
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <Button type="submit" :disabled="processing">Mettre à jour</Button>
                                    </div>
                                </Form>

                                <Form
                                    :action="`/admin/jury-members/${j.id}`"
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
                        Aucun membre du jury.
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
