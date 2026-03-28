<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import type { ColumnDef } from '@tanstack/vue-table';
import { Eye, Pencil, Trash2, UserPlus } from 'lucide-vue-next';
import { ref, h } from 'vue';
import { toast } from 'vue-sonner';
import ActionIcon from '@/components/ActionIcon.vue';
import AvatarUpload from '@/components/AvatarUpload.vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import DataTable from '@/components/DataTable.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import UserAvatar from '@/components/UserAvatar.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import * as Managers from '@/routes/managers';

interface Manager {
    id: number;
    name: string;
    email: string;
    national_id: string;
    avatar_image: string | null;
    created_at: string;
}

defineProps<{
    managers: {
        data: Manager[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
    filters: Record<string, any>;
}>();

const showModal = ref(false);
const editingManager = ref<Manager | null>(null);

const form = useForm({
    name: '',
    email: '',
    password: '',
    national_id: '',
    avatar_image: null as File | null,
    remove_avatar: false,
});

function openCreate() {
    editingManager.value = null;
    form.reset();
    showModal.value = true;
}

function openEdit(manager: Manager) {
    editingManager.value = manager;
    form.name = manager.name;
    form.email = manager.email;
    form.national_id = manager.national_id;
    form.password = '';
    form.avatar_image = null;
    form.remove_avatar = false;
    showModal.value = true;
}

function submit() {
    const opts = {
        forceFormData: true,
        onSuccess: () => {
            toast.success(
                editingManager.value
                    ? 'Manager updated successfully!'
                    : 'Manager created successfully!',
            );
            showModal.value = false;
            form.reset();
        },
        onError: () => {
            toast.error('Operation failed. Please check the form fields.');
        },
    };

    if (editingManager.value) {
        form.transform((data) => ({ ...data, _method: 'PATCH' })).post(
            Managers.update.url(editingManager.value.id),
            opts,
        );
    } else {
        form.transform((data) => data).post(Managers.store.url(), opts);
    }
}

const columns: ColumnDef<Manager, any>[] = [
    {
        accessorKey: 'name',
        header: 'Manager',
        enableSorting: true,
        cell: ({ row }) => {
            const r = row.original;

            return h('div', { class: 'flex items-center gap-3 py-1' }, [
                h(UserAvatar, {
                    user: r,
                    class: 'h-9 w-9 rounded-full ring-2 ring-border shrink-0',
                }),
                h('div', { class: 'min-w-0' }, [
                    h('p', { class: 'font-medium text-sm truncate' }, r.name),
                    h(
                        'p',
                        { class: 'text-xs text-muted-foreground truncate' },
                        r.email,
                    ),
                ]),
            ]);
        },
    },
    {
        accessorKey: 'national_id',
        header: 'National ID',
        enableSorting: false,
        cell: ({ getValue }) =>
            h(
                'span',
                {
                    class: 'font-mono text-xs bg-muted text-muted-foreground px-2 py-1 rounded-md',
                },
                getValue<string>(),
            ),
    },
    {
        accessorKey: 'created_at',
        header: 'Joined',
        enableSorting: true,
        cell: ({ getValue }) =>
            h(
                'span',
                { class: 'text-sm text-muted-foreground tabular-nums' },
                new Date(getValue<string>()).toLocaleDateString('en-US', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric',
                }),
            ),
    },
    {
        id: 'actions',
        header: '',
        enableSorting: false,
        cell: ({ row }) => {
            const r = row.original;

            return h('div', { class: 'flex items-center justify-end gap-1' }, [
                // View — sky blue
                h(ActionIcon, {
                    icon: Eye,
                    tooltip: 'View profile',
                    class: 'text-sky-600 hover:bg-sky-50 hover:text-sky-700 dark:text-sky-400 dark:hover:bg-sky-950/60 dark:hover:text-sky-300',
                    href: Managers.show.url(r.id),
                }),
                // Edit — amber
                h(ActionIcon, {
                    icon: Pencil,
                    tooltip: 'Edit',
                    class: 'text-amber-600 hover:bg-amber-50 hover:text-amber-700 dark:text-amber-400 dark:hover:bg-amber-950/60 dark:hover:text-amber-300',
                    onClick: () => openEdit(r),
                }),
                // Delete — red
                h(ConfirmDialog, {
                    url: Managers.destroy.url(r.id),
                    method: 'delete',
                    title: 'Delete Manager?',
                    description: `Delete ${r.name}? Their account will be soft-deleted.`,
                    triggerLabel: 'Delete',
                    triggerIcon: Trash2,
                    triggerTooltip: 'Delete',
                    triggerClass:
                        'text-red-600 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-950/60 dark:hover:text-red-300',
                    confirmLabel: 'Delete',
                    confirmVariant: 'destructive',
                }),
            ]);
        },
    },
];
</script>

<template>
    <AppLayout
        :breadcrumbs="[{ title: 'Managers', href: Managers.index.url() }]"
    >
        <div class="flex flex-col gap-5 p-6">
            <!-- Page Header -->
            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="space-y-0.5">
                    <h1 class="text-xl font-semibold tracking-tight">
                        Managers
                    </h1>
                    <p class="text-sm text-muted-foreground">
                        Manage hotel managers and their account access.
                    </p>
                </div>
                <Button class="w-fit shrink-0 gap-2" @click="openCreate">
                    <UserPlus class="h-4 w-4" />
                    Add Manager
                </Button>
            </div>

            <!-- Data Table -->
            <DataTable :data="managers" :columns="columns" :filters="filters" />
        </div>

        <!-- Create / Edit Dialog -->
        <Dialog v-model:open="showModal">
            <DialogContent class="max-w-md gap-0 overflow-hidden p-0">
                <DialogHeader class="px-6 pt-6 pb-4">
                    <DialogTitle class="text-base font-semibold">
                        {{ editingManager ? 'Edit Manager' : 'New Manager' }}
                    </DialogTitle>
                    <DialogDescription class="text-sm text-muted-foreground">
                        {{
                            editingManager
                                ? "Update this manager's account details."
                                : 'Fill in the details to create a new manager account.'
                        }}
                    </DialogDescription>
                </DialogHeader>

                <Separator />

                <form
                    class="flex flex-col gap-4 px-6 py-5"
                    @submit.prevent="submit"
                >
                    <!-- Avatar -->
                    <div class="flex justify-center">
                        <AvatarUpload
                            v-model="form.avatar_image"
                            v-model:removed="form.remove_avatar"
                            :current-avatar="editingManager?.avatar_image"
                            :error="form.errors.avatar_image"
                        />
                    </div>

                    <!-- Name -->
                    <div class="grid gap-1.5">
                        <Label
                            for="m-name"
                            class="text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                        >
                            Full Name
                        </Label>
                        <Input
                            id="m-name"
                            v-model="form.name"
                            placeholder="e.g. John Smith"
                            autocomplete="name"
                        />
                        <InputError :message="form.errors.name" />
                    </div>

                    <!-- Email -->
                    <div class="grid gap-1.5">
                        <Label
                            for="m-email"
                            class="text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                        >
                            Email Address
                        </Label>
                        <Input
                            id="m-email"
                            v-model="form.email"
                            type="email"
                            placeholder="john@hotel.com"
                            autocomplete="email"
                        />
                        <InputError :message="form.errors.email" />
                    </div>

                    <!-- National ID -->
                    <div class="grid gap-1.5">
                        <Label
                            for="m-national-id"
                            class="text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                        >
                            National ID
                        </Label>
                        <Input
                            id="m-national-id"
                            v-model="form.national_id"
                            placeholder="29XXXXXXXXXX"
                            class="font-mono tracking-wide"
                        />
                        <InputError :message="form.errors.national_id" />
                    </div>

                    <!-- Password -->
                    <div class="grid gap-1.5">
                        <div class="flex items-center justify-between">
                            <Label
                                for="m-password"
                                class="text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                            >
                                Password
                            </Label>
                            <span
                                v-if="editingManager"
                                class="text-xs text-muted-foreground"
                            >
                                Leave blank to keep current
                            </span>
                        </div>
                        <Input
                            id="m-password"
                            v-model="form.password"
                            type="password"
                            placeholder="••••••••"
                            autocomplete="new-password"
                        />
                        <InputError :message="form.errors.password" />
                    </div>

                    <Separator />

                    <!-- Footer -->
                    <div class="flex justify-end gap-2">
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            :disabled="form.processing"
                            @click="showModal = false"
                        >
                            Cancel
                        </Button>
                        <Button
                            type="submit"
                            size="sm"
                            :disabled="form.processing"
                        >
                            <span
                                v-if="form.processing"
                                class="mr-1.5 h-3.5 w-3.5 animate-spin rounded-full border-2 border-current border-t-transparent"
                            />
                            {{
                                editingManager
                                    ? 'Save Changes'
                                    : 'Create Manager'
                            }}
                        </Button>
                    </div>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
