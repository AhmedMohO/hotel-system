<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import ClientNavbarLayout from '@/layouts/ClientNavbarLayout.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { CalendarDays, BedDouble, BadgeDollarSign, PlusCircle } from 'lucide-vue-next';

defineOptions({ layout: ClientNavbarLayout });

type ReservationRow = {
    id: number;
    room: {
        id: number | null;
        number: string | null;
    };
    check_in: string | null;
    check_out: string | null;
    paid_price: number;
    paid_price_formatted: string;
    approved_by: number | null;
    status?: string | null;
    is_approved: boolean;
    approved_by_name: string | null;
};

const props = defineProps<{
    reservations: ReservationRow[];
}>();

function statusLabel(row: ReservationRow): string {
    if (row.status) return row.status.charAt(0).toUpperCase() + row.status.slice(1);
    return row.approved_by ? 'Approved' : 'Pending';
}

function statusVariant(row: ReservationRow): 'default' | 'secondary' | 'destructive' | 'outline' {
    const label = statusLabel(row).toLowerCase();
    if (label === 'approved') return 'default';
    if (label === 'pending') return 'secondary';
    if (label === 'cancelled' || label === 'canceled') return 'destructive';
    return 'outline';
}
</script>

<template>
    <Head title="My Reservations" />

    <div class="mx-auto w-full max-w-5xl space-y-6 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-foreground">My reservations</h1>
                <p class="text-sm text-muted-foreground mt-1">{{ reservations.length }} booking{{ reservations.length !== 1 ? 's' : '' }} total</p>
            </div>
            <a href="/client/reservations">
                <Button class="gap-2">
                    <PlusCircle class="w-4 h-4" />
                    New reservation
                </Button>
            </a>
        </div>

        <!-- Empty state -->
        <Card v-if="reservations.length === 0">
            <CardContent class="flex flex-col items-center justify-center py-16 gap-3 text-center">
                <BedDouble class="w-10 h-10 text-muted-foreground/50" />
                <p class="font-medium text-foreground">No reservations yet</p>
                <p class="text-sm text-muted-foreground max-w-xs">
                    You haven't made any bookings. Browse available rooms to get started.
                </p>
                <a href="/client/reservations" class="mt-2">
                    <Button variant="outline" class="gap-2">
                        <PlusCircle class="w-4 h-4" />
                        Browse rooms
                    </Button>
                </a>
            </CardContent>
        </Card>

        <!-- Reservation cards -->
        <div v-else class="space-y-3">
            <Card v-for="reservation in reservations" :key="reservation.id" class="overflow-hidden">
                <CardContent class="p-0">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4 p-5">

                        <!-- Room number badge -->
                        <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-muted flex items-center justify-center">
                            <BedDouble class="w-5 h-5 text-muted-foreground" />
                        </div>

                        <!-- Main info -->
                        <div class="flex-1 min-w-0 space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="font-medium text-foreground">
                                    Room {{ reservation.room?.number ?? '—' }}
                                </p>
                                <Badge :variant="statusVariant(reservation)">
                                    {{ statusLabel(reservation) }}
                                </Badge>
                            </div>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-muted-foreground">
                                <span class="flex items-center gap-1.5">
                                    <CalendarDays class="w-3.5 h-3.5" />
                                    {{ reservation.check_in ?? '—' }} → {{ reservation.check_out ?? '—' }}
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <BadgeDollarSign class="w-3.5 h-3.5" />
                                    ${{ reservation.paid_price_formatted }}
                                </span>
                            </div>
                            <p v-if="reservation.approved_by_name" class="text-xs text-muted-foreground">
                                Approved by {{ reservation.approved_by_name }}
                            </p>
                        </div>

                    </div>
                </CardContent>
            </Card>
        </div>

    </div>
</template>