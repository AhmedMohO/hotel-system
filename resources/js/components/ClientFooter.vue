<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Heart } from 'lucide-vue-next';

import AppLogo from '@/components/AppLogo.vue';
import Client from '@/routes/client';
</script>

<template>
    <footer
        class="mt-auto rounded-xl border-t border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-950"
    >
        <div
            class="mx-auto w-full max-w-7xl space-y-8 px-4 pt-8 sm:px-6 sm:pt-12 lg:px-8"
        >
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-2">
                <!-- Brand -->
                <div>
                    <div class="mb-4 flex items-center gap-2">
                        <app-logo class="h-8 w-auto" />
                    </div>
                    <p
                        class="text-xs text-gray-600 sm:text-sm dark:text-gray-400"
                    >
                        Your trusted hotel management and reservation platform.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4
                        class="mb-4 font-semibold text-gray-900 dark:text-white"
                    >
                        Quick Links
                    </h4>
                    <ul class="space-y-2">
                        <li>
                            <Link
                                :href="Client.dashboard.url()"
                                class="text-xs text-gray-600 transition-colors hover:text-gray-900 sm:text-sm dark:text-gray-400 dark:hover:text-white"
                            >
                                Dashboard
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="Client.reservations.index.url()"
                                class="text-xs text-gray-600 transition-colors hover:text-gray-900 sm:text-sm dark:text-gray-400 dark:hover:text-white"
                            >
                                Browse Rooms
                            </Link>
                        </li>
                        <li>
                            <Link
                                :href="Client.reservations.my.url()"
                                class="text-xs text-gray-600 transition-colors hover:text-gray-900 sm:text-sm dark:text-gray-400 dark:hover:text-white"
                            >
                                My Reservations
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-200 py-4 dark:border-gray-800">
                <!-- Bottom -->
                <div
                    class="flex flex-col items-center gap-4 sm:flex-row sm:justify-between"
                >
                    <p
                        class="flex items-center gap-1 text-xs text-gray-600 sm:text-sm dark:text-gray-400"
                    >
                        Made with
                        <Heart class="h-3 w-3 text-red-500 sm:h-4 sm:w-4" /> by
                        Hotel Management Team
                    </p>
                    <p
                        class="text-xs text-gray-600 sm:text-sm dark:text-gray-400"
                    >
                        &copy; 2026 Hotel System. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </footer>
</template>
