<script setup lang="ts">
import { TabsRoot } from 'radix-vue';
import type { TabsRootProps } from 'radix-vue';

interface Props extends TabsRootProps {
    class?: string;
}

withDefaults(defineProps<Props>(), {
    orientation: 'horizontal',
});
</script>

<template>
    <TabsRoot v-bind="$attrs">
        <slot />
    </TabsRoot>
</template>
