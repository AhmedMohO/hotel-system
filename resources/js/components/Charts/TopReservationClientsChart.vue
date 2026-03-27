<script setup lang="ts">
import axios from 'axios';
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';
import { ref, onMounted, watch, computed } from 'vue';
import { Pie } from 'vue-chartjs';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

ChartJS.register(ArcElement, Tooltip, Legend);

const currentYear = ref(new Date().getFullYear());
const chartData = ref<any>({ labels: [], datasets: [] });
const loading = ref(false);
const years = ref<number[]>([]);

const onYearChange = (value: unknown) => {
    if (value === null || value === undefined) {
        return;
    }

    currentYear.value = Number(value);
};

const hasData = computed(() => chartData.value.datasets[0]?.data?.some((d: number) => d > 0) ?? false);

const legendItems = computed(() => {
    const labels = (chartData.value.labels ?? []) as string[];
    const dataset = (chartData.value.datasets?.[0] ?? {}) as any;
    const values = (dataset.data ?? []) as number[];
    const colors = Array.isArray(dataset.backgroundColor) ? dataset.backgroundColor : [];

    return labels.map((label, index) => ({
        name: label,
        value: values[index] ?? 0,
        color: colors[index] ?? '#64748b',
    }));
});

onMounted(() => {
    const now = new Date().getFullYear();
    years.value = Array.from({ length: 5 }, (_, i) => now - i);
    loadData();
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false,
        },
        tooltip: {
            callbacks: {
                label: (context: any) => {
                    const value = context.parsed as number;
                    const total = context.dataset.data.reduce((a: number, b: number) => a + b, 0);
                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : '0.0';

                    return ` ${context.label}: ${value} (${percentage}%)`;
                },
            },
        },
    },
};

const loadData = async () => {
    loading.value = true;

    try {
        const response = await axios.get('/dashboard/api/top-reservation-clients', {
            params: { year: currentYear.value },
        });
        chartData.value = response.data;
    } catch (error) {
        console.error('Error loading top clients data:', error);
    } finally {
        loading.value = false;
    }
};

watch(
    () => currentYear.value,
    () => {
        loadData();
    },
);
</script>

<template>
    <div class="flex w-full min-w-[285px] flex-col gap-4 rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
        <div class="flex justify-between items-center">
            <h3 class="text-sm font-semibold">Top Reservations Clients</h3>
            <Select :model-value="String(currentYear)" @update:model-value="onYearChange">
                <SelectTrigger class="h-8 w-[96px] text-xs">
                    <SelectValue />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem v-for="year in years" :key="year" :value="String(year)">{{ year }}</SelectItem>
                </SelectContent>
            </Select>
        </div>
        <div v-if="loading" class="flex items-center justify-center h-64">
            <span class="text-gray-500 text-sm">Loading...</span>
        </div>
        <div v-else-if="!hasData" class="flex items-center justify-center h-64">
            <span class="text-gray-500 text-sm">No data available</span>
        </div>
        <div v-else class="space-y-4">
            <div class="h-72 min-h-[200px] min-w-[200px] w-full">
                <Pie :data="chartData" :options="chartOptions" class="h-full w-full" />
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div v-for="item in legendItems" :key="item.name" class="flex items-center justify-between gap-3 px-2">
                    <div class="flex items-center gap-2 min-w-0 flex-shrink">
                        <span class="h-2.5 w-2.5 rounded-sm flex-shrink-0" :style="{ backgroundColor: item.color }"></span>
                        <span class="text-gray-300 truncate">{{ item.name }}</span>
                    </div>
                    <span class="font-semibold text-gray-100 flex-shrink-0">{{ item.value }}</span>
                </div>
            </div>
        </div>
    </div>
</template>
