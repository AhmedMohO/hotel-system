<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import ClientNavbarLayout from '@/layouts/ClientNavbarLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Calendar, KeyRound, Home, ArrowRight } from 'lucide-vue-next';

defineOptions({
    layout: ClientNavbarLayout,
});
</script>

<template>
    <Head title="Client Dashboard" />

    <div class="mx-auto w-full max-w-7xl space-y-8 px-4 py-6 sm:px-6 lg:px-8 lg:py-12">
        <!-- Welcome Section -->
        <div class="rounded-lg bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-950 dark:to-indigo-950 border border-blue-200 dark:border-blue-800 p-6 sm:p-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2 sm:text-3xl">
                Welcome to Your Dashboard
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 sm:text-base">
                Find your perfect room and make a reservation
            </p>
            <a href="/client/reservations">
                <Button class="gap-2">
                    <Home class="w-4 h-4" />
                    Browse Available Rooms
                    <ArrowRight class="w-4 h-4" />
                </Button>
            </a>
        </div>

        <!-- Quick Actions Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Make a Reservation Card -->
            <Card class="hover:shadow-lg transition-shadow">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Calendar class="w-5 h-5 text-blue-600" />
                        New Reservation
                    </CardTitle>
                    <CardDescription>
                        Book a room for your stay
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Search for available rooms and make a new reservation with just a few clicks.
                    </p>
                    <a href="/client/reservations">
                        <Button variant="outline" class="w-full gap-2">
                            <Calendar class="w-4 h-4" />
                            Browse Rooms
                        </Button>
                    </a>
                </CardContent>
            </Card>

            <!-- My Reservations Card -->
            <Card class="hover:shadow-lg transition-shadow">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <KeyRound class="w-5 h-5 text-green-600" />
                        My Reservations
                    </CardTitle>
                    <CardDescription>
                        View your bookings
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Check all your current and past reservations, check-in details, and booking information.
                    </p>
                    <a href="/client/my-reservations">
                        <Button variant="outline" class="w-full gap-2">
                            <KeyRound class="w-4 h-4" />
                            View Reservations
                        </Button>
                    </a>
                </CardContent>
            </Card>

            <!-- Profile Card -->
            <Card class="hover:shadow-lg transition-shadow">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Home class="w-5 h-5 text-purple-600" />
                        Your Profile
                    </CardTitle>
                    <CardDescription>
                        Manage your account
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Update your personal information and account settings.
                    </p>
                    <a href="/client/profile">
                        <Button variant="outline" class="w-full gap-2">
                            <Home class="w-4 h-4" />
                            Edit Profile
                        </Button>
                    </a>
                </CardContent>
            </Card>
        </div>

        <!-- Information Section -->
        <Card>
            <CardHeader>
                <CardTitle>How It Works</CardTitle>
                <CardDescription>Follow these simple steps to make a reservation</CardDescription>
            </CardHeader>
            <CardContent>
                <div class="space-y-4">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm">
                            1
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Search Rooms</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Enter your check-in and check-out dates, and the number of guests to find available rooms.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm">
                            2
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Select a Room</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Choose a room that meets your needs and review the room details and pricing.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm">
                            3
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Complete Payment</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Securely pay for your reservation using our Stripe payment system. Your reservation is confirmed immediately after payment.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm">
                            4
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Check In</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                On your check-in date, arrive at the hotel and present your reservation details at the reception desk.
                            </p>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
