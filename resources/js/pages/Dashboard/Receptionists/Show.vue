<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ShieldAlert,
    Mail,
    Fingerprint,
    Calendar,
} from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
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
import * as Receptionists from '@/routes/receptionists';

defineProps<{
    receptionist: {
        id: number;
        name: string;
        email: string;
        national_id: string;
        avatar_image: string | null;
        created_at: string;
        created_by: { id: number; name: string } | null;
        banned_at: string | null;
    };
}>();

// const page = usePage<{ auth: { user: { roles: string[] } } }>();
// const isAdmin = computed(() => page.props.auth.user?.roles?.includes('admin'));
</script>

<template>
    <Head :title="`Receptionist: ${receptionist.name}`" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Receptionists', href: Receptionists.index.url() },
            { title: receptionist.name, href: '' },
        ]"
    >
        <div
            class="mx-auto min-h-screen max-w-7xl bg-muted/5 px-4 py-10 sm:px-6 lg:px-8"
        >
            <!-- Header -->
            <div
                class="mb-10 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
            >
                <div>
                    <Button
                        variant="ghost"
                        size="sm"
                        class="mb-4 -ml-3 text-base font-semibold text-muted-foreground hover:text-foreground"
                        as-child
                    >
                        <Link :href="Receptionists.index.url()">
                            <ArrowLeft class="mr-2 h-5 w-5" />
                            Back to Receptionists
                        </Link>
                    </Button>
                    <h1
                        class="text-4xl font-extrabold tracking-tight text-foreground"
                    >
                        Receptionist Profile
                    </h1>
                    <p class="mt-2 text-base font-medium text-muted-foreground">
                        Detailed information, creator link, and platform access
                        status.
                    </p>
                </div>

                <Badge
                    v-if="receptionist.banned_at"
                    variant="destructive"
                    class="self-start rounded-none px-4 py-1 text-base font-bold tracking-widest uppercase shadow-none sm:self-auto"
                >
                    Account Suspended
                </Badge>
                <Badge
                    v-else
                    variant="outline"
                    class="self-start rounded-none border-border bg-muted/20 px-4 py-1 text-base font-bold tracking-widest text-muted-foreground uppercase shadow-none sm:self-auto"
                >
                    Active Account
                </Badge>
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
                                :user="receptionist"
                                class="mb-6 h-40 w-40 border-2 border-border shadow-none"
                                :class="{
                                    'opacity-75 grayscale':
                                        receptionist.banned_at,
                                }"
                            />

                            <h2 class="text-2xl font-bold text-foreground">
                                {{ receptionist.name }}
                            </h2>
                            <p
                                class="mt-1 text-base font-semibold text-muted-foreground"
                            >
                                Receptionist
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
                                        receptionist.email
                                    }}</span>
                                </div>
                                <div
                                    class="flex items-center text-base font-semibold text-muted-foreground"
                                >
                                    <Fingerprint
                                        class="mr-3 h-5 w-5 text-foreground/70"
                                    />
                                    <span>{{ receptionist.national_id }}</span>
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
                                                receptionist.created_at,
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
                    <!-- Ban Notice -->
                    <Card
                        v-if="receptionist.banned_at"
                        class="flex items-start gap-4 rounded-xl border-destructive/20 bg-destructive/5 p-6 text-destructive shadow-none"
                    >
                        <ShieldAlert
                            class="mt-1 h-8 w-8 flex-shrink-0 text-destructive"
                        />
                        <div>
                            <h3 class="text-xl font-bold">
                                Platform Access Revoked
                            </h3>
                            <p class="mt-2 text-base font-medium opacity-90">
                                This user's account was administratively banned
                                on
                                {{
                                    new Date(
                                        receptionist.banned_at,
                                    ).toLocaleString()
                                }}. They cannot currently log into the platform.
                            </p>
                        </div>
                    </Card>

                    <Card class="rounded-xl border-border bg-card shadow-none">
                        <CardHeader
                            class="border-b border-border bg-muted/30 px-8 py-6"
                        >
                            <CardTitle
                                class="text-2xl font-bold text-foreground"
                                >Account Information</CardTitle
                            >
                            <CardDescription
                                class="mt-2 text-base font-medium text-muted-foreground"
                            >
                                Complete details of this receptionist's platform
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
                                        {{ receptionist.name }}
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
                                        {{ receptionist.email }}
                                    </dd>
                                </div>

                                <div class="sm:col-span-2">
                                    <Separator class="my-2" />
                                </div>

                                <div>
                                    <dt
                                        class="text-sm font-bold tracking-widest text-muted-foreground uppercase"
                                    >
                                        Created By
                                    </dt>
                                    <dd
                                        class="mt-2 flex items-center gap-2 text-xl font-bold text-foreground"
                                    >
                                        <span
                                            v-if="!receptionist.created_by"
                                            class="text-muted-foreground"
                                            >System Admin</span
                                        >
                                        <template v-else>
                                            <Button
                                                variant="link"
                                                class="h-auto p-0 text-xl font-bold text-primary hover:text-primary/80"
                                                as-child
                                            >
                                                <Link
                                                    :href="
                                                        Managers.show.url(
                                                            receptionist
                                                                .created_by.id,
                                                        )
                                                    "
                                                >
                                                    {{
                                                        receptionist.created_by
                                                            .name
                                                    }}
                                                </Link>
                                            </Button>
                                        </template>
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
                                                receptionist.created_at,
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
                                        National ID
                                    </dt>
                                    <dd
                                        class="mt-2 text-xl font-bold text-foreground"
                                    >
                                        {{ receptionist.national_id }}
                                    </dd>
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
                                        #{{ receptionist.id }}
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
