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

const activeTab = ref('event');

type Settings = {
    event_edition_year: number | null;
    event_title: string | null;
    event_description: string | null;
    event_theme: string | null;
    event_date: string | null;
    event_location: string | null;
    venue_room_name: string | null;
    venue_room_title: string | null;
    map_place_label: string | null;
    map_address: string | null;
    map_open_url: string | null;
    map_embed_url: string | null;
    access_text: string | null;
    network_text: string | null;
    timeline: unknown[] | null;
    instagram_url: string | null;
    privacy_policy_url: string | null;
    rules_url: string | null;
    footer_brand: string | null;
    footer_description: string | null;
    footer_copyright: string | null;
    spectator_capacity: number;
    candidate_capacity: number;
    spectator_registrations_enabled: boolean;
    spectator_registrations_end_at: string | null;
    spectator_custom_form_enabled: boolean;
    spectator_custom_form_url: string | null;
    candidate_registrations_enabled: boolean;
    candidate_registrations_end_at: string | null;
    candidate_custom_form_enabled: boolean;
    candidate_custom_form_url: string | null;
};

type TimelineItem = {
    uid: string;
    time: string;
    label: string;
    description: string;
};

const props = defineProps<{ settings: Settings }>();

function toDatetimeLocalValue(value: string | null) {
    if (!value) return '';
    let d = new Date(value);
    if (Number.isNaN(d.getTime()) && value.includes(' ')) {
        d = new Date(value.replace(' ', 'T'));
    }
    if (Number.isNaN(d.getTime())) return '';

    const pad = (n: number) => String(n).padStart(2, '0');
    const yyyy = d.getFullYear();
    const mm = pad(d.getMonth() + 1);
    const dd = pad(d.getDate());
    const hh = pad(d.getHours());
    const min = pad(d.getMinutes());
    return `${yyyy}-${mm}-${dd}T${hh}:${min}`;
}

const spectatorRegistrationsEnabled = ref<boolean>(Boolean(props.settings.spectator_registrations_enabled));
const candidateRegistrationsEnabled = ref<boolean>(Boolean(props.settings.candidate_registrations_enabled));

const spectatorCustomFormEnabled = ref<boolean>(Boolean(props.settings.spectator_custom_form_enabled));
const candidateCustomFormEnabled = ref<boolean>(Boolean(props.settings.candidate_custom_form_enabled));

const spectatorRegistrationsEndAt = ref<string>(toDatetimeLocalValue(props.settings.spectator_registrations_end_at));
const candidateRegistrationsEndAt = ref<string>(toDatetimeLocalValue(props.settings.candidate_registrations_end_at));

const spectatorCustomFormUrl = ref<string>(props.settings.spectator_custom_form_url || '');
const candidateCustomFormUrl = ref<string>(props.settings.candidate_custom_form_url || '');

watch(spectatorCustomFormEnabled, (enabled) => {
    if (!enabled) {
        spectatorCustomFormUrl.value = '';
    }
});

watch(candidateCustomFormEnabled, (enabled) => {
    if (!enabled) {
        candidateCustomFormUrl.value = '';
    }
});

function newTimelineUid() {
    return `${Math.random().toString(36).slice(2)}-${Date.now().toString(36)}`;
}

const timelineItems = ref<TimelineItem[]>(
    Array.isArray(props.settings.timeline)
        ? (props.settings.timeline as any[]).map((item) => ({
              uid: newTimelineUid(),
              time: typeof item?.time === 'string' ? item.time : '',
              label: typeof item?.label === 'string' ? item.label : '',
              description: typeof item?.description === 'string' ? item.description : '',
          }))
        : [],
);

function addTimelineItem() {
    timelineItems.value.push({ uid: newTimelineUid(), time: '', label: '', description: '' });
}

function removeTimelineItem(index: number) {
    timelineItems.value.splice(index, 1);
}

function clearTimeline() {
    timelineItems.value = [];
}

