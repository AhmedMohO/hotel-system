<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Trash2,
    Mail,
    Fingerprint,
    Calendar,
    Hash,
} from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import UserAvatar from '@/components/UserAvatar.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import * as Managers from '@/routes/managers';

defineProps<{
    manager: {
        id: number;
        name: string;
        email: string;
        national_id: string;
        avatar_image: string | null;
        created_at: string;
        deleted_at: string | null;
    };
}>();
</script>

<template>
    <Head :title="`${manager.name} — Manager`" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Managers', href: Managers.index.url() },
            { title: manager.name, href: '' },
        ]"
    >
        <div class="flex min-h-full flex-col">
            <!-- Top bar -->
            <div
                class="flex shrink-0 items-center justify-between gap-4 border-b px-6 py-3.5"
            >
                <Button
                    variant="ghost"
                    size="sm"
                    class="-ml-2 gap-1.5 text-muted-foreground hover:text-foreground"
                    as-child
                >
                    <Link :href="Managers.index.url()">
                        <ArrowLeft class="h-4 w-4" />
                        Managers
                    </Link>
                </Button>

                <span
                    v-if="manager.deleted_at"
                    class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-950 dark:text-red-400"
                >
                    <Trash2 class="h-3.5 w-3.5" />
                    Deleted
                </span>
                <span
                    v-else
                    class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400"
                >
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" />
                    Active
                </span>
            </div>

            <!-- Body: sidebar + main -->
            <div class="flex flex-1 flex-col lg:flex-row">
                <!-- Sidebar -->
                <aside
                    class="w-full shrink-0 border-b lg:w-72 lg:border-r lg:border-b-0 xl:w-80"
                >
                    <div
                        class="flex flex-col items-center gap-4 px-8 py-10 text-center"
                    >
                        <UserAvatar
                            :user="manager"
                            class="h-24 w-24 rounded-full ring-4 ring-border"
                            :class="{
                                'opacity-60 grayscale': manager.deleted_at,
                            }"
                        />
                        <div>
                            <h1 class="text-lg leading-tight font-semibold">
                                {{ manager.name }}
                            </h1>
                            <p class="mt-1 text-sm text-muted-foreground">
                                Manager
                            </p>
                        </div>
                    </div>

                    <Separator />

                    <div class="flex flex-col divide-y">
                        <div class="flex items-start gap-3 px-6 py-4">
                            <Mail
                                class="mt-0.5 h-4 w-4 shrink-0 text-muted-foreground"
                            />
                            <div class="min-w-0">
                                <p
                                    class="text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                                >
                                    Email
                                </p>
                                <p class="mt-0.5 text-sm font-medium break-all">
                                    {{ manager.email }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 px-6 py-4">
                            <Fingerprint
                                class="mt-0.5 h-4 w-4 shrink-0 text-muted-foreground"
                            />
                            <div>
                                <p
                                    class="text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                                >
                                    National ID
                                </p>
                                <p
                                    class="mt-0.5 font-mono text-sm font-medium tracking-wide"
                                >
                                    {{ manager.national_id }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 px-6 py-4">
                            <Hash
                                class="mt-0.5 h-4 w-4 shrink-0 text-muted-foreground"
                            />
                            <div>
                                <p
                                    class="text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                                >
                                    System ID
                                </p>
                                <p class="mt-0.5 font-mono text-sm font-medium">
                                    #{{ manager.id }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 px-6 py-4">
                            <Calendar
                                class="mt-0.5 h-4 w-4 shrink-0 text-muted-foreground"
                            />
                            <div>
                                <p
                                    class="text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                                >
                                    Joined
                                </p>
                                <p class="mt-0.5 text-sm font-medium">
                                    {{
                                        new Date(
                                            manager.created_at,
                                        ).toLocaleDateString('en-US', {
                                            dateStyle: 'medium',
                                        })
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Main -->
                <main class="flex-1 px-6 py-8 lg:px-10">
                    <!-- Deleted notice -->
                    <div
                        v-if="manager.deleted_at"
                        class="mb-8 flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 px-5 py-4 dark:border-red-900 dark:bg-red-950/40"
                    >
                        <Trash2
                            class="mt-0.5 h-4 w-4 shrink-0 text-red-600 dark:text-red-400"
                        />
                        <div>
                            <p
                                class="text-sm font-semibold text-red-700 dark:text-red-400"
                            >
                                Account deleted
                            </p>
                            <p
                                class="mt-0.5 text-sm text-red-600/80 dark:text-red-500"
                            >
                                This account was soft-deleted on
                                {{
                                    new Date(manager.deleted_at).toLocaleString(
                                        'en-US',
                                        {
                                            dateStyle: 'long',
                                            timeStyle: 'short',
                                        },
                                    )
                                }}. The record is retained but the manager can
                                no longer access the platform.
                            </p>
                        </div>
                    </div>

                    <!-- Account Details -->
                    <section>
                        <h2
                            class="mb-4 text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                        >
                            Account Details
                        </h2>

                        <div class="overflow-hidden rounded-xl border bg-card">
                            <div
                                class="grid grid-cols-1 divide-y sm:grid-cols-2 sm:divide-x sm:divide-y-0"
                            >
                                <div class="px-6 py-5">
                                    <p
                                        class="text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                                    >
                                        Full Name
                                    </p>
                                    <p class="mt-1.5 text-base font-semibold">
                                        {{ manager.name }}
                                    </p>
                                </div>
                                <div class="px-6 py-5">
                                    <p
                                        class="text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                                    >
                                        Email Address
                                    </p>
                                    <p
                                        class="mt-1.5 text-base font-semibold break-all"
                                    >
                                        {{ manager.email }}
                                    </p>
                                </div>
                            </div>

                            <Separator />

                            <div
                                class="grid grid-cols-1 divide-y sm:grid-cols-2 sm:divide-x sm:divide-y-0"
                            >
                                <div class="px-6 py-5">
                                    <p
                                        class="text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                                    >
                                        National ID
                                    </p>
                                    <p
                                        class="mt-1.5 font-mono text-base font-semibold tracking-wide"
                                    >
                                        {{ manager.national_id }}
                                    </p>
                                </div>
                                <div class="px-6 py-5">
                                    <p
                                        class="text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                                    >
                                        System ID
                                    </p>
                                    <p
                                        class="mt-1.5 font-mono text-base font-semibold"
                                    >
                                        #{{ manager.id }}
                                    </p>
                                </div>
                            </div>

                            <Separator />

                            <div
                                class="grid grid-cols-1 divide-y sm:grid-cols-2 sm:divide-x sm:divide-y-0"
                            >
                                <div class="px-6 py-5">
                                    <p
                                        class="text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                                    >
                                        Account Created
                                    </p>
                                    <p class="mt-1.5 text-base font-semibold">
                                        {{
                                            new Date(
                                                manager.created_at,
                                            ).toLocaleString('en-US', {
                                                dateStyle: 'medium',
                                                timeStyle: 'short',
                                            })
                                        }}
                                    </p>
                                </div>
                                <div
                                    v-if="manager.deleted_at"
                                    class="px-6 py-5"
                                >
                                    <p
                                        class="text-[11px] font-semibold tracking-wider text-muted-foreground uppercase"
                                    >
                                        Deleted At
                                    </p>
                                    <p
                                        class="mt-1.5 text-base font-semibold text-red-600 dark:text-red-400"
                                    >
                                        {{
                                            new Date(
                                                manager.deleted_at,
                                            ).toLocaleString('en-US', {
                                                dateStyle: 'medium',
                                                timeStyle: 'short',
                                            })
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>
                </main>
            </div>
        </div>
    </AppLayout>
</template>
