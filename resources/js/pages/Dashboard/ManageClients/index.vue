<script setup lang="ts">
import { Link, usePage, Head } from '@inertiajs/vue3';
import type { ColumnDef } from '@tanstack/vue-table';
import { Trash2, Pencil, Eye } from 'lucide-vue-next';
import { computed, h } from 'vue';
import { toast } from 'vue-sonner';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import DataTable from '@/components/DataTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

// ── Types ─────────────────────────────────────────────────────────────────────
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

// ── Breadcrumbs ───────────────────────────────────────────────────────────────
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Manage Clients', href: '/dashboard/clients' },
];

// ── Props ─────────────────────────────────────────────────────────────────────
const { clients, filters } = defineProps<{
    clients: Pagination;
    filters?: Record<string, any>;
}>();

// ── Auth ──────────────────────────────────────────────────────────────────────
const page = usePage<SharedProps>();
const roles = computed(() => page.props.auth?.user?.roles ?? []);
const isAdminOrManager = computed(
    () => roles.value.includes('admin') || roles.value.includes('manager'),
);

// ── Export ───────────────────────────────────────────────────────────────────
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

// ── Helpers ───────────────────────────────────────────────────────────────────
function formatDate(dateStr: string) {
    return new Date(dateStr).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}

// ── Columns ───────────────────────────────────────────────────────────────────
const columns: ColumnDef<Client, any>[] = [
    {
        id: 'client',
        header: 'Client',
        enableSorting: false,
        cell: ({ row }) =>
            h('div', { class: 'flex items-center gap-3' }, [
                h('img', {
                    src: row.original.avatar_image
                        ? '/storage/' + row.original.avatar_image
                        : '/images/avatar.jpg',
                    class: 'h-9 w-9 flex-shrink-0 rounded-full object-cover ring-2 ring-border',
                }),
                h(
                    'span',
                    { class: 'text-sm font-semibold text-foreground' },
                    row.original.name,
                ),
            ]),
    },
    {
        accessorKey: 'email',
        header: 'Email',
        enableSorting: false,
        cell: ({ row }) =>
            h(
                'span',
                { class: 'text-sm text-muted-foreground' },
                row.original.email,
            ),
    },
    {
        id: 'status',
        header: 'Status',
        enableSorting: false,
        cell: ({ row }) =>
            row.original.approved_at
                ? h(
                      'span',
                      {
                          class: 'inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-2.5 py-1 text-xs font-semibold text-primary ring-1 ring-primary/20 ring-inset',
                      },
                      [
                          h('span', {
                              class: 'h-1.5 w-1.5 rounded-full bg-primary',
                          }),
                          'Approved',
                      ],
                  )
                : h(
                      'span',
                      {
                          class: 'inline-flex items-center gap-1.5 rounded-full bg-muted px-2.5 py-1 text-xs font-semibold text-muted-foreground ring-1 ring-border ring-inset',
                      },
                      [
                          h('span', {
                              class: 'h-1.5 w-1.5 animate-pulse rounded-full bg-muted-foreground',
                          }),
                          'Pending',
                      ],
                  ),
    },
    {
        accessorKey: 'created_at',
        header: 'Created At',
        enableSorting: false,
        cell: ({ row }) =>
            h(
                'span',
                { class: 'text-sm text-muted-foreground' },
                formatDate(row.original.created_at),
            ),
    },
    {
        id: 'actions',
        header: () => h('div', { class: 'text-center flex-1' }, 'Actions'),
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
                      triggerClass:
                          'inline-flex items-center gap-1 rounded-lg bg-primary px-3 py-1.5 text-xs font-bold text-primary-foreground transition-all hover:opacity-90',
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
                      triggerClass:
                          'inline-flex items-center gap-1 rounded-lg border border-border bg-background px-3 py-1.5 text-xs font-medium text-destructive transition-colors hover:bg-muted',
                      confirmLabel: 'Yes, Revoke',
                      confirmVariant: 'destructive',
                      successMessage: `${client.name} approval has been revoked.`,
                  });

            const viewBtn = h(
                Link,
                {
                    href: `/dashboard/clients/${client.id}`,
                    class: 'rounded-lg p-1.5 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground',
                    title: 'View Details',
                },
                () => h(Eye, { class: 'h-4 w-4' }),
            );

            const adminBtns = isAdminOrManager.value
                ? [
                      h(
                          Link,
                          {
                              href: `/dashboard/clients/${client.id}/edit`,
                              class: 'rounded-lg p-1.5 text-muted-foreground transition-colors hover:bg-muted hover:text-primary',
                              title: 'Edit',
                          },
                          () => h(Pencil, { class: 'h-4 w-4' }),
                      ),
                      h(ConfirmDialog, {
                          url: `/dashboard/clients/${client.id}`,
                          method: 'delete',
                          title: 'Delete Client?',
                          description: `This will soft delete ${client.name} from the system.`,
                          triggerIcon: Trash2,
                          triggerTooltip: 'Delete',
                          triggerVariant: 'ghost',
                          triggerClass:
                              'hover:bg-destructive/10 hover:text-destructive',
                          confirmVariant: 'destructive',
                          confirmLabel: 'Delete',
                          successMessage: `${client.name} has been deleted.`,
                      }),
                  ]
                : [];

            return h('div', { class: 'flex items-center justify-end gap-2' }, [
                approveBtn,
                viewBtn,
                ...adminBtns,
            ]);
        },
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Manage Clients" />
        <div class="min-h-screen bg-background">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <!-- Header -->
                <div
                    class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
                >
                    <div>
                        <h1
                            class="text-2xl font-bold tracking-tight text-foreground"
                        >
                            Manage Clients
                        </h1>
                        <p class="mt-1 text-sm text-muted-foreground">
                            All clients in the system
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <!-- My Approved Clients — all roles -->
                        <Link
                            href="/dashboard/clients/my-approved"
                            class="inline-flex items-center gap-2 rounded-xl border border-border bg-card px-4 py-2 text-sm font-medium text-foreground shadow-sm transition-colors hover:bg-muted"
                        >
                            <svg
                                class="h-4 w-4 text-primary"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>
                            Approved Clients
                        </Link>

                        <template v-if="isAdminOrManager">
                            <button
                                @click="exportExcel"
                                class="inline-flex items-center gap-2 rounded-xl border border-border bg-card px-4 py-2 text-sm font-medium text-foreground shadow-sm transition-colors hover:bg-muted"
                            >
                                <svg
                                    class="h-4 w-4 text-primary"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <rect
                                        x="3"
                                        y="3"
                                        width="18"
                                        height="18"
                                        rx="2"
                                        ry="2"
                                    />
                                    <line x1="3" y1="9" x2="21" y2="9" />
                                    <line x1="3" y1="15" x2="21" y2="15" />
                                    <line x1="9" y1="3" x2="9" y2="21" />
                                    <line x1="15" y1="3" x2="15" y2="21" />
                                </svg>
                                Export Excel
                            </button>
                        </template>

                        <Link
                            href="/dashboard/clients/create"
                            class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2 text-sm font-semibold text-primary-foreground shadow-sm transition-colors hover:opacity-90"
                        >
                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4v16m8-8H4"
                                />
                            </svg>
                            Add Client
                        </Link>
                    </div>
                </div>

                <!-- DataTable -->
                <div
                    class="rounded-2xl border border-border bg-card p-4 shadow-sm"
                >
                    <DataTable
                        :data="clients"
                        :columns="columns"
                        :filters="filters"
                        search-placeholder="Search clients by name..."
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
