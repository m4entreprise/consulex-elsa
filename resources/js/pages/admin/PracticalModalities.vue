<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import FlashMessages from '@/components/FlashMessages.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogScrollContent } from '@/components/ui/dialog';
import AppLayout from '@/layouts/AppLayout.vue';
import Draggable from 'vuedraggable';
import { GripVertical } from 'lucide-vue-next';
import * as LucideIcons from 'lucide-vue-next';

type Modality = {
    id: number;
    icon_name: string;
    title: string;
    description: string | null;
    order: number;
};

const props = defineProps<{
    modalities: Modality[];
}>();

const items = ref<Modality[]>([...props.modalities]);
const editingItem = ref<Modality | null>(null);
const newItemIcon = ref('HelpCircle');
const showIconPicker = ref(false);
const iconSearchQuery = ref('');
const isPickingForNew = ref(false);
const selectedCategory = ref('all');

const iconCategories = {
    all: { label: 'Toutes', keywords: [] },
    general: { label: 'Général', keywords: ['home', 'user', 'settings', 'search', 'menu', 'more', 'info', 'help', 'star', 'heart', 'bookmark', 'flag', 'tag', 'bell', 'notification'] },
    communication: { label: 'Communication', keywords: ['mail', 'message', 'chat', 'phone', 'send', 'inbox', 'at', 'share', 'rss', 'wifi', 'signal'] },
    media: { label: 'Média', keywords: ['play', 'pause', 'stop', 'skip', 'music', 'video', 'image', 'camera', 'film', 'mic', 'volume', 'speaker'] },
    files: { label: 'Fichiers', keywords: ['file', 'folder', 'document', 'download', 'upload', 'save', 'copy', 'clipboard', 'archive'] },
    editing: { label: 'Édition', keywords: ['edit', 'pen', 'pencil', 'brush', 'eraser', 'scissors', 'crop', 'rotate', 'palette', 'type'] },
    arrows: { label: 'Flèches', keywords: ['arrow', 'chevron', 'move', 'corner', 'diagonal', 'expand', 'minimize', 'maximize'] },
    navigation: { label: 'Navigation', keywords: ['map', 'compass', 'navigation', 'location', 'pin', 'route', 'directions', 'globe', 'world'] },
    time: { label: 'Temps', keywords: ['clock', 'calendar', 'time', 'watch', 'timer', 'alarm', 'schedule', 'date'] },
    business: { label: 'Business', keywords: ['briefcase', 'building', 'bank', 'store', 'shop', 'cart', 'shopping', 'credit', 'wallet', 'dollar', 'euro', 'pound'] },
    development: { label: 'Dev', keywords: ['code', 'terminal', 'command', 'git', 'github', 'database', 'server', 'bug', 'package', 'npm'] },
    design: { label: 'Design', keywords: ['layout', 'grid', 'columns', 'sidebar', 'panel', 'square', 'circle', 'triangle', 'shape', 'box'] },
    social: { label: 'Social', keywords: ['facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'github', 'slack', 'discord', 'twitch'] },
    weather: { label: 'Météo', keywords: ['sun', 'moon', 'cloud', 'rain', 'snow', 'wind', 'storm', 'weather', 'temperature'] },
    transport: { label: 'Transport', keywords: ['car', 'bus', 'train', 'plane', 'bike', 'ship', 'truck', 'taxi', 'rocket'] },
    objects: { label: 'Objets', keywords: ['key', 'lock', 'unlock', 'shield', 'award', 'gift', 'trophy', 'medal', 'battery', 'plug', 'lightbulb', 'lamp'] },
};

const allIcons = computed(() => {
    const iconNames = Object.keys(LucideIcons).filter((name) => {
        if (name === 'default') return false;
        if (name === 'icons') return false;
        if (name === 'Icon') return false;
        if (name === 'createLucideIcon') return false;
        if (name.startsWith('Lucide')) return false;
        return typeof (LucideIcons as any)[name] === 'function';
    });
    
    let filtered = iconNames;
    
    // Filtrer par catégorie
    if (selectedCategory.value !== 'all') {
        const category = iconCategories[selectedCategory.value as keyof typeof iconCategories];
        filtered = iconNames.filter((name) => {
            const lowerName = name.toLowerCase();
            return category.keywords.some((keyword) => lowerName.includes(keyword));
        });
    }
    
    // Filtrer par recherche
    if (iconSearchQuery.value) {
        filtered = filtered.filter((name) =>
            name.toLowerCase().includes(iconSearchQuery.value.toLowerCase())
        );
    }
    
    return filtered;
});

function saveOrder() {
    router.post('/admin/practical-modalities/reorder', {
        items: items.value.map((item) => ({ id: item.id })),
    }, {
        preserveScroll: true,
    });
}

function startEdit(item: Modality) {
    editingItem.value = { ...item };
}

function cancelEdit() {
    editingItem.value = null;
}

function deleteItem(id: number) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette modalité ?')) {
        return;
    }

    router.delete(`/admin/practical-modalities/${id}`, {
        preserveScroll: true,
    });
}

