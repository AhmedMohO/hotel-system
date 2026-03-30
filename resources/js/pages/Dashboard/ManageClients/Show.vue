<template>
    <div class="min-h-screen bg-slate-50">
        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
            <!-- Back + header -->
            <div class="mb-8 flex items-center gap-4">
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
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                    Client Profile
                </h1>
            </div>

            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >
                <!-- Avatar banner -->
                <div
                    class="relative h-28 bg-gradient-to-r from-slate-800 to-slate-700"
                >
                    <img
                        :src="
                            client.avatar_image
                                ? '/storage/' + client.avatar_image
                                : '/images/avatar.jpg'
                        "
                        alt=""
                        class="absolute -bottom-10 left-8 h-20 w-20 rounded-2xl border-4 border-white object-cover shadow-lg"
                    />
                </div>

                <div class="px-8 pt-14 pb-8">
                    <!-- Name + badge -->
                    <div class="mb-6 flex items-start justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-slate-900">
                                {{ client.name }}
                            </h2>
                            <p class="mt-0.5 text-sm text-slate-500">
                                {{ client.email }}
                            </p>
                        </div>
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700"
                        >
                            <span
                                class="h-1.5 w-1.5 rounded-full bg-emerald-500"
                            ></span>
                            Approved
                        </span>
                    </div>

                    <!-- Details grid -->
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div
                            class="rounded-xl border border-slate-100 bg-slate-50 p-4"
                        >
                            <p
                                class="mb-1 text-xs font-medium tracking-wider text-slate-400 uppercase"
                            >
                                Mobile
                            </p>
                            <p class="text-sm font-semibold text-slate-800">
                                {{ client.mobile }}
                            </p>
                        </div>
                        <div
                            class="rounded-xl border border-slate-100 bg-slate-50 p-4"
                        >
                            <p
                                class="mb-1 text-xs font-medium tracking-wider text-slate-400 uppercase"
                            >
                                Country
                            </p>
                            <p class="text-sm font-semibold text-slate-800">
                                {{ client.country }}
                            </p>
                        </div>
                        <div
                            class="rounded-xl border border-slate-100 bg-slate-50 p-4"
                        >
                            <p
                                class="mb-1 text-xs font-medium tracking-wider text-slate-400 uppercase"
                            >
                                Gender
                            </p>
                            <p class="text-sm font-semibold text-slate-800">
                                {{ client.gender }}
                            </p>
                        </div>
                        <div
                            class="rounded-xl border border-slate-100 bg-slate-50 p-4"
                        >
                            <p
                                class="mb-1 text-xs font-medium tracking-wider text-slate-400 uppercase"
                            >
                                Registered
                            </p>
                            <p class="text-sm font-semibold text-slate-800">
                                {{ formatDate(client.created_at) }}
                            </p>
                        </div>
                        <div
                            class="rounded-xl border border-slate-100 bg-slate-50 p-4"
                        >
                            <p
                                class="mb-1 text-xs font-medium tracking-wider text-slate-400 uppercase"
                            >
                                Approved At
                            </p>
                            <p class="text-sm font-semibold text-slate-800">
                                {{ formatDate(client.approved_at) }}
                            </p>
                        </div>
                        <div
                            class="rounded-xl border border-slate-100 bg-slate-50 p-4"
                        >
                            <p
                                class="mb-1 text-xs font-medium tracking-wider text-slate-400 uppercase"
                            >
                                Approved By
                            </p>
                            <p class="text-sm font-semibold text-slate-800">
                                {{ client.approved_by?.name ?? '—' }}
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div
                        class="mt-8 flex items-center gap-3 border-t border-slate-100 pt-6"
                    >
                        <Link
                            href="/dashboard/clients"
                            class="rounded-xl bg-slate-100 px-5 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-200"
                        >
                            Back to List
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({ client: Object });

function formatDate(dateStr) {
    if (!dateStr) {
        return '—';
    }

    return new Date(dateStr).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}
</script>
