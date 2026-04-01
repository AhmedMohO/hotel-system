<script setup>
import { Link } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';

defineProps({ client: Object });

function formatDate(dateStr) {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}
</script>

<template>
    <AppLayout :breadcrumbs="[
        { title: 'Clients', href: '/dashboard/clients' },
        { title: client.name, href: '' },
    ]">
        <div class="flex min-h-full flex-col">

            <!-- Top bar -->
            <div class="flex shrink-0 items-center justify-between gap-4 border-b px-6 py-3.5">
                <Button
                    variant="ghost"
                    size="sm"
                    class="-ml-2 gap-1.5 text-muted-foreground hover:text-foreground"
                    as-child
                >
                    <Link href="/dashboard/clients">
                        <ArrowLeft class="h-4 w-4" />
                        Clients
                    </Link>
                </Button>

                <span
                    v-if="client.approved_at"
                    class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400"
                >
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" />
                    Approved
                </span>
                <span
                    v-else
                    class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-950 dark:text-amber-400"
                >
                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-amber-400" />
                    Pending
                </span>
            </div>

            <!-- Body -->
            <div class="flex flex-1 flex-col lg:flex-row">

                <!-- Sidebar / Avatar -->
                <aside class="w-full shrink-0 border-b lg:w-72 lg:border-r lg:border-b-0 xl:w-80">
                    <div class="flex flex-col items-center gap-4 px-8 py-10 text-center">
                        <img
                            :src="client.avatar_image ? '/storage/' + client.avatar_image : '/images/avatar.jpg'"
                            alt=""
                            class="h-24 w-24 rounded-full object-cover ring-4 ring-border shadow-md"
                        />
                        <div>
                            <h2 class="text-lg font-semibold">{{ client.name }}</h2>
                            <p class="text-sm text-muted-foreground">{{ client.email }}</p>
                        </div>

                        <span
                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium"
                            :class="client.gender === 'Male'
                                ? 'bg-sky-100 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300'
                                : 'bg-pink-100 text-pink-700 dark:bg-pink-950/60 dark:text-pink-300'"
                        >
                            {{ client.gender }}
                        </span>
                    </div>
                </aside>

                <!-- Details -->
                <main class="flex-1 p-6 lg:p-8">
                    <h3 class="mb-4 text-sm font-semibold tracking-wider text-muted-foreground uppercase">
                        Client Details
                    </h3>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                        <div class="rounded-lg border border-border bg-muted/40 p-4">
                            <p class="mb-1 text-xs font-semibold tracking-wider text-muted-foreground uppercase">Mobile</p>
                            <p class="text-sm font-medium text-foreground">{{ client.mobile || '—' }}</p>
                        </div>

                        <div class="rounded-lg border border-border bg-muted/40 p-4">
                            <p class="mb-1 text-xs font-semibold tracking-wider text-muted-foreground uppercase">Country</p>
                            <p class="text-sm font-medium text-foreground">{{ client.country || '—' }}</p>
                        </div>

                        <div class="rounded-lg border border-border bg-muted/40 p-4">
                            <p class="mb-1 text-xs font-semibold tracking-wider text-muted-foreground uppercase">Registered</p>
                            <p class="text-sm font-medium text-foreground tabular-nums">{{ formatDate(client.created_at) }}</p>
                        </div>

                        <div class="rounded-lg border border-border bg-muted/40 p-4">
                            <p class="mb-1 text-xs font-semibold tracking-wider text-muted-foreground uppercase">Approved At</p>
                            <p class="text-sm font-medium text-foreground tabular-nums">{{ formatDate(client.approved_at) }}</p>
                        </div>

                        <div class="rounded-lg border border-border bg-muted/40 p-4 sm:col-span-2">
                            <p class="mb-1 text-xs font-semibold tracking-wider text-muted-foreground uppercase">Approved By</p>
                            <p class="text-sm font-medium text-foreground">{{ client.approved_by?.name ?? '—' }}</p>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </AppLayout>
</template>