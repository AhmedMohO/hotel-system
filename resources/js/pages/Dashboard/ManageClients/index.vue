<script setup lang="ts">
import { Link, router, usePage, Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
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
    from: number;
    to: number;
    total: number;
    last_page: number;
    links: PaginationLink[];
};

type SharedProps = {
    auth: { user: { roles: string[] } };
    flash: { success: string | null; error: string | null };
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Manage Clients', href: '/dashboard/manage-clients' },
];

// ── Props ─────────────────────────────────────────────────────────────────────
const { clients } = defineProps<{ clients: Pagination }>();

// ── Auth ──────────────────────────────────────────────────────────────────────
const page = usePage<SharedProps>();
const flash = computed(
    () => page.props.flash ?? { success: null, error: null },
);
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

function approve(client: Client) {
    router.post(
        `/dashboard/clients/${client.id}/approve`,
        {},
        {
            preserveScroll: true,
        },
    );
}

function unapprove(client: Client) {
    router.patch(
        `/dashboard/clients/${client.id}/unapprove`,
        {},
        {
            preserveScroll: true,
        },
    );
}

// ── Delete ────────────────────────────────────────────────────────────────────
const deleteTarget = ref<{ id: number; name: string } | null>(null);

function confirmDelete(client: { id: number; name: string }) {
    deleteTarget.value = client;
}

