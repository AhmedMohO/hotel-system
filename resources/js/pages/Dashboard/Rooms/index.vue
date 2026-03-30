<script setup lang="ts">
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import DataTable from '@/components/DataTable.vue';
import ActionIcon from '@/components/ActionIcon.vue';
import { router, usePage } from '@inertiajs/vue3';
import { Pencil, Trash2 } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import * as Rooms from '@/routes/rooms';

import { h } from 'vue';

defineProps<{
    rooms: any;
    filters: Record<string, any>;
}>();





const page = usePage();
const isAdmin = (page.props.auth.user as any).roles?.includes('admin');
// const flash = (page.props as any).flash;

const columns = [
    { accessorKey: 'number',        header: 'Room Number', enableSorting: true },
    { accessorKey: 'floor.name',    header: 'Floor',       enableSorting: false },
    { accessorKey: 'capacity',      header: 'Capacity',    enableSorting: true },
    { accessorKey: 'price_dollars', header: 'Price ($)',   enableSorting: true },
    ...(isAdmin ? [{ accessorKey: 'creator.name', header: 'Manager Name', enableSorting: false }] : []),
    {
        id: 'actions',
        header: 'Actions',
        enableSorting: false,
        cell: ({ row }: any) => h('div', { class: 'flex gap-2' }, [
            h(ActionIcon, {
                icon: Pencil,
                tooltip: 'Edit',
                variant: 'ghost',
                href: Rooms.edit.url(row.original.id),
            }),
            h(ConfirmDialog, {
                url: Rooms.destroy.url(row.original.id),
                method: 'delete',
                title: 'Delete Room?',
                description: `Delete room ${row.original.number}? This cannot be undone.`,
                triggerIcon: Trash2,
                triggerTooltip: 'Delete',
                triggerVariant: 'ghost',
                triggerClass:
                    'text-red-600 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-950/60 dark:hover:text-red-300',
                confirmLabel: 'Delete',
                confirmVariant: 'destructive',
            }),
        ]),
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Rooms', href: Rooms.index.url() }]">
        <div class="space-y-6 p-6">

            <!-- <div v-if="flash?.error"
                 class="rounded-md border border-destructive bg-destructive/10 px-4 py-3 text-sm text-destructive">
                {{ flash.error }}
            </div>

            <div v-if="flash?.success"
                 class="rounded-md border border-green-500 bg-green-500/10 px-4 py-3 text-sm text-green-600">
                {{ flash.success }}
            </div> -->

            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Manage Rooms</h1>
               <Button variant="default" @click="router.visit(Rooms.create.url())">+ Add Room</Button>
            </div>

            <DataTable :data="rooms" :columns="columns" search-placeholder="Search rooms..." />
        </div>
    </AppLayout>
</template>