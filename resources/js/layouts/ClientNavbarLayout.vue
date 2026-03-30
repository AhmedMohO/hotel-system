<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Toaster } from 'vue-sonner';
import AppContent from '@/components/AppContent.vue';
import AppHeader from '@/components/AppHeader.vue';
import AppShell from '@/components/AppShell.vue';
import ClientFooter from '@/components/ClientFooter.vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { LogOut, User, Home } from 'lucide-vue-next';

const page = usePage();
</script>

<template>
    <AppShell variant="header">
        <header class="bg-white dark:bg-gray-950 border-b border-gray-200 dark:border-gray-800 sticky top-0 z-50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo/Home -->
                    <a href="/client/dashboard" class="flex items-center gap-2">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-600">
                            <Home class="w-6 h-6 text-white" />
                        </div>
                        <span class="text-xl font-bold text-gray-900 dark:text-white">Hotel</span>
                    </a>

                    <!-- Navigation Links -->
                    <nav class="hidden md:flex items-center gap-8">
                        <a href="/client/reservations" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-medium transition-colors">
                            Browse Rooms
                        </a>
                        <a href="/client/my-reservations" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-medium transition-colors">
                            My Reservations
                        </a>
                    </nav>

                    <!-- User Menu -->
                    <div class="flex items-center gap-4">
                        <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                                <Button variant="ghost" size="icon" class="relative">
                                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-medium">
                                        {{ page.props.auth.user.name.charAt(0).toUpperCase() }}
                                    </div>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end" class="w-56">
                                <div class="px-2 py-1.5">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ page.props.auth.user.name }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ page.props.auth.user.email }}
                                    </p>
                                </div>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem as-child>
                                    <a href="/client/dashboard" class="cursor-pointer flex items-center gap-2">
                                        <Home class="w-4 h-4" />
                                        Dashboard
                                    </a>
                                </DropdownMenuItem>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem as-child>
                                    <form method="POST" action="/client/logout" class="w-full">
                                        <input type="hidden" name="_token" :value="page.props.csrf_token" />
                                        <button type="submit" class="w-full text-left flex items-center gap-2 text-red-600 dark:text-red-400">
                                            <LogOut class="w-4 h-4" />
                                            Logout
                                        </button>
                                    </form>
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                </div>
            </div>
        </header>

        <AppContent variant="header">
            <slot />
            <ClientFooter />
            <Toaster theme="system" rich-colors close-button position="top-center" />
        </AppContent>
    </AppShell>
</template>

