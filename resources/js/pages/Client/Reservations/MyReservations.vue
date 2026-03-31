<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Head } from '@inertiajs/vue3';
import ClientNavbarLayout from '@/layouts/ClientNavbarLayout.vue';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { FlexRender, getCoreRowModel, useVueTable } from '@tanstack/vue-table';
import type { ColumnDef } from '@tanstack/vue-table';

defineOptions({
    layout: ClientNavbarLayout,
});

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

const columns: ColumnDef<ReservationRow>[] = [
    {
        accessorFn: (row) => row.room?.number ?? '-',
        id: 'room_number',
        header: 'Room Number',
        cell: ({ row }) => row.original.room?.number ?? '-',
    },
    {
        accessorKey: 'check_in',
        header: 'Check-in',
        cell: ({ row }) => row.original.check_in ?? '-',
    },
    {
        accessorKey: 'check_out',
        header: 'Check-out',
        cell: ({ row }) => row.original.check_out ?? '-',
    },
    {
        accessorKey: 'paid_price_formatted',
        header: 'Paid Price',
        cell: ({ row }) => `$${row.original.paid_price_formatted}`,
    },
    {
        id: 'status',
        header: 'Status',
        cell: ({ row }) => {
            if (row.original.status) {
                return row.original.status.charAt(0).toUpperCase() + row.original.status.slice(1);
            }

            return row.original.approved_by ? 'Approved' : 'Pending';
        },
    },
];

const table = useVueTable({
    get data() {
        return props.reservations;
    },
    get columns() {
        return columns;
    },
    getCoreRowModel: getCoreRowModel(),
});
</script>

<template>
    <Head title="My Reservations" />

    <div
        class="mx-auto w-full max-w-5xl space-y-6 px-4 py-6 sm:px-6 lg:px-8 lg:py-8"
    >
        <Card>
            <CardHeader>
                <CardTitle>My Reservations</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="overflow-x-auto rounded-md border">
                    <Table>
                        <TableHeader>
                            <TableRow
                                v-for="headerGroup in table.getHeaderGroups()"
                                :key="headerGroup.id"
                            >
                                <TableHead
                                    v-for="header in headerGroup.headers"
                                    :key="header.id"
                                >
                                    <FlexRender
                                        :render="header.column.columnDef.header"
                                        :props="header.getContext()"
                                    />
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow
                                v-for="row in table.getRowModel().rows"
                                :key="row.id"
                            >
                                <TableCell
                                    v-for="cell in row.getVisibleCells()"
                                    :key="cell.id"
                                >
                                    <FlexRender
                                        :render="cell.column.columnDef.cell"
                                        :props="cell.getContext()"
                                    />
                                </TableCell>
                            </TableRow>

                            <TableRow
                                v-if="table.getRowModel().rows.length === 0"
                            >
                                <TableCell
                                    :colspan="columns.length"
                                    class="h-20 text-center text-muted-foreground"
                                >
                                    You do not have any reservations yet.
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
