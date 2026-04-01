<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import ClientNavbarLayout from '@/layouts/ClientNavbarLayout.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Calendar, ClipboardList, UserCircle, ArrowRight, CheckCircle2 } from 'lucide-vue-next';
import { computed } from 'vue';

defineOptions({ layout: ClientNavbarLayout });

const props = defineProps<{
    totalReservations?: number;
    upcomingReservations?: number;
    nextCheckIn?: string | null;
    memberSince?: string | null;
}>();

const page = usePage();
const user = computed(() => (page.props.auth as any)?.user);

const steps = [
    {
        number: 1,
        title: 'Search rooms',
        description: 'Enter your check-in, check-out dates and number of guests.',
    },
    {
        number: 2,
        title: 'Select a room',
        description: 'Review room details, amenities, and pricing before choosing.',
    },
    {
        number: 3,
        title: 'Complete payment',
        description: 'Pay securely via Stripe. Your booking is confirmed instantly.',
    },
    {
        number: 4,
        title: 'Check in',
        description: 'Arrive on your date and show your booking at reception.',
    },
];
</script>

<template>
    <Head title="Client Dashboard" />

    <div class="mx-auto w-full max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">

        <!-- Hero -->
        <div class="rounded-lg border border-border bg-muted/40 p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-foreground mb-1">
                    Welcome back{{ user?.name ? ', ' + user.name : '' }}
                </h1>
                <p class="text-sm text-muted-foreground mb-4">
                    Manage your bookings and explore available rooms.
                </p>
                <span class="inline-flex items-center gap-2 text-xs text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-950/50 border border-green-200 dark:border-green-800 rounded-md px-3 py-1">
                    <CheckCircle2 class="w-3.5 h-3.5" />
                    Account active
                </span>
            </div>
            <a href="/client/reservations" class="shrink-0">
                <Button class="gap-2">
                    <Calendar class="w-4 h-4" />
                    Browse rooms
                    <ArrowRight class="w-4 h-4" />
                </Button>
            </a>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="rounded-lg bg-muted/40 border border-border p-4">
                <p class="text-xs text-muted-foreground mb-1">Total reservations</p>
                <p class="text-2xl font-semibold text-foreground">{{ totalReservations ?? 0 }}</p>
                <p class="text-xs text-muted-foreground mt-1">All time</p>
            </div>
            <div class="rounded-lg bg-muted/40 border border-border p-4">
                <p class="text-xs text-muted-foreground mb-1">Upcoming stays</p>
                <p class="text-2xl font-semibold text-foreground">{{ upcomingReservations ?? 0 }}</p>
                <p class="text-xs text-muted-foreground mt-1">{{ nextCheckIn ? 'Next: ' + nextCheckIn : 'None scheduled' }}</p>
            </div>
            <div class="rounded-lg bg-muted/40 border border-border p-4">
                <p class="text-xs text-muted-foreground mb-1">Member since</p>
                <p class="text-base font-semibold text-foreground mt-1">{{ memberSince ?? '—' }}</p>
            </div>
            <div class="rounded-lg bg-muted/40 border border-border p-4">
                <p class="text-xs text-muted-foreground mb-1">Account status</p>
                <p class="text-base font-semibold text-green-600 dark:text-green-400 mt-1">Active</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <Card class="flex flex-col">
                <CardHeader class="pb-3">
                    <div class="w-9 h-9 rounded-md bg-blue-50 dark:bg-blue-950/50 flex items-center justify-center mb-3">
                        <Calendar class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                    </div>
                    <CardTitle class="text-base">New reservation</CardTitle>
                    <CardDescription>Book a room for your stay</CardDescription>
                </CardHeader>
                <CardContent class="flex flex-col flex-1 justify-between gap-4">
                    <p class="text-sm text-muted-foreground">
                        Search available rooms and book your next stay in a few steps.
                    </p>
                    <a href="/client/reservations">
                        <Button variant="outline" class="w-full gap-2">
                            <Calendar class="w-4 h-4" />
                            Browse rooms
                        </Button>
                    </a>
                </CardContent>
            </Card>

            <Card class="flex flex-col">
                <CardHeader class="pb-3">
                    <div class="w-9 h-9 rounded-md bg-green-50 dark:bg-green-950/50 flex items-center justify-center mb-3">
                        <ClipboardList class="w-4 h-4 text-green-600 dark:text-green-400" />
                    </div>
                    <CardTitle class="text-base">My reservations</CardTitle>
                    <CardDescription>View your bookings</CardDescription>
                </CardHeader>
                <CardContent class="flex flex-col flex-1 justify-between gap-4">
                    <p class="text-sm text-muted-foreground">
                        Check current and past reservations, check-in dates, and room details.
                    </p>
                    <a href="/client/my-reservations">
                        <Button variant="outline" class="w-full gap-2">
                            <ClipboardList class="w-4 h-4" />
                            View bookings
                        </Button>
                    </a>
                </CardContent>
            </Card>

            <Card class="flex flex-col">
                <CardHeader class="pb-3">
                    <div class="w-9 h-9 rounded-md bg-purple-50 dark:bg-purple-950/50 flex items-center justify-center mb-3">
                        <UserCircle class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                    </div>
                    <CardTitle class="text-base">Your profile</CardTitle>
                    <CardDescription>Manage your account</CardDescription>
                </CardHeader>
                <CardContent class="flex flex-col flex-1 justify-between gap-4">
                    <p class="text-sm text-muted-foreground">
                        Update your personal information and account settings.
                    </p>
                    <a href="/client/profile">
                        <Button variant="outline" class="w-full gap-2">
                            <UserCircle class="w-4 h-4" />
                            Edit profile
                        </Button>
                    </a>
                </CardContent>
            </Card>
        </div>

        <!-- How it works -->
        <Card>
            <CardHeader>
                <CardTitle>How it works</CardTitle>
                <CardDescription>Four simple steps to complete your stay</CardDescription>
            </CardHeader>
            <CardContent>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="step in steps" :key="step.number" class="flex flex-col gap-3">
                        <div class="w-7 h-7 rounded-full bg-foreground text-background flex items-center justify-center text-xs font-medium shrink-0">
                            {{ step.number }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-foreground mb-1">{{ step.title }}</p>
                            <p class="text-sm text-muted-foreground leading-relaxed">{{ step.description }}</p>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>

    </div>
</template>