<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import FlashMessages from '@/components/FlashMessages.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';

type AfterMovie = {
    id: number;
    date: string;
    location: string;
    theme: string;
    winner: string;
    aftermovie_url: string | null;
};

defineProps<{ afterMovies: AfterMovie[] }>();
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Admin', href: '/dashboard' }, { title: 'After movies', href: '/admin/after-movies' }]">
        <div class="p-4">
            <FlashMessages class="mb-4" />

            <Heading
                title="After movies"
                description="Ajoute et maintiens les éditions précédentes (date, lieu, thème, vainqueur, lien)."
            />

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-xl border border-border/60 bg-card p-6">
                    <div class="text-sm font-medium">Ajouter</div>

                    <Form
                        action="/admin/after-movies"
                        method="post"
                        class="mt-4 grid gap-4"
                        v-slot="{ errors, processing }"
                        :options="{ preserveScroll: true }"
                    >
                        <div class="grid gap-2">
                            <Label for="date">Date</Label>
                            <Input id="date" name="date" type="date" required />
                            <InputError :message="errors.date" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="location">Lieu</Label>
                            <Input id="location" name="location" required />
                            <InputError :message="errors.location" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="theme">Thème</Label>
                            <Input id="theme" name="theme" required />
                            <InputError :message="errors.theme" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="winner">Vainqueur</Label>
                            <Input id="winner" name="winner" required />
                            <InputError :message="errors.winner" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="aftermovie_url">URL aftermovie</Label>
                            <Input id="aftermovie_url" name="aftermovie_url" />
                            <InputError :message="errors.aftermovie_url" />
                        </div>

                        <Button type="submit" :disabled="processing">Créer</Button>
                    </Form>
                </div>

                <div class="rounded-xl border border-border/60 bg-card p-6">
                    <div class="text-sm font-medium">Liste</div>

                    <div class="mt-4 grid gap-3">
                        <div
                            v-for="m in afterMovies"
                            :key="m.id"
                            class="rounded-xl border border-border/60 bg-background/40 p-4"
                        >
                            <div class="grid gap-3">
                                <Form
                                    :action="`/admin/after-movies/${m.id}`"
                                    method="post"
                                    class="grid gap-3"
                                    v-slot="{ errors, processing }"
                                    :options="{ preserveScroll: true }"
                                >
                                    <input type="hidden" name="_method" value="PATCH" />

                                    <div class="grid gap-2">
                                        <Label :for="`date_${m.id}`">Date</Label>
                                        <Input :id="`date_${m.id}`" name="date" type="date" :default-value="m.date" required />
                                        <InputError :message="errors.date" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label :for="`location_${m.id}`">Lieu</Label>
                                        <Input :id="`location_${m.id}`" name="location" :default-value="m.location" required />
                                        <InputError :message="errors.location" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label :for="`theme_${m.id}`">Thème</Label>
                                        <Input :id="`theme_${m.id}`" name="theme" :default-value="m.theme" required />
                                        <InputError :message="errors.theme" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label :for="`winner_${m.id}`">Vainqueur</Label>
                                        <Input :id="`winner_${m.id}`" name="winner" :default-value="m.winner" required />
                                        <InputError :message="errors.winner" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label :for="`aftermovie_${m.id}`">URL aftermovie</Label>
                                        <Input :id="`aftermovie_${m.id}`" name="aftermovie_url" :default-value="m.aftermovie_url || ''" />
                                        <InputError :message="errors.aftermovie_url" />
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <Button type="submit" :disabled="processing">Mettre à jour</Button>
                                    </div>
                                </Form>

                                <Form
                                    :action="`/admin/after-movies/${m.id}`"
                                    method="post"
                                    v-slot="{ processing: deleting }"
                                    :options="{ preserveScroll: true }"
                                >
                                    <input type="hidden" name="_method" value="DELETE" />
                                    <Button type="submit" variant="destructive" :disabled="deleting">Supprimer</Button>
                                </Form>
                            </div>
                        </div>

                        <div v-if="afterMovies.length === 0" class="text-sm text-muted-foreground">
                            Aucun after movie.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
