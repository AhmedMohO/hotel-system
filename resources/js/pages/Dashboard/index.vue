<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { nextTick, onMounted, onUnmounted, ref } from 'vue';
import GenderDistributionChart from '@/components/Charts/GenderDistributionChart.vue';
import ReservationsByCountryChart from '@/components/Charts/ReservationsByCountryChart.vue';
import ReservationsRevenueMonthlyChart from '@/components/Charts/ReservationsRevenueMonthlyChart.vue';
import TopReservationClientsChart from '@/components/Charts/TopReservationClientsChart.vue';
import DashboardStatistics from '@/components/DashboardStatistics.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
    },
];

const chartRenderKey = ref(0);
let resizeTimer: ReturnType<typeof setTimeout> | null = null;

const handleResize = () => {
    if (resizeTimer) {
        clearTimeout(resizeTimer);
    }

    resizeTimer = setTimeout(() => {
        chartRenderKey.value += 1;
    }, 150);
};

onMounted(() => {
    window.addEventListener('resize', handleResize);

    // Trigger one rerender after initial layout settles to avoid first-visit chart sizing glitches.
    nextTick(() => {
        requestAnimationFrame(() => {
            chartRenderKey.value += 1;
        });
    });
});

onUnmounted(() => {
    window.removeEventListener('resize', handleResize);

    if (resizeTimer) {
        clearTimeout(resizeTimer);
    }
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4"
        >
            <!-- Statistics Cards -->
            <DashboardStatistics />

            <!-- Row 1: 3 Pie Charts -->
            <div class="grid gap-4 [grid-template-columns:repeat(auto-fit,minmax(280px,1fr))]">
                <GenderDistributionChart :key="`gender-${chartRenderKey}`" />
                <ReservationsByCountryChart :key="`country-${chartRenderKey}`" />
                <TopReservationClientsChart :key="`clients-${chartRenderKey}`" />
            </div>

            <!-- Row 2: Line Chart (Full Width) -->
            <div
                class="relative min-h-[500px] rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
            >
                <ReservationsRevenueMonthlyChart :key="`revenue-${chartRenderKey}`" />
            </div>
        </div>
    </AppLayout>
</template>
