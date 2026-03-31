<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavItem } from '@/types';

defineProps<{
    items: NavItem[];
}>();

const { isCurrentUrl } = useCurrentUrl();
</script>

<template>
    <SidebarGroup class="px-2 py-3">
        <SidebarGroupLabel
            class="mb-2 px-2 text-[10px] font-bold tracking-[0.15em] text-muted-foreground/50 uppercase"
        >
            Menu
        </SidebarGroupLabel>

        <SidebarMenu class="space-y-px">
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton
                    as-child
                    :is-active="isCurrentUrl(item.href)"
                    :tooltip="item.title"
                    class="group relative h-9 w-full rounded-md px-2.5 text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground data-[active=true]:bg-primary/10 data-[active=true]:text-primary"
                >
                    <Link :href="item.href" class="flex items-center gap-2.5">
                        <!-- Left active bar -->
                        <span
                            class="absolute inset-y-1.5 left-0 w-0.5 origin-center rounded-r-full bg-primary transition-transform"
                            :class="
                                isCurrentUrl(item.href)
                                    ? 'scale-y-100 opacity-100'
                                    : 'scale-y-0 opacity-0'
                            "
                        />

                        <component
                            :is="item.icon"
                            class="h-4 w-4 shrink-0"
                            :class="
                                isCurrentUrl(item.href)
                                    ? 'text-primary'
                                    : 'text-muted-foreground group-hover:text-foreground'
                            "
                        />

                        <span
                            class="truncate"
                            :class="
                                isCurrentUrl(item.href)
                                    ? 'font-semibold text-primary'
                                    : 'text-muted-foreground group-hover:text-foreground'
                            "
                        >
                            {{ item.title }}
                        </span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
