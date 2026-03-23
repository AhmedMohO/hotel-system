<script setup lang="ts">
import { Monitor, Moon, Sun } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useAppearance } from '@/composables/useAppearance';

const { appearance, updateAppearance } = useAppearance();
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button
                variant="ghost"
                size="icon"
                class="h-9 w-9 cursor-pointer border border-border/50 bg-background/20 shadow-sm backdrop-blur-md transition-all hover:bg-background/40"
            >
                <Sun
                    v-if="appearance === 'light'"
                    class="h-[1.2rem] w-[1.2rem] scale-100 rotate-0 text-foreground transition-all"
                />
                <Moon
                    v-else-if="appearance === 'dark'"
                    class="h-[1.2rem] w-[1.2rem] scale-100 rotate-0 text-white transition-all"
                />
                <Monitor
                    v-else
                    class="h-[1.2rem] w-[1.2rem] scale-100 rotate-0 text-foreground transition-all dark:text-white"
                />
                <span class="sr-only">Toggle theme</span>
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
            <DropdownMenuItem @click="updateAppearance('light')">
                <Sun class="mr-2 h-4 w-4" />
                <span>Light</span>
            </DropdownMenuItem>
            <DropdownMenuItem @click="updateAppearance('dark')">
                <Moon class="mr-2 h-4 w-4" />
                <span>Dark</span>
            </DropdownMenuItem>
            <DropdownMenuItem @click="updateAppearance('system')">
                <Monitor class="mr-2 h-4 w-4" />
                <span>System</span>
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
