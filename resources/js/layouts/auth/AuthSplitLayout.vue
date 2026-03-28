<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Toaster } from 'vue-sonner';
import AppLogo from '@/components/AppLogo.vue';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import ThemeSwitcher from '@/components/ThemeSwitcher.vue';
import { useAppearance } from '@/composables/useAppearance';
import { home } from '@/routes';

const { appearance } = useAppearance();

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="relative grid min-h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0"
    >
        <div class="absolute top-4 right-4 z-50 lg:top-8 lg:right-8">
            <ThemeSwitcher />
        </div>
        <div class="lg:p-8">
            <div
                class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[450px]"
            >
                <div class="flex flex-col space-y-2 text-center">
                    <h1
                        class="text-2xl font-semibold tracking-tight"
                        v-if="title"
                    >
                        {{ title }}
                    </h1>
                    <p class="text-sm text-muted-foreground" v-if="description">
                        {{ description }}
                    </p>
                </div>
                <slot />
            </div>
        </div>
        <div
            class="relative hidden h-full flex-col bg-muted p-10 text-white lg:flex dark:border-l"
        >
            <div class="absolute inset-0 bg-zinc-950" />
            <img
                src="/images/auth-bg.png"
                alt="Haven Luxury Hotel"
                class="absolute inset-0 size-full object-cover opacity-60 grayscale-[20%]"
            />
            <div
                class="absolute inset-0 bg-gradient-to-t from-zinc-950/80 via-transparent to-zinc-950/40"
            />
            <PlaceholderPattern
                class="absolute inset-0 size-full stroke-white/5"
            />
            <Link
                :href="home()"
                class="relative z-20 flex items-center text-lg font-medium"
            >
                <AppLogo />
            </Link>

            <div class="relative z-20 mt-auto">
                <blockquote class="space-y-2">
                    <p class="text-lg leading-relaxed font-light italic">
                        &ldquo;Experience luxury like never before. Our hotel
                        system ensures a seamless stay from the moment you log
                        in.&rdquo;
                    </p>
                    <footer class="text-sm font-medium opacity-80">
                        &mdash; Haven Executive
                    </footer>
                </blockquote>
            </div>
        </div>
        <Toaster :theme="appearance" rich-colors close-button position="top-center" />
    </div>
</template>
