<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import {
    FlexRender,
    getCoreRowModel,
    getPaginationRowModel,
    useVueTable,
    type ColumnDef,
} from '@tanstack/vue-table';
import { computed, h, reactive } from 'vue';
import ClientNavbarLayout from '@/layouts/ClientNavbarLayout.vue';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { Search, BedDouble, Users, DollarSign } from 'lucide-vue-next';

defineOptions({ layout: ClientNavbarLayout });

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

function reserveHref(roomId: number): string {
    const params = new URLSearchParams({
        check_in: filters.check_in,
        check_out: filters.check_out,
        accompany_number: filters.accompany_number,
    });
    return `/client/reservations/rooms/${roomId}?${params.toString()}`;
}

const columns: ColumnDef<Room>[] = [
    {
        accessorKey: 'number',
        header: 'Room',
        cell: ({ row }) => row.original.number,
    },
    {
        id: 'floor',
        header: 'Floor',
        cell: ({ row }) => row.original.floor?.name ?? '—',
    },
    {
        accessorKey: 'capacity',
        header: 'Capacity',
        cell: ({ row }) => `${row.original.capacity} guests`,
    },
    {
        accessorKey: 'price_formatted',
        header: 'Price / Night',
        cell: ({ row }) => `$${row.original.price_formatted}`,
    },
    {
        id: 'action',
        header: '',
        cell: ({ row }) => row.original.id,
    },
];

const table = useVueTable({
    get data() { return props.rooms; },
    get columns() { return columns; },
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    initialState: { pagination: { pageSize: 10 } },
});

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
            onSuccess: () => { table.setPageIndex(0); },
        },
    );
}
</script>

<template>
    <Head title="Available Rooms" />

    <div class="mx-auto w-full max-w-6xl space-y-6 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">

        <!-- Search Card -->
        <Card>
            <CardHeader>
                <CardTitle>Find available rooms</CardTitle>
                <CardDescription>Enter your stay details to see what's available</CardDescription>
            </CardHeader>
            <CardContent>
                <form class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4" @submit.prevent="searchAvailableRooms">
                    <div class="space-y-2">
                        <Label for="check-in" class="text-sm">Check-in date</Label>
                        <Input id="check-in" v-model="filters.check_in" type="date" required />
                        <InputError :message="errors.check_in" />
                    </div>

                    <div class="space-y-2">
                        <Label for="check-out" class="text-sm">Check-out date</Label>
                        <Input id="check-out" v-model="filters.check_out" type="date" required />
                        <InputError :message="errors.check_out" />
                    </div>

                    <div class="space-y-2">
                        <Label for="accompany" class="text-sm">Number of guests</Label>
                        <Input id="accompany" v-model="filters.accompany_number" type="number" min="1" required />
                        <InputError :message="errors.accompany_number" />
                    </div>

                    <div class="flex items-end sm:col-span-2 lg:col-span-1">
                        <Button class="w-full gap-2" type="submit">
                            <Search class="w-4 h-4" />
                            Search
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>

        <!-- Results Card -->
        <Card>
            <CardHeader class="flex flex-row items-center justify-between">
                <div>
                    <CardTitle>Available rooms</CardTitle>
                    <CardDescription v-if="hasSearchInputs && rooms.length > 0">
                        {{ rooms.length }} room{{ rooms.length !== 1 ? 's' : '' }} found
                    </CardDescription>
                </div>
            </CardHeader>
            <CardContent>

                <!-- Empty state -->
                <div v-if="table.getRowModel().rows.length === 0" class="flex flex-col items-center justify-center py-16 gap-3 text-center">
                    <BedDouble class="w-10 h-10 text-muted-foreground/50" />
                    <p class="font-medium text-foreground">
                        {{ hasSearchInputs ? 'No rooms available' : 'Search to see rooms' }}
                    </p>
                    <p class="text-sm text-muted-foreground max-w-xs">
                        {{ hasSearchInputs ? 'Try different dates or fewer guests.' : 'Enter your check-in, check-out dates and number of guests above.' }}
                    </p>
                </div>

                <!-- Table -->
                <div v-else class="overflow-x-auto rounded-md border">
                    <Table>
                        <TableHeader>
                            <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                                <TableHead
                                    v-for="header in headerGroup.headers"
                                    :key="header.id"
                                    :class="header.id === 'action' ? 'text-right w-24' : ''"
                                >
                                    <FlexRender
                                        v-if="!header.isPlaceholder"
                                        :render="header.column.columnDef.header"
                                        :props="header.getContext()"
                                    />
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="row in table.getRowModel().rows" :key="row.id" class="hover:bg-muted/50">
                                <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id" :class="cell.column.id === 'action' ? 'text-right' : ''">
                                    <a v-if="cell.column.id === 'action'" :href="reserveHref(row.original.id)">
                                        <Button size="sm">Reserve</Button>
                                    </a>
                                    <FlexRender v-else :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <!-- Pagination -->
                <div v-if="table.getRowModel().rows.length > 0" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-muted-foreground">
                        Page {{ table.getState().pagination.pageIndex + 1 }} of {{ Math.max(table.getPageCount(), 1) }}
                    </p>
                    <div class="flex items-center gap-2">
                        <label for="page-size" class="text-sm text-muted-foreground">Rows</label>
                        <select
                            id="page-size"
                            class="h-9 rounded-md border border-input bg-background px-2 text-sm"
                            :value="String(table.getState().pagination.pageSize)"
                            @change="table.setPageSize(Number(($event.target as HTMLSelectElement).value))"
                        >
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                        </select>
                        <Button size="sm" variant="outline" :disabled="!table.getCanPreviousPage()" @click="table.previousPage()">Previous</Button>
                        <Button size="sm" variant="outline" :disabled="!table.getCanNextPage()" @click="table.nextPage()">Next</Button>
                    </div>
                </div>

            </CardContent>
        </Card>
    </div>
</template>