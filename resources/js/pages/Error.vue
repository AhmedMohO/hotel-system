<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    AlertTriangle,
    ShieldAlert,
    RefreshCw,
    Home,
    SearchX,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';

const props = defineProps<{
    status: number;
}>();

const reload = () => {
    window.location.reload();
};

const title = computed(() => {
    return (
        {
            503: 'SERVICE UNAVAILABLE',
            500: 'SERVER ERROR',
            404: 'PAGE NOT FOUND',
            403: 'ACCESS FORBIDDEN',
        }[props.status] || 'ERROR OCCURRED'
    );
});

const subtitle = computed(() => {
    return (
        {
            503: 'Our system is resting. Please check back later.',
            500: 'Something went wrong in our back office.',
            404: "We couldn't find the page you were looking for.",
            403: "You're not authorized to access this page.",
        }[props.status] || 'An unexpected error has occurred.'
    );
});

const icon = computed(() => {
    return (
        {
            503: RefreshCw,
            500: AlertTriangle,
            404: SearchX,
            403: ShieldAlert,
        }[props.status] || AlertTriangle
    );
});
</script>

<template>
    <Head :title="title" />

    <div
        class="flex min-h-screen flex-col items-center justify-center bg-background font-sans selection:bg-primary/20"
    >
        <!-- Error Content -->
        <main
            class="relative z-10 flex w-full max-w-2xl flex-col items-center px-6 text-center"
        >
            <!-- Icon with elegant aura -->
            <div
                class="relative mb-12 flex h-24 w-24 items-center justify-center rounded-full border border-border bg-card sm:h-32 sm:w-32"
            >
                <div
                    class="absolute inset-0 rounded-full bg-primary/5 blur-3xl"
                />
                <component
                    :is="icon"
                    class="relative size-12 text-primary sm:size-16"
                    stroke-width="1.5"
                />
            </div>

            <!-- Status Code -->
            <div class="mb-4">
                <span
                    class="block text-6xl font-black tracking-tighter text-primary sm:text-8xl"
                >
                    {{ status }}
                </span>
            </div>

            <!-- Labels -->
            <div class="mb-10 space-y-3">
                <h1
                    class="text-xs font-bold tracking-[0.4em] text-muted-foreground uppercase opacity-80"
                >
                    {{ title }}
                </h1>
                <h2
                    class="text-lg font-semibold tracking-tight text-foreground sm:text-xl"
                >
                    {{ subtitle }}
                </h2>
            </div>

            <!-- Actions -->
            <div
                class="flex w-full max-w-sm flex-col items-center gap-6 sm:flex-row"
            >
                <Button
                    as-child
                    class="h-12 w-full gap-2 rounded-xl bg-primary text-primary-foreground shadow-lg shadow-primary/20 transition-all hover:translate-y-[-2px] hover:shadow-xl sm:flex-1"
                >
                    <Link href="/">
                        <Home class="size-5" />
                        <span class="font-bold tracking-wide">RETURN HOME</span>
                    </Link>
                </Button>

                <Button
                    variant="outline"
                    @click="reload"
                    class="h-12 w-full gap-2 rounded-xl border-border bg-card font-bold tracking-wide transition-all hover:bg-muted/50 sm:flex-1"
                >
                    <RefreshCw class="size-5" />
                    TRY AGAIN
                </Button>
            </div>
        </main>

        <!-- Footer / Signature -->
        <div class="absolute bottom-12 left-0 w-full text-center">
            <p
                class="text-[10px] font-bold tracking-[0.3em] text-muted-foreground uppercase opacity-40"
            >
                Managed by Hotel Management System &copy;
                {{ new Date().getFullYear() }}
            </p>
        </div>

        <!-- Luxury pattern overlay -->
        <div
            class="pointer-events-none fixed inset-0 z-0 opacity-[0.03] grayscale invert dark:invert-0"
            style="
                background-image: url('https://www.transparenttextures.com/patterns/cubes.png');
            "
        />
    </div>
</template>

<style scoped>
/* Optional: sophisticated entrance animation */
main {
    animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
