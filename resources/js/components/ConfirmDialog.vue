<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';

const props = defineProps<{
    url: string;
    method?: 'delete' | 'patch' | 'post';
    title?: string;
    description?: string;
    triggerLabel?: string;
    triggerIcon?: any;
    triggerTooltip?: string;
    triggerVariant?:
        | 'destructive'
        | 'outline'
        | 'secondary'
        | 'ghost'
        | 'default';
    confirmLabel?: string;
    confirmVariant?: 'destructive' | 'default';
}>();

const emit = defineEmits<{
    success: [];
}>();

const open = ref(false);
const processing = ref(false);

async function confirm() {
    processing.value = true;
    router.visit(props.url, {
        method: props.method ?? 'delete',
        preserveState: false,
        onSuccess: () => {
            emit('success');
            open.value = false;
        },
        onFinish: () => {
            processing.value = false;
        },
    });
}
</script>

<template>
    <AlertDialog v-model:open="open">
        <slot name="trigger">
            <template v-if="triggerIcon && triggerTooltip">
                <TooltipProvider>
                    <Tooltip>
                        <TooltipTrigger as-child>
                            <AlertDialogTrigger as-child>
                                <Button :variant="triggerVariant ?? 'ghost'" size="icon" class="h-8 w-8">
                                    <component :is="triggerIcon" class="h-4 w-4" />
                                </Button>
                            </AlertDialogTrigger>
                        </TooltipTrigger>
                        <TooltipContent>
                            <p>{{ triggerTooltip }}</p>
                        </TooltipContent>
                    </Tooltip>
                </TooltipProvider>
            </template>
            <template v-else>
                <AlertDialogTrigger as-child>
                    <Button :variant="triggerVariant ?? 'destructive'" size="sm">
                        {{ triggerLabel ?? 'Delete' }}
                    </Button>
                </AlertDialogTrigger>
            </template>
        </slot>
        <AlertDialogContent @interact-outside="open = false" @escape-key-down="open = false">
            <AlertDialogHeader>
                <AlertDialogTitle>{{
                    title ?? 'Are you absolutely sure?'
                }}</AlertDialogTitle>
                <AlertDialogDescription>
                    {{ description ?? 'This action cannot be undone.' }}
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel :disabled="processing"
                    >Cancel</AlertDialogCancel
                >
                <AlertDialogAction
                    :disabled="processing"
                    :class="
                        confirmVariant === 'destructive' || !confirmVariant
                            ? 'bg-destructive text-destructive-foreground hover:bg-destructive/90'
                            : ''
                    "
                    @click.prevent="confirm"
                >
                    <Spinner v-if="processing" class="mr-2 h-4 w-4" />
                    {{ confirmLabel ?? 'Confirm' }}
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
