<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { ColumnDef } from '@tanstack/vue-table';
import { h } from 'vue';
import DataTable from '@/components/DataTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

type Client = {
    id: number;
    name: string;
    email: string;
    mobile: string;
    country: string;
    gender: string;
    avatar_image: string | null;
    approved_at: string;
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

// ── Props ─────────────────────────────────────────────────────────────────────
const { clients, filters, backHref } = defineProps<{
    clients: Pagination;
    filters?: Record<string, any>;
    backHref?: string;
}>();

// ── Breadcrumbs ───────────────────────────────────────────────────────────────
const isReceptionist = backHref === '/dashboard/receptionist/clients';
const breadcrumbs: BreadcrumbItem[] = isReceptionist
    ? [
          { title: 'Dashboard', href: '/dashboard' },
          { title: 'Manage Clients', href: '/dashboard/receptionist/clients' },
          {
              title: 'My Approved Clients',
              href: '/dashboard/receptionist/clients/my-approved',
          },
      ]
    : [
          { title: 'Dashboard', href: '/dashboard' },
          { title: 'Manage Clients', href: '/dashboard/clients' },
          { title: 'Approved Clients', href: '/dashboard/clients/my-approved' },
      ];

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
                    alt: '',
                    class: 'h-9 w-9 flex-shrink-0 rounded-full object-cover ring-2 ring-primary/20',
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
                            class: 'inline-flex items-center gap-1 text-xs font-medium text-emerald-500 dark:text-emerald-400',
                        },
                        [
                            h('span', {
                                class: 'h-1.5 w-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400',
                            }),
                            'Approved',
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
            h(
                'span',
                { class: 'text-sm text-muted-foreground' },
                row.original.email,
            ),
    },
    {
        accessorKey: 'mobile',
        header: 'Mobile',
        enableSorting: false,
        cell: ({ row }) =>
            h(
                'span',
                { class: 'text-sm text-muted-foreground' },
                row.original.mobile,
            ),
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
        accessorKey: 'approved_at',
        header: 'Approved At',
        enableSorting: false,
        cell: ({ row }) =>
            h(
                'span',
                { class: 'text-sm text-muted-foreground' },
                formatDate(row.original.approved_at),
            ),
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-background">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6 flex items-center gap-4">
                    <Link
                        :href="backHref ?? '/dashboard/clients'"
                        class="rounded-xl border border-transparent p-2 text-muted-foreground transition-all hover:border-border hover:bg-card hover:text-foreground"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 19l-7-7 7-7"
                            />
                        </svg>
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-foreground">
                            {{
                                isReceptionist
                                    ? 'My Approved Clients'
                                    : 'Approved Clients'
                            }}
                        </h1>
                        <p class="mt-1 text-sm text-muted-foreground">
                            <span class="font-semibold text-primary">{{
                                clients.total
                            }}</span>
                            approved client{{ clients.total !== 1 ? 's' : '' }}
                        </p>
                    </div>
                </div>

                <!-- DataTable -->
                <div class="rounded-2xl border border-border bg-card p-4 shadow-sm">
                    <DataTable
                        :data="clients"
                        :columns="columns"
                        :filters="filters"
                        search-placeholder="Search by name..."
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>