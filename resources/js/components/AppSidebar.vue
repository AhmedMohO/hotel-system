<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    LayoutDashboard,
    ShieldCheck,
    User,
    UserCog,
    UserRound,
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
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import * as Managers from '@/routes/managers';
import * as Receptionists from '@/routes/receptionists';
import type { NavItem } from '@/types';

const page = usePage<{ auth: { user: { roles: string[] } } }>();
const roles = computed(() => page.props.auth.user?.roles ?? []);

const isAdmin = computed(() => roles.value.includes('admin'));
const isManager = computed(() => roles.value.includes('manager'));
const isReceptionist = computed(() => roles.value.includes('receptionist'));

const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [
        { title: 'Dashboard', href: dashboard(), icon: LayoutDashboard },
    ];

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
    }

    // ── ONE "Manage Clients" link for EVERY role ───────────────────────────────
    // Admin/Manager  → /dashboard/clients          (full CRUD)
    // Receptionist   → /dashboard/receptionist/clients  (pending list)
    if (isAdmin.value || isManager.value) {
        items.push({
            title: 'Manage Clients',
            href: '/dashboard/clients',
            icon: UserRound,
        });
    } else if (isReceptionist.value) {
        items.push({
            title: 'Manage Clients',
            href: '/dashboard/receptionist/clients',
            icon: UserRound,
        });
    }

    return items;
});

const footerNavItems: NavItem[] = [
    { title: 'My Profile', href: '/dashboard/profile', icon: User },
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
