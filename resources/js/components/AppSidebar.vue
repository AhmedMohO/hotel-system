<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    LayoutDashboard,
    User,
    UserCog,
    UserRound,
    ShieldCheck,
    Building2,
    BedDouble,
    ClipboardList,
} from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarSeparator,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import * as Floors from '@/routes/floors';
import * as Managers from '@/routes/managers';
import * as Receptionists from '@/routes/receptionists';
import * as Rooms from '@/routes/rooms';
import type { NavItem } from '@/types';

const page = usePage<{ auth: { user: { roles: string[] } } }>();
const roles = computed(() => page.props.auth.user?.roles ?? []);
const isAdmin = computed(() => roles.value.includes('admin'));
const isManager = computed(() => roles.value.includes('manager'));
const isReceptionist = computed(() => roles.value.includes('receptionist'));

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [];

    if (!isReceptionist.value) {
        items.push({
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutDashboard,
        });
    }

    if (isAdmin.value) {
        items.push({
            title: 'Manage Managers',
            href: Managers.index.url(),
            icon: ShieldCheck,
        });
    }

    if (isAdmin.value || isManager.value) {
        items.push({
            title: 'Manage Receptionists',
            href: Receptionists.index.url(),
            icon: UserCog,
        });

        items.push({
            title: 'Manage Floors',
            href: Floors.index.url(),
            icon: Building2,
        });

        items.push({
            title: 'Manage Rooms',
            href: Rooms.index.url(),
            icon: BedDouble,
        });
    }

    items.push(
        {
            title: 'Manage Clients',
            href: isReceptionist.value
                ? '/dashboard/receptionist/clients'
                : '/dashboard/clients',
            icon: UserRound,
        },
        {
            title: 'Clients Reservations',
            href: '/dashboard/clients-reservations',
            icon: ClipboardList,
        },
    );

    return items;
});

const footerNavItems: NavItem[] = [
    { title: 'My Profile', href: '/dashboard/profile', icon: User },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <!-- Header: Logo -->
        <SidebarHeader class="border-b border-border/60 px-3 py-3">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton
                        size="lg"
                        as-child
                        class="rounded-md hover:bg-accent"
                    >
                        <Link
                            :href="
                                isReceptionist
                                    ? '/dashboard/receptionist/clients'
                                    : dashboard()
                            "
                            class="flex items-center gap-2.5"
                        >
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <!-- Main Navigation -->
        <SidebarContent class="overflow-x-hidden">
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <!-- Footer: Profile + User -->
        <SidebarFooter class="border-t border-border/60">
            <NavFooter :items="footerNavItems" />
            <SidebarSeparator class="opacity-50" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>

    <slot />
</template>