function openIconPicker(forNew = false) {
    iconSearchQuery.value = '';
    selectedCategory.value = 'all';
    isPickingForNew.value = forNew;
    showIconPicker.value = true;
}

function selectIcon(iconName: string) {
    if (isPickingForNew.value) {
        newItemIcon.value = iconName;
    } else if (editingItem.value) {
        editingItem.value.icon_name = iconName;
    }
    showIconPicker.value = false;
}

function getIconComponent(iconName: string) {
    if (!iconName) return LucideIcons.HelpCircle;
    const IconComponent = (LucideIcons as any)[iconName];
    if (IconComponent === (LucideIcons as any).Icon) return LucideIcons.HelpCircle;
    return IconComponent && typeof IconComponent === 'function' ? IconComponent : LucideIcons.HelpCircle;
}
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Admin', href: '/dashboard' }, { title: 'Site web', href: '#' }, { title: 'Modalités pratiques' }]">
        <div class="p-4">
            <FlashMessages class="mb-4" />

            <Heading
                title="Modalités pratiques"
                description="Gérer les informations pratiques affichées sur le site (bar, restauration, accès, etc.)."
            />

            <div class="mt-6 w-full">
                <div class="flex flex-col items-stretch gap-6 lg:flex-row lg:items-start">
                    <div class="w-full lg:w-96 lg:shrink-0">
                        <Form
                            action="/admin/practical-modalities"
                            method="post"
                            class="rounded-xl border border-border/60 bg-card p-4 lg:sticky lg:top-6"
                            v-slot="{ errors, processing }"
                            @success="() => { newItemIcon = 'HelpCircle'; }"
                            :options="{ preserveScroll: true }"
                        >
                            <h3 class="mb-4 font-semibold">Ajouter une modalité</h3>

                            <div class="grid gap-4">
                                <div class="grid gap-2">
                                    <Label>Icône</Label>
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-lg border border-border/60 bg-muted">
                                            <component :is="getIconComponent(newItemIcon)" class="h-6 w-6" />
                                        </div>
                                        <Button type="button" variant="outline" @click="openIconPicker(true)">
                                            Choisir une icône
                                        </Button>
                                    </div>
                                    <input type="hidden" name="icon_name" :value="newItemIcon" />
                                    <InputError :message="errors.icon_name" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="new_title">Titre</Label>
                                    <Input id="new_title" name="title" required />
                                    <InputError :message="errors.title" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="new_description">Description</Label>
                                    <textarea
                                        id="new_description"
                                        name="description"
                                        rows="3"
                                        class="block w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs"
                                    ></textarea>
                                    <InputError :message="errors.description" />
                                </div>

                                <Button type="submit" :disabled="processing">Ajouter</Button>
                            </div>
                        </Form>
                    </div>

                    <div class="min-w-0 flex-1 overflow-x-hidden">
                        <Draggable
                            v-model="items"
                            item-key="id"
                            handle=".drag-handle"
                            @end="saveOrder"
                            class="flex flex-col gap-4"
                        >
                            <template #item="{ element }">
                                <div class="w-full overflow-hidden rounded-xl border border-border/60 bg-card p-4">
                                    <div v-if="editingItem && editingItem.id === element.id">
                                        <Form
                                            :action="`/admin/practical-modalities/${element.id}`"
                                            method="post"
                                            class="grid gap-4"
                                            v-slot="{ errors, processing }"
                                            @success="cancelEdit"
                                            :options="{ preserveScroll: true }"
                                        >
                                            <input type="hidden" name="_method" value="PATCH" />

                                    <div class="grid gap-2">
                                        <Label>Icône</Label>
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-12 w-12 items-center justify-center rounded-lg border border-border/60 bg-muted">
                                                <component :is="getIconComponent(editingItem.icon_name)" class="h-6 w-6" />
                                            </div>
                                            <Button type="button" variant="outline" @click="openIconPicker(false)">
                                                Choisir une icône
                                            </Button>
                                        </div>
                                        <input type="hidden" name="icon_name" :value="editingItem.icon_name" />
                                        <InputError :message="errors.icon_name" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="title">Titre</Label>
                                        <Input
                                            id="title"
                                            name="title"
                                            v-model="editingItem.title"
                                            required
                                        />
                                        <InputError :message="errors.title" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="description">Description</Label>
                                        <textarea
                                            id="description"
                                            name="description"
                                            v-model="editingItem.description"
                                            rows="3"
                                            class="block w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs"
                                        ></textarea>
                                        <InputError :message="errors.description" />
                                    </div>

                                            <div class="flex items-center gap-2">
                                                <Button type="submit" :disabled="processing">Enregistrer</Button>
                                                <Button type="button" variant="outline" @click="cancelEdit">Annuler</Button>
                                            </div>
                                        </Form>
                                    </div>

                                    <div v-else class="flex min-w-0 flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="flex min-w-0 flex-1 items-start gap-4">
                                            <button type="button" class="drag-handle mt-1 cursor-move text-muted-foreground hover:text-foreground">
                                                <GripVertical class="h-5 w-5" />
                                            </button>

                                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg border border-border/60 bg-muted">
                                                <component :is="getIconComponent(element.icon_name)" class="h-6 w-6" />
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                <h3 class="break-all font-semibold leading-snug">{{ element.title }}</h3>
                                                <p class="mt-1 break-all whitespace-pre-line text-sm leading-relaxed text-muted-foreground">{{ element.description }}</p>
                                            </div>
                                        </div>

                                        <div class="flex shrink-0 flex-row gap-2 sm:flex-col sm:items-stretch">
                                            <Button variant="outline" size="sm" @click="startEdit(element)">
                                                Modifier
                                            </Button>
                                            <Button variant="destructive" size="sm" @click="deleteItem(element.id)">
                                                Supprimer
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Draggable>
                    </div>
                </div>
            </div>
        </div>

        <Dialog v-model:open="showIconPicker">
            <DialogScrollContent class="flex max-h-[85vh] flex-col overflow-hidden sm:max-w-5xl">
                <DialogHeader>
                    <DialogTitle>Choisir une icône ({{ allIcons.length }} disponibles)</DialogTitle>
                </DialogHeader>

                <div class="mt-4 flex min-h-0 flex-col gap-4">
                    <div class="shrink-0 space-y-4">
                        <Input
                            v-model="iconSearchQuery"
                            placeholder="Rechercher une icône..."
                        />

                        <!-- Catégories -->
                        <div class="-mx-1 flex flex-nowrap gap-2 overflow-x-auto px-1 pb-1">
                            <button
                                v-for="(category, key) in iconCategories"
                                :key="key"
                                type="button"
                                @click="() => selectedCategory = String(key)"
                                :class="[
                                    'px-3 py-1.5 text-sm rounded-md transition-colors',
                                    selectedCategory === key
                                        ? 'bg-primary text-primary-foreground'
                                        : 'bg-muted hover:bg-muted/80'
                                ]"
                            >
                                {{ category.label }}
                            </button>
                        </div>
                    </div>

                    <div class="min-h-0 flex-1 overflow-y-auto pr-1">
                        <div class="grid grid-cols-4 gap-2 sm:grid-cols-6 md:grid-cols-8">
                            <button
                                v-for="iconName in allIcons"
                                :key="iconName"
                                type="button"
                                @click="selectIcon(iconName)"
                                class="flex h-14 w-14 items-center justify-center rounded-lg border border-border/60 transition-colors hover:bg-muted sm:h-16 sm:w-16"
                                :title="iconName"
                            >
                                <component :is="getIconComponent(iconName)" class="h-6 w-6" />
                            </button>
                        </div>
                    </div>
                </div>
            </DialogScrollContent>
        </Dialog>
    </AppLayout>
</template>
