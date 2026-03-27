<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Manage Clients',
        href: '/dashboard/manage-clients',
    },
];

const page = usePage<{ auth: { user: { roles: string[] } } }>();
const canExportClients = computed(() => {
    const roles = page.props.auth.user?.roles ?? [];

    return roles.includes('manager') || roles.includes('admin');
});
</script>

<template>
    <Head title="Manage Clients" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Manage Clients</h1>
                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">List of all clients.</p>
                </div>

                <Button v-if="canExportClients" as-child>
                    <a href="/dashboard/clients/export">Export to Excel</a>
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
