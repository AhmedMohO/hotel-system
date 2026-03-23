<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Manage Clients',
        href: '/dashboard/manage-clients',
    },
];
</script>

<template>
    <Head title="Manage Clients" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Manage Clients</h1>
            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">List of all clients.</p>
        </div>
    </AppLayout>
</template>
