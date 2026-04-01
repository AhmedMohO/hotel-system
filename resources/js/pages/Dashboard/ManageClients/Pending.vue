<script setup>
import { Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps({ clients: Object });

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Clients', href: '/dashboard/clients' },
    { title: 'Pending Approvals', href: '' },
];

function formatDate(dateStr) {
    return new Date(dateStr).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-5 p-6">

            <!-- Page Header -->
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="space-y-0.5">
                    <h1 class="text-xl font-semibold tracking-tight">Pending Approvals</h1>
                    <p class="text-sm text-muted-foreground">
                        <span class="font-medium text-amber-600 dark:text-amber-400">{{ clients.total }}</span>
                        client{{ clients.total !== 1 ? 's' : '' }} awaiting approval.
                    </p>
                </div>
                <Button variant="outline" size="sm" class="w-fit gap-2" as-child>
                    <Link href="/dashboard/clients">
                        <ArrowLeft class="h-4 w-4" />
                        Back to Clients
                    </Link>
                </Button>
            </div>

            <!-- Flash -->
            <div
                v-if="$page.props.flash?.success"
                class="rounded-md border border-green-500 bg-green-500/10 px-4 py-3 text-sm text-green-600"
            >
                {{ $page.props.flash.success }}
            </div>

            <!-- Table Card -->
            <div class="overflow-hidden rounded-xl border border-border bg-card shadow-sm">
                <table class="min-w-full divide-y divide-border">
                    <thead class="bg-muted/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold tracking-wider text-muted-foreground uppercase">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold tracking-wider text-muted-foreground uppercase">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold tracking-wider text-muted-foreground uppercase">Country</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold tracking-wider text-muted-foreground uppercase">Gender</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold tracking-wider text-muted-foreground uppercase">Registered</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold tracking-wider text-muted-foreground uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">

                        <!-- Empty state -->
                        <tr v-if="clients.data.length === 0">
                            <td colspan="6" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <div class="mb-2 flex h-14 w-14 items-center justify-center rounded-full bg-primary/10">
                                        <svg class="h-7 w-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <p class="font-medium text-foreground">All caught up!</p>
                                    <p class="text-sm text-muted-foreground">There are no clients pending approval.</p>
                                    <Link href="/dashboard/clients" class="mt-2 text-sm text-primary underline underline-offset-2 hover:opacity-80">
                                        Back to clients
                                    </Link>
                                </div>
                            </td>
                        </tr>

                        <tr
                            v-for="client in clients.data"
                            :key="client.id"
                            class="transition-colors hover:bg-muted/40"
                        >
                            <!-- Name + Avatar -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img
                                        :src="client.avatar_image ? '/storage/' + client.avatar_image : '/images/avatar.jpg'"
                                        alt=""
                                        class="h-9 w-9 flex-shrink-0 rounded-full object-cover ring-2 ring-border"
                                    />
                                    <div>
                                        <p class="text-sm font-medium text-foreground">{{ client.name }}</p>
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-600 dark:text-amber-400">
                                            <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-amber-400"></span>
                                            Pending
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Contact -->
                            <td class="px-6 py-4">
                                <p class="text-sm text-foreground">{{ client.email }}</p>
                                <p class="mt-0.5 text-xs text-muted-foreground">{{ client.mobile }}</p>
                            </td>

                            <!-- Country -->
                            <td class="px-6 py-4 text-sm text-muted-foreground">{{ client.country }}</td>

                            <!-- Gender -->
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                    :class="client.gender === 'Male' ? 'bg-sky-100 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300' : 'bg-pink-100 text-pink-700 dark:bg-pink-950/60 dark:text-pink-300'"
                                >
                                    {{ client.gender }}
                                </span>
                            </td>

                            <!-- Registered -->
                            <td class="px-6 py-4 text-sm text-muted-foreground tabular-nums">
                                {{ formatDate(client.created_at) }}
                            </td>

                            <!-- Approve -->
                            <td class="px-6 py-4 text-right">
                                <ConfirmDialog
                                    :url="`/dashboard/clients/${client.id}/approve`"
                                    method="post"
                                    title="Approve Client?"
                                    :description="`Approve ${client.name}? They will gain access to the platform.`"
                                    triggerLabel="Approve"
                                    triggerVariant="default"
                                    triggerClass="h-7 rounded-md px-2.5 text-xs font-semibold"
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
                    class="flex items-center justify-between border-t border-border px-6 py-4"
                >
                    <p class="text-sm text-muted-foreground">
                        Showing {{ clients.from }}–{{ clients.to }} of {{ clients.total }}
                    </p>
                    <div class="flex gap-1">
                        <Link
                            v-for="link in clients.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            :class="[
                                'rounded-md px-3 py-1.5 text-sm transition-colors',
                                link.active ? 'bg-primary font-semibold text-primary-foreground' : 'text-muted-foreground hover:bg-muted',
                                !link.url ? 'pointer-events-none opacity-40' : '',
                            ]"
                        >
                            <span v-html="link.label"></span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>