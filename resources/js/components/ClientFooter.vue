<script setup lang="ts">
import { Heart } from 'lucide-vue-next';
</script>

<template>
    <footer class="bg-gray-50 dark:bg-gray-950 border-t border-gray-200 dark:border-gray-800 mt-16">
        <div class="mx-auto w-full max-w-7xl space-y-8 px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-2">
                <!-- Brand -->
                <div>
                    <div class="mb-4 flex items-center gap-2">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600">
                            <span class="font-bold text-white">H</span>
                        </div>
                        <span class="text-lg font-bold text-gray-900 dark:text-white sm:text-xl">Hotel System</span>
                    </div>
                    <p class="text-xs text-gray-600 dark:text-gray-400 sm:text-sm">
                        Your trusted hotel management and reservation platform.
                    </p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="mb-4 font-semibold text-gray-900 dark:text-white">Quick Links</h4>
                    <ul class="space-y-2">
                        <li>
                            <a href="/client/dashboard" class="text-xs text-gray-600 transition-colors hover:text-gray-900 dark:text-gray-400 dark:hover:text-white sm:text-sm">
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="/client/reservations" class="text-xs text-gray-600 transition-colors hover:text-gray-900 dark:text-gray-400 dark:hover:text-white sm:text-sm">
                                Browse Rooms
                            </a>
                        </li>
                        <li>
                            <a href="/client/my-reservations" class="text-xs text-gray-600 transition-colors hover:text-gray-900 dark:text-gray-400 dark:hover:text-white sm:text-sm">
                                My Reservations
                            </a>
                        </li>
                    </ul>
                </div>

            </div>

            <!-- Divider -->
            <div class="border-t border-gray-200 dark:border-gray-800 pt-8">
                <!-- Bottom -->
                <div class="flex flex-col items-center gap-4 sm:flex-row sm:justify-between">
                    <p class="flex items-center gap-1 text-xs text-gray-600 dark:text-gray-400 sm:text-sm">
                        Made with <Heart class="h-3 w-3 text-red-500 sm:h-4 sm:w-4" /> by Hotel Management Team
                    </p>
                    <p class="text-xs text-gray-600 dark:text-gray-400 sm:text-sm">
                        &copy; 2026 Hotel System. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </footer>
</template>

