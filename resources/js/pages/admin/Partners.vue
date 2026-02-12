<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import FlashMessages from '@/components/FlashMessages.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Partner = {
    id: number;
    name: string;
    url: string | null;
    description: string | null;
    logo_path: string | null;
    is_featured: boolean;
    sort_order: number;
};

defineProps<{ partners: Partner[] }>();
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
                            <div class="grid gap-2">
                                <Label for="sort_order">Ordre</Label>
                                <Input id="sort_order" name="sort_order" type="number" min="0" default-value="0" />
                                <InputError :message="errors.sort_order" />
                            </div>

                            <label class="mt-7 flex items-center gap-3 text-sm">
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

                    <div class="mt-3 text-xs text-muted-foreground">
                        Pense à exécuter <code>php artisan storage:link</code> pour servir les logos.
                    </div>
                </div>

                <div class="rounded-xl border border-border/60 bg-card p-6">
                    <div class="text-sm font-medium">Liste</div>

                    <div class="mt-4 grid gap-3">
                        <div
                            v-for="p in partners"
                            :key="p.id"
                            class="rounded-xl border border-border/60 bg-background/40 p-4"
                        >
                            <div class="grid gap-3">
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
                                        <div class="grid gap-2">
                                            <Label :for="`sort_${p.id}`">Ordre</Label>
                                            <Input :id="`sort_${p.id}`" name="sort_order" type="number" min="0" :default-value="p.sort_order" />
                                            <InputError :message="errors.sort_order" />
                                        </div>

                                        <label class="mt-7 flex items-center gap-3 text-sm">
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

                        <div v-if="partners.length === 0" class="text-sm text-muted-foreground">
                            Aucun partenaire.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
