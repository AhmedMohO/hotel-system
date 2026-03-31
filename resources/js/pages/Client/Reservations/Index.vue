<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, reactive } from 'vue';
import ClientNavbarLayout from '@/layouts/ClientNavbarLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

defineOptions({
    layout: ClientNavbarLayout,
});

type Room = {
    id: number;
    number: string;
    capacity: number;
    price: number;
    price_formatted: string;
    floor?: {
        id: number;
        name: string;
        number: string;
    };
};

const props = defineProps<{
    rooms: Room[];
    filters: {
        check_in?: string | null;
        check_out?: string | null;
        accompany_number?: number | string | null;
    };
}>();

const page = usePage<{ errors: Record<string, string> }>();

const filters = reactive({
    check_in: props.filters.check_in ?? '',
    check_out: props.filters.check_out ?? '',
    accompany_number: props.filters.accompany_number ? String(props.filters.accompany_number) : '1',
});

const hasSearchInputs = computed(() => Boolean(filters.check_in && filters.check_out && filters.accompany_number));
const errors = computed(() => page.props.errors ?? {});

function searchAvailableRooms() {
    router.get(
        '/client/reservations',
        {
            check_in: filters.check_in,
            check_out: filters.check_out,
            accompany_number: filters.accompany_number,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
}

function reserveHref(roomId: number): string {
    const params = new URLSearchParams({
        check_in: filters.check_in,
        check_out: filters.check_out,
        accompany_number: filters.accompany_number,
    });

    return `/client/reservations/rooms/${roomId}?${params.toString()}`;
}
</script>

<template>
    <Head title="Available Rooms" />

    <div class="mx-auto w-full max-w-6xl space-y-6 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
        <Card>
            <CardHeader>
                <CardTitle>Find Available Rooms</CardTitle>
            </CardHeader>
            <CardContent>
                <form class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4" @submit.prevent="searchAvailableRooms">
                    <div class="space-y-2">
                        <Label for="check-in" class="text-sm">Check-in</Label>
                        <Input id="check-in" v-model="filters.check_in" type="date" required />
                        <InputError :message="errors.check_in" />
                    </div>

                    <div class="space-y-2">
                        <Label for="check-out" class="text-sm">Check-out</Label>
                        <Input id="check-out" v-model="filters.check_out" type="date" required />
                        <InputError :message="errors.check_out" />
                    </div>

                    <div class="space-y-2">
                        <Label for="accompany" class="text-sm">Accompany Number</Label>
                        <Input id="accompany" v-model="filters.accompany_number" type="number" min="1" required />
                        <InputError :message="errors.accompany_number" />
                    </div>

                    <div class="flex items-end sm:col-span-2 lg:col-span-1">
                        <Button class="w-full" type="submit">Search</Button>
                    </div>
                </form>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Available Rooms</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="overflow-x-auto rounded-md border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Room Number</TableHead>
                                <TableHead>Floor</TableHead>
                                <TableHead>Capacity</TableHead>
                                <TableHead>Price / Night</TableHead>
                                <TableHead class="text-right">Action</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="room in rooms" :key="room.id">
                                <TableCell>{{ room.number }}</TableCell>
                                <TableCell>{{ room.floor?.name ?? '-' }}</TableCell>
                                <TableCell>{{ room.capacity }}</TableCell>
                                <TableCell>${{ room.price_formatted }}</TableCell>
                                <TableCell class="text-right">
                                    <a :href="reserveHref(room.id)">
                                        <Button size="sm">Reserve</Button>
                                    </a>
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="rooms.length === 0">
                                <TableCell colspan="5" class="h-20 text-center text-muted-foreground">
                                    {{ hasSearchInputs ? 'No rooms available for this date range.' : 'Select dates to start searching.' }}
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </CardContent>
        </Card>
    </div>
</template>

