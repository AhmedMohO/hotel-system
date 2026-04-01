<script setup lang="ts">
import { Link, usePage, Head } from '@inertiajs/vue3';
import type { ColumnDef } from '@tanstack/vue-table';
import { Trash2, Pencil, Eye, UserPlus, FileDown, CheckCircle } from 'lucide-vue-next';
import { computed, h } from 'vue';
import { toast } from 'vue-sonner';
import ActionIcon from '@/components/ActionIcon.vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import DataTable from '@/components/DataTable.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

type Client = {
    id: number;
    name: string;
    email: string;
    avatar_image: string | null;
    created_at: string;
    approved_at: string | null;
};

type PaginationLink = { label: string; url: string | null; active: boolean };
type Pagination = {
    data: Client[];
    current_page: number;
    from: number;
    to: number;
    total: number;
    last_page: number;
    per_page: number;
    links: PaginationLink[];
};

type SharedProps = {
    auth: { user: { roles: string[] } };
    flash: { success: string | null; error: string | null };
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Clients', href: '/dashboard/clients' },
];

const { clients, filters } = defineProps<{
    clients: Pagination;
    filters?: Record<string, any>;
}>();

const page = usePage<SharedProps>();
const roles = computed(() => page.props.auth?.user?.roles ?? []);
const isAdminOrManager = computed(
    () => roles.value.includes('admin') || roles.value.includes('manager'),
);

function exportExcel() {
    toast.promise(
        new Promise((resolve) => {
            setTimeout(() => {
                window.location.href = '/dashboard/clients/export/excel';
                resolve(null);
            }, 500);
        }),
        {
            loading: 'Preparing your export...',
            success: 'Export started! Your file will download shortly.',
            error: 'Failed to export clients.',
        },
    );
}

function formatDate(dateStr: string) {
    return new Date(dateStr).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}

