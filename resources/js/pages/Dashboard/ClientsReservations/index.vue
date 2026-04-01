<script setup lang="ts">
import type { ColumnDef } from '@tanstack/vue-table';
import { computed, h } from 'vue';
import DataTable from '@/components/DataTable.vue';
import UserAvatar from '@/components/UserAvatar.vue';
import AppLayout from '@/layouts/AppLayout.vue';

interface ReservationRow {
    id: number;
    client: {
        name: string | null;
        avatar_image?: string | null;
    };
    room: { number: string | null };
    accompany_number: number;
    check_in: string | null;
    check_out: string | null;
    paid_price: number | null;
    status: string;
    approved_by: number | null;
    approved_by_name: string | null;
}

interface PaginatedReservations {
    data: ReservationRow[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
    links: { url: string | null; label: string; active: boolean }[];
}

interface TableFilters {
    filter?: {
        name?: string;
    };
    sort?: string;
    per_page?: string | number;
}

const props = defineProps<{
    reservations: PaginatedReservations;
    filters: TableFilters;
    canSeeApprovedBy: boolean;
}>();

const dateFormatter = new Intl.DateTimeFormat('en-GB', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
});

function formatDate(value: string | null): string {
    if (!value) {
        return '-';
    }

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return dateFormatter.format(date);
}

const columns = computed<ColumnDef<ReservationRow>[]>(() => {
    const items: ColumnDef<ReservationRow>[] = [
        {
        id: 'client_name',
        accessorFn: (row) => row.client?.name ?? '-',
        header: 'Client Name',
        enableSorting: true,
        cell: ({ row }) => {
            const client = row.original.client;

            return h('div', { class: 'flex items-center gap-3' }, [
                h(UserAvatar, {
                    user: {
                        name: client?.name ?? 'Unknown Client',
                        avatar_image: client?.avatar_image,
                    },
                    class: 'h-8 w-8 rounded-full border border-border shadow-none',
                }),
                h('span', { class: 'font-medium' }, client?.name ?? '-'),
            ]);
        },
    },
        {
        accessorKey: 'accompany_number',
        header: 'Accompany No.',
        enableSorting: true,
        },
        {
        id: 'room_number',
        accessorFn: (row) => row.room?.number ?? '-',
        header: 'Room Number',
        enableSorting: true,
        },
        {
        accessorKey: 'check_in',
        header: 'Check In',
        enableSorting: true,
        cell: ({ row }) => formatDate(row.original.check_in),
        },
        {
        accessorKey: 'check_out',
        header: 'Check Out',
        enableSorting: true,
        cell: ({ row }) => formatDate(row.original.check_out),
        },
        {
        accessorKey: 'paid_price',
        header: 'Paid Price',
        enableSorting: true,
        cell: ({ row }) => `$${Number(row.original.paid_price ?? 0).toFixed(2)}`,
        },
        {
        accessorKey: 'status',
        header: 'Status',
        enableSorting: true,
        cell: ({ row }) => {
            const label = row.original.status === 'approved' ? 'Approved' : 'Pending';
            const classes = row.original.status === 'approved'
                ? 'inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700'
                : 'inline-flex items-center rounded-full border border-amber-200 bg-amber-50 px-2 py-0.5 text-xs font-medium text-amber-700';

            return h('span', { class: classes }, label);
        },
        },
    ];

    if (props.canSeeApprovedBy) {
        items.push({
            accessorKey: 'approved_by_name',
            header: 'Approved By',
            cell: ({ row }) => row.original.approved_by_name ?? '-',
        });
    }

    return items;
});
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Clients Reservations', href: '/dashboard/clients-reservations' }]">
        <div class="space-y-6 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-primary-900 dark:text-primary-100">Clients Reservations</h1>
                    <p class="mt-2 text-sm text-primary-700 dark:text-primary-300">Track completed bookings that have already been approved.</p>
                </div>
            </div>

            <DataTable :data="reservations" :columns="columns" :filters="filters" />
        </div>
    </AppLayout>
</template>
