<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Manage Receptionists',
        href: '/dashboard/manage-receptionists',
    },
    {
        title: 'Create',
        href: '/dashboard/manage-receptionists/create',
    },
];
</script>

<template>
    <Head title="Create Receptionist" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Create Receptionist</h1>
            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Add a new receptionist to the system.</p>
        </div>
    </AppLayout>
</template>
