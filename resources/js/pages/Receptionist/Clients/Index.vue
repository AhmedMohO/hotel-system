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
                        class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white transition-colors"
                    >
                        Unapproved Clients
                    </Link>
                    <Link
                        href="/dashboard/receptionist/clients/my-approved"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-100"
                    >
                        My Approved Clients
                    </Link>
                    <!-- <Link
                        href="/dashboard/receptionist/clients/reservations"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-100"
                    >
                        Clients Reservations
                    </Link> -->
                </div>

                <!-- Flash -->
                <div
                    v-if="flash.success"
                    class="mb-6 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-700"
                >
                    <svg
                        class="h-5 w-5 flex-shrink-0"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    {{ flash.success }}
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
                                    Email
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold tracking-wider text-slate-500 uppercase"
                                >
                                    Mobile
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold tracking-wider text-slate-500 uppercase"
                                >
                                    Country
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold tracking-wider text-slate-500 uppercase"
                                >
                                    Gender
                                </th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-semibold tracking-wider text-slate-500 uppercase"
                                >
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-if="clients.data.length === 0">
                                <td colspan="6" class="px-6 py-20 text-center">
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
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                        <p class="text-sm font-medium">
                                            All caught up! No pending clients.
                                        </p>
                                    </div>
                                </td>
                            </tr>

                            <tr
                                v-for="client in clients.data"
                                :key="client.id"
                                class="transition-colors hover:bg-slate-50/60"
                            >
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img
                                            :src="
                                                client.avatar_image
                                                    ? '/storage/' +
                                                      client.avatar_image
                                                    : '/images/avatar.jpg'
                                            "
                                            alt=""
                                            class="h-9 w-9 flex-shrink-0 rounded-full object-cover ring-2 ring-amber-100"
                                        />
                                        <div>
                                            <p
                                                class="text-sm font-semibold text-slate-900"
                                            >
                                                {{ client.name }}
                                            </p>
                                            <span
                                                class="inline-flex items-center gap-1 text-xs font-medium text-amber-600"
                                            >
                                                <span
                                                    class="h-1.5 w-1.5 animate-pulse rounded-full bg-amber-400"
                                                />
                                                Pending
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ client.email }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ client.mobile }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ client.country }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                        :class="
                                            client.gender === 'Male'
                                                ? 'bg-blue-50 text-blue-600'
                                                : 'bg-pink-50 text-pink-600'
                                        "
                                    >
                                        {{ client.gender }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button
                                        @click="approve(client)"
                                        :disabled="approvingId === client.id"
                                        class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-semibold text-white transition-colors hover:bg-emerald-700 disabled:bg-emerald-400"
                                    >
                                        <svg
                                            v-if="approvingId !== client.id"
                                            class="h-3.5 w-3.5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2.5"
                                                d="M5 13l4 4L19 7"
                                            />
                                        </svg>
                                        <svg
                                            v-else
                                            class="h-3.5 w-3.5 animate-spin"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                        >
                                            <circle
                                                class="opacity-25"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                                stroke="currentColor"
                                                stroke-width="4"
                                            />
                                            <path
                                                class="opacity-75"
                                                fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                                            />
                                        </svg>
                                        {{
                                            approvingId === client.id
                                                ? 'Approving…'
                                                : 'Approve'
                                        }}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div
                        v-if="clients.last_page > 1"
                        class="flex items-center justify-between border-t border-slate-100 px-6 py-4"
                    >
                        <p class="text-sm text-slate-500">
                            Showing {{ clients.from }}–{{ clients.to }} of
                            {{ clients.total }}
                        </p>
                        <div class="flex gap-1">
                            <template
                                v-for="link in clients.links"
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
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
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
    from: number;
    to: number;
    total: number;
    last_page: number;
    links: PaginationLink[];
};

type SharedProps = {
    flash: { success: string | null; error: string | null };
};

// ── Breadcrumbs (plain strings — no route() calls here) ───────────────────────

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Manage Clients', href: '/dashboard/receptionist/clients' },
];

// ── Props ─────────────────────────────────────────────────────────────────────

const { clients } = defineProps<{ clients: Pagination }>();

// ── Flash ─────────────────────────────────────────────────────────────────────

const page = usePage<SharedProps>();
const flash = computed(() => page.props.flash);

// ── State ─────────────────────────────────────────────────────────────────────

const approvingId = ref<number | null>(null);

// ── Methods ───────────────────────────────────────────────────────────────────

function approve(client: Client) {
    approvingId.value = client.id;
    router.post(
        `/dashboard/receptionist/clients/${client.id}/approve`,
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                approvingId.value = null;
            },
        },
    );
}
</script>