const timelineJson = computed(() => {
    const cleaned = timelineItems.value
        .map((i) => ({
            time: (i.time ?? '').trim(),
            label: (i.label ?? '').trim(),
            description: (i.description ?? '').trim(),
        }))
        .filter((i) => i.time !== '' || i.label !== '' || i.description !== '');

    if (cleaned.length === 0) return '';
    return JSON.stringify(cleaned);
});
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Admin', href: '/dashboard' }, { title: 'Site web', href: '#' }, { title: 'Général' }]">
        <div class="p-4">
            <FlashMessages class="mb-4" />

            <Heading
                title="Général"
                description="Paramètres de l'événement : contenu public + quotas modifiables (spectateurs/candidats)."
            />

            <div class="max-w-4xl">
                <Form
                    action="/admin/settings"
                    method="post"
                    enctype="multipart/form-data"
                    class="grid gap-6"
                    v-slot="{ errors, processing }"
                    :options="{ preserveScroll: true }"
                >
                    <input type="hidden" name="_method" value="PATCH" />

                    <div class="w-full">
                        <div class="inline-flex h-10 items-center justify-center rounded-md bg-muted p-1 text-muted-foreground w-full">
                            <button
                                type="button"
                                @click="activeTab = 'event'"
                                :class="[
                                    'inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 flex-1',
                                    activeTab === 'event' ? 'bg-background text-foreground shadow-sm' : ''
                                ]"
                            >
                                Événement
                            </button>
                            <button
                                type="button"
                                @click="activeTab = 'venue'"
                                :class="[
                                    'inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 flex-1',
                                    activeTab === 'venue' ? 'bg-background text-foreground shadow-sm' : ''
                                ]"
                            >
                                Lieu & Accès
                            </button>
                            <button
                                type="button"
                                @click="activeTab = 'timeline'"
                                :class="[
                                    'inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 flex-1',
                                    activeTab === 'timeline' ? 'bg-background text-foreground shadow-sm' : ''
                                ]"
                            >
                                Programme
                            </button>
                            <button
                                type="button"
                                @click="activeTab = 'registrations'"
                                :class="[
                                    'inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 flex-1',
                                    activeTab === 'registrations' ? 'bg-background text-foreground shadow-sm' : ''
                                ]"
                            >
                                Inscriptions
                            </button>
                            <button
                                type="button"
                                @click="activeTab = 'misc'"
                                :class="[
                                    'inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 flex-1',
                                    activeTab === 'misc' ? 'bg-background text-foreground shadow-sm' : ''
                                ]"
                            >
                                Divers
                            </button>
                        </div>

                        <div v-show="activeTab === 'event'" class="mt-6">

                    <div class="grid gap-3 rounded-xl border border-border/60 bg-card p-6">
                        <div class="grid gap-2">
                            <Label for="event_edition_year">Année de l'édition</Label>
                            <Input
                                id="event_edition_year"
                                name="event_edition_year"
                                type="number"
                                min="1900"
                                max="2100"
                                :default-value="settings.event_edition_year ?? ''"
                            />
                            <InputError :message="errors.event_edition_year" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="event_theme">Thème</Label>
                            <Input id="event_theme" name="event_theme" :default-value="settings.event_theme || ''" />
                            <InputError :message="errors.event_theme" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="event_title">Accroche / Description courte</Label>
                            <Input id="event_title" name="event_title" :default-value="settings.event_title || ''" />
                            <InputError :message="errors.event_title" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="event_description">Description</Label>
                            <textarea
                                id="event_description"
                                name="event_description"
                                rows="5"
                                class="block w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs"
                            >{{ settings.event_description || '' }}</textarea>
                            <InputError :message="errors.event_description" />
                        </div>

                        <div class="grid gap-2 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="event_date">Date</Label>
                                <Input id="event_date" name="event_date" :default-value="settings.event_date || ''" />
                                <InputError :message="errors.event_date" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="event_location">Lieu</Label>
                                <Input id="event_location" name="event_location" :default-value="settings.event_location || ''" />
                                <InputError :message="errors.event_location" />
                            </div>
                        </div>
                        </div>
                        </div>

                        <div v-show="activeTab === 'venue'" class="mt-6">
                        <div class="grid gap-3 rounded-xl border border-border/60 bg-card p-6">

                        <div class="grid gap-2 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="venue_room_name">Nom de la salle</Label>
                                <Input id="venue_room_name" name="venue_room_name" :default-value="settings.venue_room_name || ''" />
                                <InputError :message="errors.venue_room_name" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="venue_room_title">Titre de la salle</Label>
                                <Input id="venue_room_title" name="venue_room_title" :default-value="settings.venue_room_title || ''" />
                                <InputError :message="errors.venue_room_title" />
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label for="map_place_label">Lieu du plan</Label>
                            <Input id="map_place_label" name="map_place_label" :default-value="settings.map_place_label || ''" />
                            <InputError :message="errors.map_place_label" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="map_address">Adresse (affichée)</Label>
                            <textarea
                                id="map_address"
                                name="map_address"
                                rows="3"
                                class="block w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs"
                            >{{ settings.map_address || '' }}</textarea>
                            <InputError :message="errors.map_address" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="map_open_url">URL "Ouvrir" (Google Maps / OSM)</Label>
                            <Input id="map_open_url" name="map_open_url" :default-value="settings.map_open_url || ''" />
                            <InputError :message="errors.map_open_url" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="map_embed_url">URL d'embed de la map (iframe)</Label>
                            <Input id="map_embed_url" name="map_embed_url" :default-value="settings.map_embed_url || ''" />
                            <InputError :message="errors.map_embed_url" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="access_text">Texte d'accès</Label>
                            <textarea
                                id="access_text"
                                name="access_text"
                                rows="4"
                                class="block w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs"
                            >{{ settings.access_text || '' }}</textarea>
                            <InputError :message="errors.access_text" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="network_text">Texte Réseau</Label>
                            <Input id="network_text" name="network_text" :default-value="settings.network_text || ''" />
                            <InputError :message="errors.network_text" />
                        </div>
                        </div>
                        </div>

                        <div v-show="activeTab === 'timeline'" class="mt-6">
                        <div class="grid gap-3 rounded-xl border border-border/60 bg-card p-6">

                        <div class="grid gap-2">
                            <Label for="timeline_json">Timeline (JSON)</Label>
                            <input type="hidden" name="timeline_json" :value="timelineJson" />

                            <div class="grid gap-3">
                                <Draggable v-model="timelineItems" item-key="uid" handle=".drag-handle" class="grid gap-3">
                                    <template #item="{ element, index }">
                                        <div class="rounded-xl border border-border/60 bg-background/40 p-4">
                                            <div class="grid gap-3">
                                                <div class="flex items-center justify-between gap-3">
                                                    <button
                                                        type="button"
                                                        class="drag-handle inline-flex items-center gap-2 rounded-md border border-border/60 bg-background px-2 py-1 text-xs text-muted-foreground"
                                                    >
                                                        <GripVertical class="h-4 w-4" />
                                                        Glisser pour réordonner
                                                    </button>

                                                    <Button type="button" variant="destructive" @click="removeTimelineItem(index)">
                                                        Supprimer
                                                    </Button>
                                                </div>

                                                <div class="grid gap-3 sm:grid-cols-2">
                                                    <div class="grid gap-2">
                                                        <Label :for="`timeline_time_${element.uid}`">Heure</Label>
                                                        <Input :id="`timeline_time_${element.uid}`" type="time" v-model="element.time" />
                                                    </div>
                                                    <div class="grid gap-2">
                                                        <Label :for="`timeline_label_${element.uid}`">Titre</Label>
                                                        <Input :id="`timeline_label_${element.uid}`" v-model="element.label" />
                                                    </div>
                                                </div>

                                                <div class="grid gap-2">
                                                    <Label :for="`timeline_desc_${element.uid}`">Description</Label>
                                                    <textarea
                                                        :id="`timeline_desc_${element.uid}`"
                                                        v-model="element.description"
                                                        rows="3"
                                                        class="block w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs"
                                                    ></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </Draggable>

                                <div class="flex flex-wrap items-center gap-2">
                                    <Button type="button" variant="outline" @click="addTimelineItem">
                                        Ajouter une étape
                                    </Button>
                                    <Button type="button" variant="destructive" @click="clearTimeline">
                                        Vider la timeline
                                    </Button>
                                </div>
                            </div>

                            <InputError :message="errors.timeline_json" />
                        </div>
                        </div>
                        </div>

                        <div v-show="activeTab === 'registrations'" class="mt-6">
                        <div class="grid gap-3 rounded-xl border border-border/60 bg-card p-6">
                        <div class="grid gap-2 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="spectator_capacity">Jauge spectateurs</Label>
                                <Input id="spectator_capacity" name="spectator_capacity" type="number" min="0" :default-value="settings.spectator_capacity" />
                                <InputError :message="errors.spectator_capacity" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="candidate_capacity">Quota candidats</Label>
                                <Input id="candidate_capacity" name="candidate_capacity" type="number" min="0" :default-value="settings.candidate_capacity" />
                                <InputError :message="errors.candidate_capacity" />
                            </div>
                        </div>

                        <div class="grid gap-2 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="spectator_registrations_end_at">Fin inscriptions spectateurs</Label>
                                <Input
                                    id="spectator_registrations_end_at"
                                    name="spectator_registrations_end_at"
                                    type="datetime-local"
                                    v-model="spectatorRegistrationsEndAt"
                                />
                                <InputError :message="errors.spectator_registrations_end_at" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="candidate_registrations_end_at">Fin inscriptions candidats</Label>
                                <Input
                                    id="candidate_registrations_end_at"
                                    name="candidate_registrations_end_at"
                                    type="datetime-local"
                                    v-model="candidateRegistrationsEndAt"
                                />
                                <InputError :message="errors.candidate_registrations_end_at" />
                            </div>
                        </div>

                        <div class="grid gap-3">
                            <label class="flex items-center gap-3 text-sm">
                                <input
                                    type="checkbox"
                                    name="spectator_registrations_enabled"
                                    class="size-4"
                                    v-model="spectatorRegistrationsEnabled"
                                />
                                <span>Inscriptions spectateurs ouvertes</span>
                            </label>

                            <div class="grid gap-2">
                                <label class="flex items-center gap-3 text-sm">
                                    <input
                                        type="checkbox"
                                        name="spectator_custom_form_enabled"
                                        class="size-4"
                                        v-model="spectatorCustomFormEnabled"
                                    />
                                    <span>Utiliser un formulaire personnalisé (spectateurs)</span>
                                </label>

                                <div v-if="spectatorCustomFormEnabled" class="grid gap-2">
                                    <Label for="spectator_custom_form_url">Lien formulaire spectateurs</Label>
                                    <Input
                                        id="spectator_custom_form_url"
                                        name="spectator_custom_form_url"
                                        v-model="spectatorCustomFormUrl"
                                        placeholder="https://..."
                                    />
                                    <InputError :message="errors.spectator_custom_form_url" />
                                </div>
                            </div>

                            <label class="flex items-center gap-3 text-sm">
                                <input
                                    type="checkbox"
                                    name="candidate_registrations_enabled"
                                    class="size-4"
                                    v-model="candidateRegistrationsEnabled"
                                />
                                <span>Inscriptions candidats ouvertes</span>
                            </label>

                            <div class="grid gap-2">
                                <label class="flex items-center gap-3 text-sm">
                                    <input
                                        type="checkbox"
                                        name="candidate_custom_form_enabled"
                                        class="size-4"
                                        v-model="candidateCustomFormEnabled"
                                    />
                                    <span>Utiliser un formulaire personnalisé (candidats)</span>
                                </label>

                                <div v-if="candidateCustomFormEnabled" class="grid gap-2">
                                    <Label for="candidate_custom_form_url">Lien formulaire candidats</Label>
                                    <Input
                                        id="candidate_custom_form_url"
                                        name="candidate_custom_form_url"
                                        v-model="candidateCustomFormUrl"
                                        placeholder="https://..."
                                    />
                                    <InputError :message="errors.candidate_custom_form_url" />
                                </div>
                            </div>
                        </div>
                        </div>
                        </div>

                        <div v-show="activeTab === 'misc'" class="mt-6">
                        <div class="grid gap-3 rounded-xl border border-border/60 bg-card p-6">

                        <div class="grid gap-2">
                            <Label for="instagram_url">Instagram URL</Label>
                            <Input id="instagram_url" name="instagram_url" :default-value="settings.instagram_url || ''" />
                            <InputError :message="errors.instagram_url" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="footer_brand">Texte footer (ligne 1)</Label>
                            <Input id="footer_brand" name="footer_brand" :default-value="settings.footer_brand || ''" />
                            <InputError :message="errors.footer_brand" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="footer_description">Description footer</Label>
                            <textarea
                                id="footer_description"
                                name="footer_description"
                                rows="3"
                                class="block w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs"
                            >{{ settings.footer_description || '' }}</textarea>
                            <InputError :message="errors.footer_description" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="footer_copyright">Texte footer (ligne 2)</Label>
                            <Input id="footer_copyright" name="footer_copyright" :default-value="settings.footer_copyright || ''" />
                            <InputError :message="errors.footer_copyright" />
                        </div>

                        <div class="grid gap-2 sm:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="privacy_policy_pdf">Politique de confidentialité (PDF)</Label>
                                <input
                                    id="privacy_policy_pdf"
                                    name="privacy_policy_pdf"
                                    type="file"
                                    accept="application/pdf"
                                    class="block w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                                />
                                <InputError :message="errors.privacy_policy_pdf" />
                                <div v-if="settings.privacy_policy_url" class="text-xs text-muted-foreground">
                                    <a :href="settings.privacy_policy_url" target="_blank" rel="noopener noreferrer" class="underline underline-offset-4">
                                        Voir le PDF actuel
                                    </a>
                                </div>
                            </div>
                            <div class="grid gap-2">
                                <Label for="rules_pdf">Règlement (PDF)</Label>
                                <input
                                    id="rules_pdf"
                                    name="rules_pdf"
                                    type="file"
                                    accept="application/pdf"
                                    class="block w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm"
                                />
                                <InputError :message="errors.rules_pdf" />
                                <div v-if="settings.rules_url" class="text-xs text-muted-foreground">
                                    <a :href="settings.rules_url" target="_blank" rel="noopener noreferrer" class="underline underline-offset-4">
                                        Voir le PDF actuel
                                    </a>
                                </div>
                            </div>
                        </div>
                        </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-4">
                        <Button type="submit" :disabled="processing">Enregistrer</Button>
                        <span class="text-sm text-muted-foreground">/admin/settings</span>
                    </div>
                </Form>
            </div>
        </div>
    </AppLayout>
</template>
