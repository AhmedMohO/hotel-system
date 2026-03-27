<script setup lang="ts">
import axios from 'axios';
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, Filler } from 'chart.js';
import { ref, onMounted, watch } from 'vue';
import { Line } from 'vue-chartjs';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend, Filler);

const currentYear = ref(new Date().getFullYear());
const chartData = ref({ labels: [], datasets: [] });
const loading = ref(false);
const years = ref<number[]>([]);

const onYearChange = (value: unknown) => {
    if (value === null || value === undefined) {
        return;
    }

    currentYear.value = Number(value);
};

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
            labels: {
                color: '#9ca3af',
            },
        },
    },
    scales: {
        y: {
            beginAtZero: true,
            min: 0,
            ticks: {
                color: '#9ca3af',
            },
            grid: {
                color: 'rgba(156, 163, 175, 0.1)',
            },
        },
        x: {
            ticks: {
                color: '#9ca3af',
            },
            grid: {
                color: 'rgba(156, 163, 175, 0.1)',
            },
        },
    },
};

onMounted(() => {
    const now = new Date().getFullYear();
    years.value = Array.from({ length: 5 }, (_, i) => now - i);
    loadData();
});

const loadData = async () => {
    loading.value = true;

    try {
        const response = await axios.get('/dashboard/api/reservations-revenue-monthly', {
            params: { year: currentYear.value },
        });
        chartData.value = response.data;
    } catch (error) {
        console.error('Error loading monthly revenue data:', error);
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
    <div class="flex flex-col gap-4 rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
        <div class="flex justify-between items-center">
            <h3 class="text-sm font-semibold">Reservations Revenue (Monthly)</h3>
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
            <span class="text-primary-500 text-sm">Loading...</span>
        </div>
        <div v-else class="h-[420px] w-full">
            <Line :data="chartData" :options="chartOptions" class="h-full w-full" />
        </div>
    </div>
</template>
