<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Manage Managers',
        href: '/dashboard/manage-managers',
    },
];
</script>

<template>
    <Head title="Manage Managers" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Manage Managers</h1>
            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">List of all managers.</p>
        </div>
    </AppLayout>
</template>
