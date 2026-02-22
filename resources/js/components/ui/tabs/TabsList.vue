<script setup lang="ts">
import { TabsList as TabsListPrimitive, type TabsListProps } from 'radix-vue';
import { cn } from '@/lib/utils';
import { computed } from 'vue';

const props = defineProps<TabsListProps & { class?: string }>();

const classes = computed(() => cn('inline-flex h-10 items-center justify-center rounded-md bg-muted p-1 text-muted-foreground', props.class));
</script>

<template>
    <TabsListPrimitive :class="classes">
        <slot />
    </TabsListPrimitive>
</template>
