<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-slate-50">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-6">
                    <h1
                        class="text-2xl font-bold tracking-tight text-slate-900"
                    >
                        Manage Clients
                    </h1>
                    <p class="mt-1 text-sm text-slate-500">
                        Manage and approve client accounts
                    </p>
                </div>

                <!-- Internal Tab Navigation -->
                <div
                    class="mb-6 flex w-fit gap-1 rounded-xl border border-slate-200 bg-white p-1 shadow-sm"
                >
                    <Link
                        href="/dashboard/receptionist/clients"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-100"
                    >
                        Unapproved Clients
                    </Link>
                    <Link
                        href="/dashboard/receptionist/clients/my-approved"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-100"
                    >
                        My Approved Clients
                    </Link>
                    <Link
                        href="/dashboard/receptionist/clients/reservations"
                        class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white transition-colors"
                    >
                        Clients Reservations
                    </Link>
                </div>

                <!-- Table -->
                <div
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                >
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/80">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold tracking-wider text-slate-500 uppercase"
                                >
                                    Client Name
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold tracking-wider text-slate-500 uppercase"
                                >
                                    Room Number
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold tracking-wider text-slate-500 uppercase"
                                >
                                    Accompany Number
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold tracking-wider text-slate-500 uppercase"
                                >
                                    Paid Price
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-if="reservations.data.length === 0">
                                <td colspan="4" class="px-6 py-20 text-center">
                                    <div
                                        class="flex flex-col items-center gap-2 text-slate-400"
                                    >
                                        <svg
                                            class="h-12 w-12 text-slate-200"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1.5"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                            />
                                        </svg>
                                        <p class="text-sm font-medium">
                                            No reservations found.
                                        </p>
                                        <p class="text-xs">
                                            Your approved clients haven't made
                                            reservations yet.
                                        </p>
                                    </div>
                                </td>
                            </tr>

                            <tr
                                v-for="reservation in reservations.data"
                                :key="reservation.id"
                                class="transition-colors hover:bg-slate-50/60"
                            >
                                <td class="px-6 py-4">
                                    <p
                                        class="text-sm font-semibold text-slate-900"
                                    >
                                        {{ reservation.client?.name ?? '—' }}
                                    </p>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1 text-sm font-semibold text-slate-700"
                                    >
                                        #{{ reservation.room?.number ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ reservation.accompany_number }}
                                    <span class="text-xs text-slate-400"
                                        >guests</span
                                    >
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="text-sm font-semibold text-emerald-700"
                                    >
                                        ${{
                                            Number(
                                                reservation.paid_price,
                                            ).toLocaleString()
                                        }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div
                        v-if="reservations.last_page > 1"
                        class="flex items-center justify-between border-t border-slate-100 px-6 py-4"
                    >
                        <p class="text-sm text-slate-500">
                            Showing {{ reservations.from }}–{{
                                reservations.to
                            }}
                            of {{ reservations.total }}
                        </p>
                        <div class="flex gap-1">
                            <template
                                v-for="link in reservations.links"
                                :key="link.label"
                            >
                                <a
                                    v-if="link.url"
                                    :href="link.url"
                                    v-html="link.label"
                                    :class="[
                                        'rounded-lg px-3 py-1.5 text-sm transition-colors',
                                        link.active
                                            ? 'bg-slate-900 font-semibold text-white'
                                            : 'text-slate-600 hover:bg-slate-100',
                                    ]"
                                />
                                <span
                                    v-else
                                    v-html="link.label"
                                    class="rounded-lg px-3 py-1.5 text-sm text-slate-400 opacity-40"
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

type Reservation = {
    id: number;
    accompany_number: number;
    paid_price: number;
    created_at: string;
    client: { name: string } | null;
    room: { number: string } | null;
};

type PaginationLink = { label: string; url: string | null; active: boolean };

type Pagination = {
    data: Reservation[];
    from: number;
    to: number;
    total: number;
    last_page: number;
    links: PaginationLink[];
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Manage Clients', href: '/dashboard/receptionist/clients' },
    {
        title: 'Clients Reservations',
        href: '/dashboard/receptionist/clients/reservations',
    },
];

const { reservations } = defineProps<{ reservations: Pagination }>();
</script>
