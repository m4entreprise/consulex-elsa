<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';

const page = usePage();

const success = computed(() => page.props.flash?.success);
const error = computed(() => page.props.flash?.error);
</script>

<template>
    <div class="space-y-3">
        <Alert v-if="success" class="border-emerald-200 bg-emerald-50 text-emerald-900">
            <AlertTitle>OK</AlertTitle>
            <AlertDescription>{{ success }}</AlertDescription>
        </Alert>

        <Alert v-if="error" variant="destructive">
            <AlertTitle>Error</AlertTitle>
            <AlertDescription>{{ error }}</AlertDescription>
        </Alert>
    </div>
</template>
