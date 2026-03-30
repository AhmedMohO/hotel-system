<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { ColumnDef } from '@tanstack/vue-table';
import { h } from 'vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import DataTable from '@/components/DataTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

// ── Types ─────────────────────────────────────────────────────────────────────
type Client = {
    id: number;
    name: string;
    email: string;
    mobile: string;
    country: string;
    gender: string;
    avatar_image: string | null;
    created_at: string;
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

// ── Breadcrumbs ───────────────────────────────────────────────────────────────
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Manage Clients', href: '/dashboard/receptionist/clients' },
];

// ── Props ─────────────────────────────────────────────────────────────────────
const { clients, filters } = defineProps<{
    clients: Pagination;
    filters?: Record<string, any>;
}>();

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
                    alt: '',
                    class: 'h-9 w-9 flex-shrink-0 rounded-full object-cover ring-2 ring-border',
                }),
                h('div', {}, [
                    h(
                        'p',
                        { class: 'text-sm font-semibold text-foreground' },
                        row.original.name,
                    ),
                    h(
                        'span',
                        {
                            class: 'inline-flex items-center gap-1 text-xs font-medium text-yellow-500',
                        },
                        [
                            h('span', {
                                class: 'h-1.5 w-1.5 animate-pulse rounded-full bg-yellow-500',
                            }),
                            'Pending',
                        ],
                    ),
                ]),
            ]),
    },
    {
        accessorKey: 'email',
        header: 'Email',
        enableSorting: false,
        cell: ({ row }) =>
            h('div', {}, [
                h(
                    'p',
                    { class: 'text-sm text-foreground' },
                    row.original.email,
                ),
                h(
                    'p',
                    { class: 'mt-0.5 text-xs text-muted-foreground' },
                    row.original.mobile,
                ),
            ]),
    },
    {
        accessorKey: 'country',
        header: 'Country',
        enableSorting: false,
        cell: ({ row }) =>
            h(
                'span',
                { class: 'text-sm text-foreground' },
                row.original.country,
            ),
    },
    {
        accessorKey: 'gender',
        header: 'Gender',
        enableSorting: false,
        cell: ({ row }) =>
            h(
                'span',
                {
                    class: [
                        'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium',
                        row.original.gender === 'Male'
                            ? 'bg-blue-50 text-blue-600 dark:bg-blue-950 dark:text-blue-300'
                            : 'bg-pink-50 text-pink-600 dark:bg-pink-950 dark:text-pink-300',
                    ],
                },
                row.original.gender,
            ),
    },
    {
        accessorKey: 'created_at',
        header: 'Registered',
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
        header: () =>
            h('div', { class: 'text-center justify-center flex-1' }, 'Action'),
        enableSorting: false,
        cell: ({ row }) => {
            const client = row.original;

            return h('div', { class: 'flex justify-center' }, [
                h(ConfirmDialog, {
                    url: `/dashboard/receptionist/clients/${client.id}/approve`,
                    method: 'post',
                    title: 'Approve Client?',
                    description: `Approve ${client.name}? They will be notified and gain access to the platform.`,
                    triggerLabel: 'Approve',
                    triggerVariant: 'default',
                    triggerClass:
                        'bg-primary text-primary-foreground hover:opacity-90 text-xs rounded-xl px-4',
                    confirmLabel: 'Yes, Approve',
                    confirmVariant: 'default',
                    successMessage: `${client.name} has been approved successfully.`,
                }),
            ]);
        },
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
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
                            Manage and approve client accounts
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <Link
                            href="/dashboard/receptionist/clients/my-approved"
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
                            My Approved Clients
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
