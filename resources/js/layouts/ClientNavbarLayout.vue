<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    Calendar,
    ClipboardList,
    Home,
    LogOut,
    Menu,
    User,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { Toaster } from 'vue-sonner';

import AppContent from '@/components/AppContent.vue';
import AppLogo from '@/components/AppLogo.vue';
import AppShell from '@/components/AppShell.vue';
import ClientFooter from '@/components/ClientFooter.vue';
import ThemeSwitcher from '@/components/ThemeSwitcher.vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from '@/components/ui/sheet';
import Client from '@/routes/client';

const page = usePage();

const avatarUrl = computed(() => {
    const avatar = (page.props.auth as any)?.user?.avatar_image as
        | string
        | null
        | undefined;

    if (!avatar) {
        return null;
    }

    if (
        avatar.startsWith('http') ||
        avatar.startsWith('/') ||
        avatar.startsWith('data:')
    ) {
        return avatar;
    }

    if (avatar.startsWith('storage/')) {
        return `/${avatar}`;
    }

    return `/storage/${avatar}`;
});

const userInitial = computed(() =>
    String((page.props.auth as any)?.user?.name ?? 'U')
        .charAt(0)
        .toUpperCase(),
);
</script>

<template>
    <AppShell variant="header">
        <header
            class="sticky top-0 z-50 w-full border-b border-border bg-background/80 backdrop-blur-md"
        >
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <!-- Logo/Home -->
                    <Link
                        :href="Client.dashboard.url()"
                        class="flex items-center gap-2 transition-opacity hover:opacity-80"
                    >
                        <AppLogo />
                    </Link>

                    <!-- Desktop Navigation Links -->
                    <nav class="hidden items-center gap-8 md:flex">
                        <Link
                            :href="Client.reservations.index.url()"
                            class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
                            :class="{
                                'text-foreground': page.url.startsWith(
                                    Client.reservations.index.url(),
                                ),
                            }"
                        >
                            Browse Rooms
                        </Link>
                        <Link
                            :href="Client.reservations.my.url()"
                            class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
                            :class="{
                                'text-foreground': page.url.startsWith(
                                    Client.reservations.my.url(),
                                ),
                            }"
                        >
                            My Reservations
                        </Link>
                    </nav>

                    <!-- Right Column: Theme + User + Mobile Menu -->
                    <div class="flex items-center gap-2 sm:gap-4">
                        <ThemeSwitcher />

                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="relative overflow-hidden rounded-full transition-colors hover:bg-muted/50"
                                >
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-full bg-primary font-medium text-primary-foreground ring-2 ring-primary/10"
                                    >
                                        <img
                                            v-if="avatarUrl"
                                            :src="avatarUrl"
                                            alt="Profile avatar"
                                            class="h-full w-full object-cover"
                                        />
                                        <span v-else>{{ userInitial }}</span>
                                    </div>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end" class="mt-2 w-56">
                                <div
                                    class="mb-1 border-b border-border/50 px-2 py-2"
                                >
                                    <p class="truncate text-sm font-semibold">
                                        {{ page.props.auth.user.name }}
                                    </p>
                                    <p
                                        class="truncate text-xs text-muted-foreground"
                                    >
                                        {{ page.props.auth.user.email }}
                                    </p>
                                </div>
                                <DropdownMenuItem as-child>
                                    <Link
                                        :href="Client.dashboard.url()"
                                        class="flex cursor-pointer items-center gap-2"
                                    >
                                        <Home class="h-4 w-4" />
                                        Dashboard
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem as-child>
                                    <Link
                                        :href="Client.profile.edit.url()"
                                        class="flex cursor-pointer items-center gap-2"
                                    >
                                        <User class="h-4 w-4" />
                                        Edit Profile
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem as-child>
                                    <Link
                                        :href="Client.logout.url()"
                                        method="post"
                                        as="button"
                                        class="flex w-full cursor-pointer items-center gap-2 text-destructive focus:text-destructive"
                                    >
                                        <LogOut class="h-4 w-4" />
                                        Logout
                                    </Link>
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>

                        <!-- Mobile Menu Button (Sheet) -->
                        <Sheet>
                            <SheetTrigger as-child>
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="md:hidden"
                                >
                                    <Menu class="h-5 w-5" />
                                </Button>
                            </SheetTrigger>
                            <SheetContent
                                side="right"
                                class="w-[300px] p-0 sm:w-[400px]"
                            >
                                <SheetHeader class="border-b p-6 text-left">
                                    <SheetTitle class="flex items-center gap-2">
                                        <AppLogo />
                                    </SheetTitle>
                                </SheetHeader>
                                <div class="flex flex-col py-6">
                                    <Link
                                        :href="Client.dashboard.url()"
                                        class="flex items-center gap-3 px-6 py-4 text-base font-medium transition-colors hover:bg-muted"
                                        :class="{
                                            'bg-muted text-primary':
                                                page.url ===
                                                Client.dashboard.url(),
                                        }"
                                    >
                                        <Home class="h-5 w-5" />
                                        Dashboard
                                    </Link>
                                    <Link
                                        :href="Client.reservations.index.url()"
                                        class="flex items-center gap-3 px-6 py-4 text-base font-medium transition-colors hover:bg-muted"
                                        :class="{
                                            'bg-muted text-primary':
                                                page.url.startsWith(
                                                    Client.reservations.index.url(),
                                                ),
                                        }"
                                    >
                                        <Calendar class="h-5 w-5" />
                                        Browse Rooms
                                    </Link>
                                    <Link
                                        :href="Client.reservations.my.url()"
                                        class="flex items-center gap-3 px-6 py-4 text-base font-medium transition-colors hover:bg-muted"
                                        :class="{
                                            'bg-muted text-primary':
                                                page.url.startsWith(
                                                    Client.reservations.my.url(),
                                                ),
                                        }"
                                    >
                                        <ClipboardList class="h-5 w-5" />
                                        My Reservations
                                    </Link>
                                    <DropdownMenuSeparator class="mx-6 my-2" />
                                    <Link
                                        :href="Client.profile.edit.url()"
                                        class="flex items-center gap-3 px-6 py-4 text-base font-medium transition-colors hover:bg-muted"
                                        :class="{
                                            'bg-muted text-primary':
                                                page.url.startsWith(
                                                    Client.profile.edit.url(),
                                                ),
                                        }"
                                    >
                                        <User class="h-5 w-5" />
                                        Profile Settings
                                    </Link>
                                    <Link
                                        :href="Client.logout.url()"
                                        method="post"
                                        as="button"
                                        class="flex w-full items-center gap-3 px-6 py-4 text-left text-base font-medium text-destructive transition-colors hover:bg-destructive/5"
                                    >
                                        <LogOut class="h-5 w-5" />
                                        Logout
                                    </Link>
                                </div>
                            </SheetContent>
                        </Sheet>
                    </div>
                </div>
            </div>
        </header>

        <AppContent variant="header">
            <slot />
            <ClientFooter />
        </AppContent>
        <Toaster
            theme="system"
            rich-colors
            close-button
            position="top-center"
        />
    </AppShell>
</template>
