<template>
    <div class="min-h-screen bg-slate-50">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Header -->
            <div
                class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
            >
                <div class="flex items-center gap-4">
                    <Link
                        href="/dashboard/clients"
                        class="rounded-xl border border-transparent p-2 text-slate-500 transition-all hover:border-slate-200 hover:bg-white hover:text-slate-800"
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
                        <h1
                            class="text-2xl font-bold tracking-tight text-slate-900"
                        >
                            Pending Approvals
                        </h1>
                        <p class="mt-1 text-sm text-slate-500">
                            <span class="font-semibold text-amber-600">{{
                                clients.total
                            }}</span>
                            client{{ clients.total !== 1 ? 's' : '' }} awaiting
                            approval
                        </p>
                    </div>
                </div>
            </div>

            <!-- Flash -->
            <div
                v-if="$page.props.flash?.success"
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
                {{ $page.props.flash.success }}
            </div>

            <!-- Table Card -->
            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-amber-50/60">
                        <tr>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold tracking-wider text-slate-500 uppercase"
                            >
                                Client
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold tracking-wider text-slate-500 uppercase"
                            >
                                Contact
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
                                class="px-6 py-4 text-left text-xs font-semibold tracking-wider text-slate-500 uppercase"
                            >
                                Registered
                            </th>
                            <th
                                class="px-6 py-4 text-right text-xs font-semibold tracking-wider text-slate-500 uppercase"
                            >
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <!-- Empty state -->
                        <tr v-if="clients.data.length === 0">
                            <td colspan="6" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <div
                                        class="mb-2 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100"
                                    >
                                        <svg
                                            class="h-8 w-8 text-emerald-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                    </div>
                                    <p class="font-semibold text-slate-700">
                                        All caught up!
                                    </p>
                                    <p class="text-sm text-slate-400">
                                        There are no clients pending approval.
                                    </p>
                                    <Link
                                        href="/dashboard/clients"
                                        class="mt-3 text-sm text-slate-600 underline underline-offset-2 hover:text-slate-900"
                                    >
                                        Back to approved clients
                                    </Link>
                                </div>
                            </td>
                        </tr>

                        <tr
                            v-for="client in clients.data"
                            :key="client.id"
                            class="transition-colors hover:bg-slate-50/60"
                        >
                            <!-- Name + Avatar -->
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
                                            ></span>
                                            Pending
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Contact -->
                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-800">
                                    {{ client.email }}
                                </p>
                                <p class="mt-0.5 text-xs text-slate-400">
                                    {{ client.mobile }}
                                </p>
                            </td>

                            <!-- Country -->
                            <td class="px-6 py-4 text-sm text-slate-700">
                                {{ client.country }}
                            </td>

                            <!-- Gender -->
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

                            <!-- Registered -->
                            <td class="px-6 py-4 text-sm text-slate-500">
                                {{ formatDate(client.created_at) }}
                            </td>

                            <!-- Approve Button -->
                            <td class="px-6 py-4 text-right">
                                <ConfirmDialog
                                    :url="`/dashboard/clients/${client.id}/approve`"
                                    method="post"
                                    title="Approve Client?"
                                    :description="`Approve ${client.name}? They will gain access to the platform.`"
                                    triggerLabel="Approve"
                                    triggerVariant="default"
                                    triggerClass="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-4 py-2 text-xs font-semibold text-white transition-colors hover:bg-emerald-700 hover:text-white"
                                    confirmLabel="Yes, Approve"
                                    confirmVariant="default"
                                    :successMessage="`${client.name} has been approved successfully.`"
                                />
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
                        <Link
                            v-for="link in clients.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            :class="[
                                'rounded-lg px-3 py-1.5 text-sm transition-colors',
                                link.active
                                    ? 'bg-slate-900 font-semibold text-white'
                                    : 'text-slate-600 hover:bg-slate-100',
                                !link.url
                                    ? 'pointer-events-none opacity-40'
                                    : '',
                            ]"
                        >
                            <span v-html="link.label"></span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import ConfirmDialog from '@/components/ConfirmDialog.vue';

defineProps({ clients: Object });

function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}
</script>
