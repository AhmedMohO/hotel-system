<script setup lang="ts">
import { Toaster } from 'vue-sonner';
import { useAppearance } from '@/composables/useAppearance';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItem } from '@/types';

const { appearance } = useAppearance();

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
        <Toaster :theme="appearance" rich-colors close-button position="top-center" />
    </AppLayout>
</template>
