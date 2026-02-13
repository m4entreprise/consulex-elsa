<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import FlashMessages from '@/components/FlashMessages.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';

type JuryMember = {
    id: number;
    name: string;
    role: string;
    detail: string | null;
    photo_path: string | null;
    photo_url?: string | null;
    is_active: boolean;
    sort_order: number;
};

defineProps<{ juryMembers: JuryMember[] }>();
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
                    <div class="text-sm font-medium">Liste</div>

                    <div class="mt-4 grid gap-3">
                        <div
                            v-for="j in juryMembers"
                            :key="j.id"
                            class="rounded-xl border border-border/60 bg-background/40 p-4"
                        >
                            <div class="grid gap-3">
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

                                    <div class="grid gap-2 sm:grid-cols-2">
                                        <div class="grid gap-2">
                                            <Label :for="`sort_${j.id}`">Ordre</Label>
                                            <Input :id="`sort_${j.id}`" name="sort_order" type="number" min="0" :default-value="j.sort_order" />
                                            <InputError :message="errors.sort_order" />
                                        </div>

                                        <label class="mt-7 flex items-center gap-3 text-sm">
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

                        <div v-if="juryMembers.length === 0" class="text-sm text-muted-foreground">
                            Aucun membre du jury.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
