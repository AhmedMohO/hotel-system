<script setup lang="ts">
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import DataTable from '@/components/DataTable.vue';
import ActionIcon from '@/components/ActionIcon.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { Pencil, Trash2, Plus } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import * as Floors from '@/routes/floors';
import { h } from 'vue';




defineProps<{
    floors: any;
    filters: Record<string, any>;
}>();

const page = usePage();
const isAdmin = (page.props.auth.user as any).roles?.includes('admin');
// const flash = (page.props as any).flash;

const columns = [
    { accessorKey: 'number', header: 'Floor Number', enableSorting: true },
    { accessorKey: 'name',   header: 'Name',         enableSorting: true },
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
                href: Floors.edit.url(row.original.id),
            }),
            h(ConfirmDialog, {
                url: Floors.destroy.url(row.original.id),
                method: 'delete',
                title: 'Delete Floor?',
                description: `Delete floor ${row.original.name}? This cannot be undone.`,
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
    <AppLayout :breadcrumbs="[{ title: 'Floors', href: Floors.index.url() }]">
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
                <h1 class="text-2xl font-semibold">Manage Floors</h1>
                <Button class="w-fit shrink-0 gap-2" @click="router.visit(Floors.create.url())">
                    + Add Floor
                </Button>
            </div>

            <DataTable :data="floors" :columns="columns" search-placeholder="Search floors..." />
        </div>
    </AppLayout>
</template>