function doDelete() {
    if (!deleteTarget.value?.id) {
        return;
    }

    router.delete(`/dashboard/clients/${deleteTarget.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            deleteTarget.value = null;
        },
    });
}

function formatDate(dateStr: string) {
    return new Date(dateStr).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Manage Clients" />
        <div class="min-h-screen bg-slate-50">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <div
                    class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
                >
                    <div>
                        <h1
                            class="text-2xl font-bold tracking-tight text-slate-900"
                        >
                            Manage Clients
                        </h1>
                        <p class="mt-1 text-sm text-slate-500">
                            All clients in the system
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <template v-if="isAdminOrManager">
                            <button
                                @click="exportExcel"
                                class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-medium text-emerald-700 shadow-sm transition-colors hover:bg-emerald-100"
                            >
                                <svg
                                    class="h-4 w-4"
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
                                    ></rect>
                                    <line x1="3" y1="9" x2="21" y2="9"></line>
                                    <line x1="3" y1="15" x2="21" y2="15"></line>
                                    <line x1="9" y1="3" x2="9" y2="21"></line>
                                    <line x1="15" y1="3" x2="15" y2="21"></line>
                                </svg>
                                Export Excel
                            </button>
                        </template>
                        <Link
                            href="/dashboard/clients/create"
                            class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-5 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-slate-800"
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
                                ></path>
                            </svg>
                            Add Client
                        </Link>
                    </div>
                </div>

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
                        ></path>
                    </svg>
                    {{ flash.success }}
                </div>

                <div
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                >
                    <table class="min-w-full divide-y divide-slate-100">
                        <thead class="bg-slate-50/80">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold tracking-wider text-slate-500 uppercase"
                                >
                                    Client
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold tracking-wider text-slate-500 uppercase"
                                >
                                    Email
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold tracking-wider text-slate-500 uppercase"
                                >
                                    Status
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold tracking-wider text-slate-500 uppercase"
                                >
                                    Created At
                                </th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-semibold tracking-wider text-slate-500 uppercase"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr v-if="clients.data.length === 0">
                                <td colspan="5" class="px-6 py-20 text-center">
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
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"
                                            ></path>
                                        </svg>
                                        <p class="text-sm font-medium">
                                            No clients found
                                        </p>
                                    </div>
                                </td>
                            </tr>

                            <tr
                                v-for="client in clients.data"
                                :key="client.id"
                                class="group transition-colors hover:bg-slate-50/60"
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
                                            class="h-9 w-9 flex-shrink-0 rounded-full object-cover ring-2 ring-slate-100"
                                        />
                                        <span
                                            class="text-sm font-semibold text-slate-900"
                                            >{{ client.name }}</span
                                        >
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-600">
                                    {{ client.email }}
                                </td>

                                <td class="px-6 py-4">
                                    <span
                                        v-if="client.approved_at"
                                        class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-600/20 ring-inset"
                                    >
                                        <span
                                            class="h-1.5 w-1.5 rounded-full bg-emerald-500"
                                        ></span>
                                        Approved
                                    </span>
                                    <span
                                        v-else
                                        class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 ring-1 ring-amber-600/20 ring-inset"
                                    >
                                        <span
                                            class="h-1.5 w-1.5 rounded-full bg-amber-500"
                                        ></span>
                                        Pending
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm text-slate-500">
                                    {{ formatDate(client.created_at) }}
                                </td>

                                <td class="px-6 py-4">
                                    <div
                                        class="flex items-center justify-end gap-2"
                                    >
                                        <button
                                            v-if="!client.approved_at"
                                            @click="approve(client)"
                                            class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-bold text-white transition-all hover:bg-emerald-700"
                                        >
                                            <svg
                                                class="h-3.5 w-3.5"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M5 13l4 4L19 7"
                                                ></path>
                                            </svg>
                                            Approve
                                        </button>
                                        <button
                                            v-else
                                            @click="unapprove(client)"
                                            class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 transition-colors hover:bg-slate-50"
                                        >
                                            <svg
                                                class="h-3.5 w-3.5 text-red-500"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"
                                                ></path>
                                            </svg>
                                            Revoke
                                        </button>

                                        <Link
                                            :href="`/dashboard/clients/${client.id}`"
                                            class="rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-600"
                                            title="View Details"
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
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                                ></path>
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                                ></path>
                                            </svg>
                                        </Link>

                                        <template v-if="isAdminOrManager">
                                            <Link
                                                :href="`/dashboard/clients/${client.id}/edit`"
                                                class="rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-blue-50 hover:text-blue-600"
                                                title="Edit"
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
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                                    ></path>
                                                </svg>
                                            </Link>
                                            <button
                                                @click="confirmDelete(client)"
                                                class="rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-red-50 hover:text-red-600"
                                                title="Delete"
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
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                    ></path>
                                                </svg>
                                            </button>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div
                        v-if="clients.last_page > 1"
                        class="flex items-center justify-between border-t border-slate-100 px-6 py-4"
                    >
                        <p class="text-sm text-slate-500">
                            Showing {{ clients.from }}–{{ clients.to }} of
                            {{ clients.total }} clients
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
                                ></a>
                                <span
                                    v-else
                                    v-html="link.label"
                                    class="rounded-lg px-3 py-1.5 text-sm text-slate-400 opacity-40"
                                ></span>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <Teleport to="body">
                <div
                    v-if="deleteTarget"
                    class="fixed inset-0 z-50 flex items-center justify-center p-4"
                >
                    <div
                        class="absolute inset-0 bg-black/40 backdrop-blur-sm"
                        @click="deleteTarget = null"
                    ></div>
                    <div
                        class="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl"
                    >
                        <div
                            class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100"
                        >
                            <svg
                                class="h-6 w-6 text-red-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                ></path>
                            </svg>
                        </div>
                        <h3
                            class="mb-1 text-center text-lg font-bold text-slate-900"
                        >
                            Delete Client?
                        </h3>
                        <p class="mb-6 text-center text-sm text-slate-500">
                            This will permanently remove
                            <strong class="text-slate-700">{{
                                deleteTarget.name
                            }}</strong>
                            and all their data.
                        </p>
                        <div class="flex gap-3">
                            <button
                                @click="deleteTarget = null"
                                class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50"
                            >
                                Cancel
                            </button>
                            <button
                                @click="doDelete"
                                class="flex-1 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-red-700"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </Teleport>
        </div>
    </AppLayout>
</template>
