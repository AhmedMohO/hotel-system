<script setup lang="ts">
import axios from 'axios';
import { DollarSign } from 'lucide-vue-next';
import { ref, onMounted, computed } from 'vue';

interface Stats {
    totalReservations: number;
    totalRevenue: number;
    activeClients: number;
    roomsOccupied: number;
    totalRooms: number;
}

const stats = ref<Stats>({
    totalReservations: 0,
    totalRevenue: 0,
    activeClients: 0,
    roomsOccupied: 0,
    totalRooms: 0,
});

const totalRevenue = computed(() => Number(stats.value.totalRevenue ?? 0) / 100);

const formatRevenue = (val: number) => {
    if (val >= 1_000_000) {
        return `${(val / 1_000_000).toFixed(1)}M`;
    }

    if (val >= 1_000) {
        return `${(val / 1_000).toFixed(1)}K`;
    }

    return val.toLocaleString('en-US', { maximumFractionDigits: 2 });
};

const fullRevenueLabel = computed(() => `$${totalRevenue.value.toLocaleString('en-US', { maximumFractionDigits: 2 })}`);

onMounted(async () => {
    try {
        const response = await axios.get('/dashboard/api/statistics');
        stats.value = response.data;
    } catch (error) {
        console.error('Error loading statistics:', error);
    }
});
</script>

<template>
    <div class="grid gap-4 md:grid-cols-4 mb-6">
        <!-- Total Reservations -->
        <div class="rounded-lg border border-sidebar-border/70 bg-card p-4 dark:border-sidebar-border">
            <div class="text-sm font-medium text-muted-foreground">Total Reservations</div>
            <div class="mt-2 text-3xl font-bold">{{ stats.totalReservations }}</div>
        </div>

        <!-- Total Revenue -->
        <div class="overflow-hidden rounded-lg border border-sidebar-border/70 bg-card p-4 dark:border-sidebar-border">
            <div class="flex items-center gap-1 text-sm font-medium text-muted-foreground">
                <DollarSign class="h-4 w-4 text-muted-foreground" />
                <span>Total Revenue</span>
            </div>
            <span :title="fullRevenueLabel" class="mt-2 block min-w-0 truncate text-3xl font-bold tabular-nums">${{ formatRevenue(totalRevenue) }}</span>
        </div>

        <!-- Active Clients -->
        <div class="rounded-lg border border-sidebar-border/70 bg-card p-4 dark:border-sidebar-border">
            <div class="text-sm font-medium text-muted-foreground">Active Clients</div>
            <div class="mt-2 text-3xl font-bold">{{ stats.activeClients }}</div>
        </div>

        <!-- Rooms Occupied -->
        <div class="rounded-lg border border-sidebar-border/70 bg-card p-4 dark:border-sidebar-border">
            <div class="text-sm font-medium text-muted-foreground">Rooms Occupied</div>
            <div class="mt-2 text-3xl font-bold">{{ stats.roomsOccupied }} / {{ stats.totalRooms }}</div>
        </div>
    </div>
</template>
