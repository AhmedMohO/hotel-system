<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Manage Managers',
        href: '/dashboard/manage-managers',
    },
    {
        title: 'Create',
        href: '/dashboard/manage-managers/create',
    },
];
</script>

<template>
    <Head title="Create Manager" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Create Manager</h1>
            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Add a new manager to the system.</p>
        </div>
    </AppLayout>
</template>
