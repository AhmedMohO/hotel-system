<script setup lang="ts">
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    icon: any;
    tooltip: string;
    variant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost' | 'link';
    href?: string;
}>();

const emit = defineEmits<{
    (e: 'click', event: MouseEvent): void;
}>();
</script>

<template>
    <TooltipProvider>
        <Tooltip>
            <TooltipTrigger as-child>
                <Link v-if="href" :href="href">
                    <Button
                        size="icon"
                        :variant="variant || 'ghost'"
                        class="h-8 w-8 text-muted-foreground hover:text-foreground"
                        @click="$emit('click', $event)"
                    >
                        <component :is="icon" class="h-4 w-4" />
                        <span class="sr-only">{{ tooltip }}</span>
                    </Button>
                </Link>
                <Button
                    v-else
                    size="icon"
                    :variant="variant || 'ghost'"
                    class="h-8 w-8 text-muted-foreground hover:text-foreground"
                    @click="$emit('click', $event)"
                >
                    <component :is="icon" class="h-4 w-4" />
                    <span class="sr-only">{{ tooltip }}</span>
                </Button>
            </TooltipTrigger>
            <TooltipContent>
                <p>{{ tooltip }}</p>
            </TooltipContent>
        </Tooltip>
    </TooltipProvider>
</template>
