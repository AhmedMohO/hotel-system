<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { FlexRender, getCoreRowModel, useVueTable } from '@tanstack/vue-table';
import type { ColumnDef, SortingState } from '@tanstack/vue-table';
import {
    ArrowUpDown,
    ArrowUp,
    ArrowDown,
    ChevronLeft,
    ChevronRight,
    ChevronsLeft,
    ChevronsRight,
} from 'lucide-vue-next';
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

interface PaginatedData<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
}

const props = defineProps<{
    data: PaginatedData<any>;
    columns: ColumnDef<any, any>[];
    filters?: Record<string, any>;
    routeParams?: Record<string, any>;
}>();

const sorting = ref<SortingState>([]);
const globalFilter = ref(
    props.filters?.filter?.name ?? props.filters?.search ?? '',
);
const perPage = ref(props.data.per_page.toString());

let searchTimer: ReturnType<typeof setTimeout>;

const table = useVueTable({
    get data() {
        return props.data.data;
    },
    get columns() {
        return props.columns;
    },
    manualPagination: true,
    manualSorting: true,
    getCoreRowModel: getCoreRowModel(),
    pageCount: props.data.last_page,
    state: {
        get pagination() {
            return {
                pageIndex: props.data.current_page - 1,
                pageSize: props.data.per_page,
            };
        },
        get sorting() {
            return sorting.value;
        },
    },
    onSortingChange: (updater) => {
        sorting.value =
            typeof updater === 'function' ? updater(sorting.value) : updater;
        const sort = sorting.value[0];
        const sortParam = sort
            ? sort.desc
                ? `-${sort.id}`
                : sort.id
            : undefined;
        navigateTo({ sort: sortParam, page: 1 });
    },
    onPaginationChange: () => {},
});

function navigateTo(extra: Record<string, any> = {}) {
    router.get(
        window.location.pathname,
        {
            ...props.filters,
            ...props.routeParams,
            ...extra,
        },
        { preserveState: true, replace: true },
    );
}

function onSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        const filterState = { ...(props.filters?.filter || {}) };
        
        if (globalFilter.value) {
            filterState.name = globalFilter.value;
        } else {
            delete filterState.name;
        }

        navigateTo({
            filter: Object.keys(filterState).length > 0 ? filterState : undefined,
            page: 1,
        });
    }, 400);
}

function goToPage(page: number) {
    navigateTo({ page });
}

function onPerPageChange(val: any) {
    if (!val) {
        return;
    }

    navigateTo({ per_page: val, page: 1 });
}

// Compute pagination button numbers to show
const pageNumbers = computed(() => {
    const current = props.data.current_page;
    const last = props.data.last_page;
    const delta = 2;
    const range = [];

    for (
        let i = Math.max(2, current - delta);
        i <= Math.min(last - 1, current + delta);
        i++
    ) {
        range.push(i);
    }

    if (current - delta > 2) {
        range.unshift('...');
    }

    if (current + delta < last - 1) {
        range.push('...');
    }

    range.unshift(1);

    if (last !== 1) {
        range.push(last);
    }

    return range;
});
</script>

<template>
    <div class="space-y-4">
        <!-- Search -->
        <Input
            v-model="globalFilter"
            placeholder="Search..."
            class="max-w-xs"
            @input="onSearch"
        />

        <!-- Table -->
        <div class="rounded-md border">
            <Table>
                <TableHeader>
                    <TableRow
                        v-for="headerGroup in table.getHeaderGroups()"
                        :key="headerGroup.id"
                    >
                        <TableHead
                            v-for="header in headerGroup.headers"
                            :key="header.id"
                            :class="
                                header.column.getCanSort()
                                    ? 'cursor-pointer select-none'
                                    : ''
                            "
                            @click="
                                header.column.getCanSort()
                                    ? header.column.toggleSorting()
                                    : null
                            "
                        >
                            <div class="flex items-center gap-1">
                                <FlexRender
                                    v-if="!header.isPlaceholder"
                                    :render="header.column.columnDef.header"
                                    :props="header.getContext()"
                                />
                                <template v-if="header.column.getCanSort()">
                                    <ArrowUp
                                        v-if="
                                            header.column.getIsSorted() ===
                                            'asc'
                                        "
                                        class="h-4 w-4"
                                    />
                                    <ArrowDown
                                        v-else-if="
                                            header.column.getIsSorted() ===
                                            'desc'
                                        "
                                        class="h-4 w-4"
                                    />
                                    <ArrowUpDown
                                        v-else
                                        class="h-4 w-4 opacity-40"
                                    />
                                </template>
                            </div>
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="table.getRowModel().rows.length">
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
                    </template>
                    <TableRow v-else>
                        <TableCell
                            :colspan="columns.length"
                            class="h-24 text-center text-muted-foreground"
                        >
                            No results found.
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <!-- Pagination -->
        <div
            class="mt-4 flex items-center justify-between text-sm text-muted-foreground"
        >
            <div class="flex items-center gap-4">
                <span>
                    Showing {{ data.from ?? 0 }}–{{ data.to ?? 0 }} of
                    {{ data.total }} results
                </span>

                <!-- Per Page Select -->
            </div>

            <div class="flex items-center gap-2">
                <Button
                    variant="outline"
                    class="hidden h-8 w-8 p-0 lg:flex"
                    :disabled="data.current_page <= 1"
                    @click="goToPage(1)"
                >
                    <ChevronsLeft class="h-4 w-4" />
                </Button>
                <Button
                    variant="outline"
                    class="h-8 w-8 p-0"
                    :disabled="data.current_page <= 1"
                    @click="goToPage(data.current_page - 1)"
                >
                    <ChevronLeft class="h-4 w-4" />
                </Button>

                <!-- Numbered Buttons -->
                <template v-for="(page, idx) in pageNumbers" :key="idx">
                    <Button
                        v-if="page !== '...'"
                        variant="outline"
                        class="h-8 w-8 p-0"
                        :class="
                            page === data.current_page
                                ? 'bg-primary text-primary-foreground hover:bg-primary/90'
                                : ''
                        "
                        @click="goToPage(page as number)"
                    >
                        {{ page }}
                    </Button>
                    <span v-else class="px-2">...</span>
                </template>

                <Button
                    variant="outline"
                    class="h-8 w-8 p-0"
                    :disabled="data.current_page >= data.last_page"
                    @click="goToPage(data.current_page + 1)"
                >
                    <ChevronRight class="h-4 w-4" />
                </Button>
                <Button
                    variant="outline"
                    class="hidden h-8 w-8 p-0 lg:flex"
                    :disabled="data.current_page >= data.last_page"
                    @click="goToPage(data.last_page)"
                >
                    <ChevronsRight class="h-4 w-4" />
                </Button>
            </div>
            <div class="flex items-center gap-2">
                <span>Limit</span>
                <Select v-model="perPage" @update:model-value="onPerPageChange">
                    <SelectTrigger class="h-8 w-[70px]">
                        <SelectValue :placeholder="perPage" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="10">10</SelectItem>
                        <SelectItem value="20">20</SelectItem>
                        <SelectItem value="30">30</SelectItem>
                        <SelectItem value="40">40</SelectItem>
                        <SelectItem value="50">50</SelectItem>
                    </SelectContent>
                </Select>
            </div>
        </div>
    </div>
</template>
