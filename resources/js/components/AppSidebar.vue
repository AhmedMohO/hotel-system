<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    BookOpen,
    FolderGit2,
    UserCog,
    UserRound,
    ShieldCheck,
} from 'lucide-vue-next';
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
} from '@/components/ui/sidebar';
import type { NavItem } from '@/types';
import { dashboard } from '@/routes';
import {
    manageClients,
    manageManagers,
    manageReceptionists,
} from '@/routes/dashboard';

const mainNavItems: NavItem[] = [
    {
        title: 'Manage Managers',
        href: manageManagers(),
        icon: ShieldCheck,
    },
    {
        title: 'Manage Receptionists',
        href: manageReceptionists(),
        icon: UserCog,
    },
    {
        title: 'Manage Clients',
        href: manageClients(),
        icon: UserRound,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: FolderGit2,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