const columns: ColumnDef<Client, any>[] = [
    {
        id: 'client',
        header: 'Client',
        enableSorting: false,
        cell: ({ row }) =>
            h('div', { class: 'flex items-center gap-3 py-1' }, [
                h('img', {
                    src: row.original.avatar_image
                        ? '/storage/' + row.original.avatar_image
                        : '/images/avatar.jpg',
                    class: 'h-9 w-9 flex-shrink-0 rounded-full object-cover ring-2 ring-border',
                }),
                h('div', { class: 'min-w-0' }, [
                    h('p', { class: 'text-sm font-medium truncate' }, row.original.name),
                    h('p', { class: 'text-xs text-muted-foreground truncate' }, row.original.email),
                ]),
            ]),
    },
    {
        id: 'status',
        header: 'Status',
        enableSorting: false,
        cell: ({ row }) =>
            row.original.approved_at
                ? h('span', {
                      class: 'inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200 ring-inset dark:bg-emerald-950/60 dark:text-emerald-400 dark:ring-emerald-800',
                  }, [
                      h('span', { class: 'h-1.5 w-1.5 rounded-full bg-emerald-500' }),
                      'Approved',
                  ])
                : h('span', {
                      class: 'inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700 ring-1 ring-amber-200 ring-inset dark:bg-amber-950/60 dark:text-amber-400 dark:ring-amber-800',
                  }, [
                      h('span', { class: 'h-1.5 w-1.5 animate-pulse rounded-full bg-amber-400' }),
                      'Pending',
                  ]),
    },
    {
        accessorKey: 'created_at',
        header: 'Joined',
        enableSorting: false,
        cell: ({ row }) =>
            h('span', { class: 'text-sm text-muted-foreground tabular-nums' }, formatDate(row.original.created_at)),
    },
    {
        id: 'actions',
        header: () => h('div', { class: 'flex justify-center' }, 'Actions'),
        enableSorting: false,
        cell: ({ row }) => {
            const client = row.original;

            const approveBtn = !client.approved_at
                ? h(ConfirmDialog, {
                      url: `/dashboard/clients/${client.id}/approve`,
                      method: 'post',
                      title: 'Approve Client?',
                      description: `Approve ${client.name}? They will be notified and gain access to the platform.`,
                      triggerLabel: 'Approve',
                      triggerVariant: 'default',
                      triggerClass: 'h-7 rounded-md px-2.5 text-xs font-semibold',
                      confirmLabel: 'Yes, Approve',
                      confirmVariant: 'default',
                      successMessage: `${client.name} has been approved successfully.`,
                  })
                : h(ConfirmDialog, {
                      url: `/dashboard/clients/${client.id}/unapprove`,
                      method: 'patch',
                      title: 'Revoke Approval?',
                      description: `Revoke approval for ${client.name}? They will lose access to the platform.`,
                      triggerLabel: 'Revoke',
                      triggerVariant: 'outline',
                      triggerClass: 'h-7 rounded-md px-2.5 text-xs font-medium text-destructive border-destructive/40 hover:bg-destructive/10',
                      confirmLabel: 'Yes, Revoke',
                      confirmVariant: 'destructive',
                      successMessage: `${client.name} approval has been revoked.`,
                  });

            return h('div', { class: 'flex items-center justify-center gap-1' }, [
                approveBtn,
                h(ActionIcon, {
                    icon: Eye,
                    tooltip: 'View profile',
                    class: 'text-sky-600 hover:bg-sky-50 hover:text-sky-700 dark:text-sky-400 dark:hover:bg-sky-950/60 dark:hover:text-sky-300',
                    href: `/dashboard/clients/${client.id}`,
                }),
                ...(isAdminOrManager.value ? [
                    h(ActionIcon, {
                        icon: Pencil,
                        tooltip: 'Edit',
                        class: 'text-amber-600 hover:bg-amber-50 hover:text-amber-700 dark:text-amber-400 dark:hover:bg-amber-950/60 dark:hover:text-amber-300',
                        href: `/dashboard/clients/${client.id}/edit`,
                    }),
                    h(ConfirmDialog, {
                        url: `/dashboard/clients/${client.id}`,
                        method: 'delete',
                        title: 'Delete Client?',
                        description: `This will soft delete ${client.name} from the system.`,
                        triggerIcon: Trash2,
                        triggerTooltip: 'Delete',
                        triggerVariant: 'ghost',
                        triggerClass: 'text-red-600 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-950/60 dark:hover:text-red-300',
                        confirmVariant: 'destructive',
                        confirmLabel: 'Delete',
                        successMessage: `${client.name} has been deleted.`,
                    }),
                ] : []),
            ]);
        },
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Clients" />
        <div class="flex flex-col gap-5 p-6">

            <!-- Page Header -->
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-0.5">
                    <h1 class="text-xl font-semibold tracking-tight">Clients</h1>
                    <p class="text-sm text-muted-foreground">
                        Manage client accounts, approvals and access.
                    </p>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <Button variant="outline" size="sm" class="gap-2" as-child>
                        <Link href="/dashboard/clients/my-approved">
                            <CheckCircle class="h-4 w-4 text-primary" />
                            My Approved
                        </Link>
                    </Button>

                    <Button
                        v-if="isAdminOrManager"
                        variant="outline"
                        size="sm"
                        class="gap-2 text-green-600 hover:text-green-700 hover:bg-green-50 dark:hover:bg-green-950/40"
                        @click="exportExcel"
                    >
                        <FileDown class="h-4 w-4" />
                        Export Excel
                    </Button>

                    <Button size="sm" class="gap-2" as-child>
                        <Link href="/dashboard/clients/create">
                            <UserPlus class="h-4 w-4" />
                            Add Client
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Data Table -->
            <DataTable
                :data="clients"
                :columns="columns"
                :filters="filters"
                search-placeholder="Search clients by name..."
            />
        </div>
    </AppLayout>
</template>