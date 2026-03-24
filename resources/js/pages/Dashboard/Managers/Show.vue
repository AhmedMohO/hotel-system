<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, Calendar, Mail, Fingerprint } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    CardDescription,
} from '@/components/ui/card';
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
    };
}>();
</script>

<template>
    <Head :title="`Manager: ${manager.name}`" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Managers', href: Managers.index.url() },
            { title: manager.name, href: '' },
        ]"
    >
        <div
            class="mx-auto min-h-screen max-w-7xl bg-muted/5 px-4 py-10 sm:px-6 lg:px-8"
        >
            <!-- Header -->
            <div
                class="mb-10 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <Button
                        variant="ghost"
                        size="sm"
                        class="mb-4 -ml-3 text-base font-semibold text-muted-foreground hover:text-foreground"
                        as-child
                    >
                        <Link :href="Managers.index.url()">
                            <ArrowLeft class="mr-2 h-5 w-5" />
                            Back to Managers
                        </Link>
                    </Button>
                    <h1
                        class="text-4xl font-extrabold tracking-tight text-foreground"
                    >
                        Manager Profile
                    </h1>
                    <p class="mt-2 text-base font-medium text-muted-foreground">
                        Detailed information and account status.
                    </p>
                </div>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 gap-10 lg:grid-cols-4">
                <!-- Left Column: Profile Card -->
                <div class="lg:col-span-1">
                    <Card class="rounded-xl border-border bg-card shadow-none">
                        <CardContent
                            class="flex flex-col items-center p-8 text-center"
                        >
                            <UserAvatar
                                :user="manager"
                                class="mb-6 h-40 w-40 border-2 border-border shadow-none"
                            />

                            <h2 class="text-2xl font-bold text-foreground">
                                {{ manager.name }}
                            </h2>
                            <p
                                class="mt-1 text-base font-semibold text-muted-foreground"
                            >
                                Admin Manager
                            </p>

                            <Separator class="my-8 w-full" />

                            <div class="w-full space-y-5 text-left">
                                <div
                                    class="flex items-center text-base font-semibold text-muted-foreground"
                                >
                                    <Mail
                                        class="mr-3 h-5 w-5 text-foreground/70"
                                    />
                                    <span class="truncate">{{
                                        manager.email
                                    }}</span>
                                </div>
                                <div
                                    class="flex items-center text-base font-semibold text-muted-foreground"
                                >
                                    <Fingerprint
                                        class="mr-3 h-5 w-5 text-foreground/70"
                                    />
                                    <span>{{ manager.national_id }}</span>
                                </div>
                                <div
                                    class="flex items-center text-base font-semibold text-muted-foreground"
                                >
                                    <Calendar
                                        class="mr-3 h-5 w-5 text-foreground/70"
                                    />
                                    <span
                                        >Joined
                                        {{
                                            new Date(
                                                manager.created_at,
                                            ).toLocaleDateString()
                                        }}</span
                                    >
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Right Column: Details -->
                <div class="space-y-10 lg:col-span-3">
                    <Card class="rounded-xl border-border bg-card shadow-none">
                        <CardHeader
                            class="border-b border-border bg-muted/30 px-8 py-6"
                        >
                            <CardTitle
                                class="text-2xl font-bold text-foreground"
                                >Personal Information</CardTitle
                            >
                            <CardDescription
                                class="mt-2 text-base font-medium text-muted-foreground"
                            >
                                Complete details of this manager's platform
                                identity.
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="p-8">
                            <dl
                                class="grid grid-cols-1 gap-x-8 gap-y-10 sm:grid-cols-2"
                            >
                                <div>
                                    <dt
                                        class="text-sm font-bold tracking-widest text-muted-foreground uppercase"
                                    >
                                        Full Name
                                    </dt>
                                    <dd
                                        class="mt-2 text-xl font-bold text-foreground"
                                    >
                                        {{ manager.name }}
                                    </dd>
                                </div>
                                <div>
                                    <dt
                                        class="text-sm font-bold tracking-widest text-muted-foreground uppercase"
                                    >
                                        Email Address
                                    </dt>
                                    <dd
                                        class="mt-2 text-xl font-bold text-foreground"
                                    >
                                        {{ manager.email }}
                                    </dd>
                                </div>

                                <div class="sm:col-span-2">
                                    <Separator class="my-2" />
                                </div>

                                <div>
                                    <dt
                                        class="text-sm font-bold tracking-widest text-muted-foreground uppercase"
                                    >
                                        National ID
                                    </dt>
                                    <dd
                                        class="mt-2 text-xl font-bold text-foreground"
                                    >
                                        {{ manager.national_id }}
                                    </dd>
                                </div>
                                <div>
                                    <dt
                                        class="text-sm font-bold tracking-widest text-muted-foreground uppercase"
                                    >
                                        Account Created
                                    </dt>
                                    <dd
                                        class="mt-2 text-xl font-bold text-foreground"
                                    >
                                        {{
                                            new Date(
                                                manager.created_at,
                                            ).toLocaleString()
                                        }}
                                    </dd>
                                </div>

                                <div class="sm:col-span-2">
                                    <Separator class="my-2" />
                                </div>

                                <div>
                                    <dt
                                        class="text-sm font-bold tracking-widest text-muted-foreground uppercase"
                                    >
                                        System ID
                                    </dt>
                                    <dd
                                        class="mt-2 text-xl font-bold text-foreground"
                                    >
                                        #{{ manager.id }}
                                    </dd>
                                </div>
                            </dl>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
