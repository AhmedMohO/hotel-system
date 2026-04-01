<script setup lang="ts">
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Toaster } from 'vue-sonner';
import AppContent from '@/components/AppContent.vue';
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
import AppLogo from '@/components/AppLogo.vue';

const page = usePage();

const avatarUrl = computed(() => {
    const avatar = (page.props.auth as any)?.user?.avatar_image as string | null | undefined;

    if (!avatar) {
        return null;
    }

    if (avatar.startsWith('http') || avatar.startsWith('/') || avatar.startsWith('data:')) {
        return avatar;
    }

    if (avatar.startsWith('storage/')) {
        return `/${avatar}`;
    }

    return `/storage/${avatar}`;
});

const userInitial = computed(() => String((page.props.auth as any)?.user?.name ?? 'U').charAt(0).toUpperCase());
</script>

<template>
    <AppShell variant="header">
        <header class="bg-white dark:bg-gray-950 border-b border-gray-200 dark:border-gray-800 sticky top-0 z-50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo/Home -->
                   <app-logo class="w-auto h-8" />

                    <!-- Navigation Links -->
                    <nav class="hidden md:flex items-center gap-8">
                        <a href="/client/reservations" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-medium transition-colors">
                            Browse Rooms
                        </a>
                        <a href="/client/my-reservations" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-medium transition-colors">
                            My Reservations
                        </a>
<!--                        <a href="/client/profile" class="text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-medium transition-colors">-->
<!--                            Profile-->
<!--                        </a>-->
                    </nav>

                    <!-- User Menu -->
                    <div class="flex items-center gap-4">
                        <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                                <Button variant="ghost" size="icon" class="relative">
                                    <div class="w-8 h-8 rounded-full overflow-hidden bg-blue-600 flex items-center justify-center text-white font-medium">
                                        <img v-if="avatarUrl" :src="avatarUrl" alt="Profile avatar" class="h-full w-full object-cover" />
                                        <span v-else>{{ userInitial }}</span>
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
                                <DropdownMenuItem as-child>
                                    <a href="/client/profile" class="cursor-pointer flex items-center gap-2">
                                        <User class="w-4 h-4" />
                                        Edit Profile
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

