<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { toast } from 'vue-sonner';
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

const handleExportClick = () => {
    toast.promise(
        new Promise((resolve) => {
            setTimeout(() => {
                window.location.href = '/dashboard/clients/export';
                resolve(null);
            }, 500);
        }),
        {
            loading: 'Preparing your export...',
            success: 'Export started! Your file will download shortly.',
            error: 'Failed to export clients.',
        },
    );
};
</script>

<template>
    <Head title="Manage Clients" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 py-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-primary-900 dark:text-primary-100">Manage Clients</h1>
                    <p class="mt-2 text-sm text-primary-700 dark:text-primary-300">List of all clients.</p>
                </div>

                <Button v-if="canExportClients" @click="handleExportClick">
                    Export to Excel
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
