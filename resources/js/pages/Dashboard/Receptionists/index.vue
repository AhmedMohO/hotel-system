<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import type { ColumnDef } from '@tanstack/vue-table';
import {
    Eye,
    Pencil,
    Trash2,
    ShieldAlert,
    ShieldCheck,
    UserPlus,
} from 'lucide-vue-next';
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
import * as Receptionists from '@/routes/receptionists';

interface Receptionist {
    id: number;
    name: string;
    email: string;
    national_id: string;
    avatar_image: string | null;
    created_at: string;
    created_by_name: string | null;
    banned_at: string | null;
    can_manage: boolean;
}

defineProps<{
    receptionists: {
        data: Receptionist[];
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

const page = usePage<{ auth: { user: { roles: string[] } } }>();
const isAdmin = page.props.auth.user?.roles?.includes('admin') ?? false;

const showModal = ref(false);
const editingReceptionist = ref<Receptionist | null>(null);

const form = useForm({
    name: '',
    email: '',
    password: '',
    national_id: '',
    avatar_image: null as File | null,
    remove_avatar: false,
});

function openCreate() {
    editingReceptionist.value = null;
    form.reset();
    showModal.value = true;
}

function openEdit(r: Receptionist) {
    editingReceptionist.value = r;
    form.name = r.name;
    form.email = r.email;
    form.national_id = r.national_id;
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
                editingReceptionist.value
                    ? 'Receptionist updated successfully!'
                    : 'Receptionist created successfully!',
            );
            showModal.value = false;
            form.reset();
        },
        onError: () => {
            toast.error('Operation failed. Please check the form fields.');
        },
    };

    if (editingReceptionist.value) {
        form.transform((data) => ({ ...data, _method: 'PATCH' })).post(
            Receptionists.update.url(editingReceptionist.value.id),
            opts,
        );
    } else {
        form.transform((data) => data).post(Receptionists.store.url(), opts);
    }
}

const columns: ColumnDef<Receptionist, any>[] = [
    {
        accessorKey: 'name',
        header: 'Receptionist',
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
    ...(isAdmin
        ? [
              {
                  accessorKey: 'created_by_name',
                  header: 'Added By',
                  enableSorting: false,
                  cell: ({ getValue }: any) =>
                      h('span', { class: 'text-sm' }, getValue() ?? '—'),
              } as ColumnDef<Receptionist, any>,
          ]
        : []),
    {
        id: 'status',
        header: 'Status',
        enableSorting: false,
        cell: ({ row }) => {
            const banned = !!row.original.banned_at;

            return h('div', { class: 'flex items-center gap-1.5' }, [
                h('span', {
                    class: [
                        'inline-block h-2 w-2 rounded-full',
                        banned ? 'bg-red-500' : 'bg-emerald-500',
                    ],
                }),
                h(
                    'span',
                    {
                        class: [
                            'text-xs font-medium',
                            banned
                                ? 'text-red-600 dark:text-red-400'
                                : 'text-emerald-600 dark:text-emerald-400',
                        ],
                    },
                    banned ? 'Banned' : 'Active',
                ),
            ]);
        },
    },
    {
        id: 'actions',
        header: () =>
            h('div', { class: 'text-center justify-center flex-1' }, 'Actions'),
        enableSorting: false,
        cell: ({ row }) => {
            const r = row.original;

            return h(
                'div',
                { class: 'flex items-center justify-center gap-1' },
                [
                    h(ActionIcon, {
                        icon: Eye,
                        tooltip: 'View profile',
                        class: 'text-sky-600 hover:bg-sky-50 hover:text-sky-700 dark:text-sky-400 dark:hover:bg-sky-950/60 dark:hover:text-sky-300',
                        href: Receptionists.show.url(r.id),
                    }),
                    ...(r.can_manage
                        ? [
                              h(ActionIcon, {
                                  icon: Pencil,
                                  tooltip: 'Edit',
                                  class: 'text-amber-600 hover:bg-amber-50 hover:text-amber-700 dark:text-amber-400 dark:hover:bg-amber-950/60 dark:hover:text-amber-300',
                                  onClick: () => openEdit(r),
                              }),
                              r.banned_at
                                  ? h(ConfirmDialog, {
                                        url: Receptionists.unban.url(r.id),
                                        method: 'patch',
                                        title: 'Unban Receptionist?',
                                        description: `Allow ${r.name} to log in again?`,
                                        triggerLabel: 'Unban',
                                        triggerIcon: ShieldCheck,
                                        triggerTooltip: 'Unban',
                                        triggerClass:
                                            'text-emerald-600 hover:bg-emerald-50 hover:text-emerald-700 dark:text-emerald-400 dark:hover:bg-emerald-950/60 dark:hover:text-emerald-300',
                                        confirmLabel: 'Unban',
                                    })
                                  : h(ConfirmDialog, {
                                        url: Receptionists.ban.url(r.id),
                                        method: 'patch',
                                        title: 'Ban Receptionist?',
                                        description: `Prevent ${r.name} from logging in?`,
                                        triggerLabel: 'Ban',
                                        triggerIcon: ShieldAlert,
                                        triggerTooltip: 'Ban',
                                        triggerClass:
                                            'text-orange-600 hover:bg-orange-50 hover:text-orange-700 dark:text-orange-400 dark:hover:bg-orange-950/60 dark:hover:text-orange-300',
                                        confirmLabel: 'Ban',
                                        confirmVariant: 'destructive',
                                    }),
                              h(ConfirmDialog, {
                                  url: Receptionists.destroy.url(r.id),
                                  method: 'delete',
                                  title: 'Delete Receptionist?',
                                  description: `Delete ${r.name}? if there are any linked data to this receptionist, the account will be soft-deleted.`,
                                  triggerLabel: 'Delete',
                                  triggerIcon: Trash2,
                                  triggerTooltip: 'Delete',
                                  triggerClass:
                                      'text-red-600 hover:bg-red-50 hover:text-red-700 dark:text-red-400 dark:hover:bg-red-950/60 dark:hover:text-red-300',
                                  confirmLabel: 'Delete',
                                  confirmVariant: 'destructive',
                              }),
                          ]
                        : []),
                ],
            );
        },
    },
];
</script>

<template>
    <AppLayout
        :breadcrumbs="[
            { title: 'Receptionists', href: Receptionists.index.url() },
        ]"
    >
        <div class="flex flex-col gap-5 p-6">
            <div
                class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="space-y-0.5">
                    <h1 class="text-xl font-semibold tracking-tight">
                        Receptionists
                    </h1>
                    <p class="text-sm text-muted-foreground">
                        Manage front-desk staff, access and permissions.
                    </p>
                </div>
                <Button class="w-fit shrink-0 gap-2" @click="openCreate">
                    <UserPlus class="h-4 w-4" />
                    Add Receptionist
                </Button>
            </div>

            <!-- Data Table -->
            <DataTable
                :data="receptionists"
                :columns="columns"
                :filters="filters"
            />
        </div>

        <!-- Create / Edit Dialog -->
        <Dialog v-model:open="showModal">
            <DialogContent class="max-w-md gap-0 overflow-hidden p-0">
                <DialogHeader class="px-6 pt-6 pb-4">
                    <DialogTitle class="text-base font-semibold">
                        {{
                            editingReceptionist
                                ? 'Edit Receptionist'
                                : 'New Receptionist'
                        }}
                    </DialogTitle>
                    <DialogDescription class="text-sm text-muted-foreground">
                        {{
                            editingReceptionist
                                ? "Update this receptionist's account details."
                                : 'Fill in the details to create a new account.'
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
                            :current-avatar="editingReceptionist?.avatar_image"
                            :error="form.errors.avatar_image"
                        />
                    </div>

                    <!-- Name -->
                    <div class="grid gap-1.5">
                        <Label
                            for="r-name"
                            class="text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                        >
                            Full Name
                        </Label>
                        <Input
                            id="r-name"
                            v-model="form.name"
                            placeholder="e.g. Jane Doe"
                            autocomplete="name"
                        />
                        <InputError :message="form.errors.name" />
                    </div>

                    <!-- Email -->
                    <div class="grid gap-1.5">
                        <Label
                            for="r-email"
                            class="text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                        >
                            Email Address
                        </Label>
                        <Input
                            id="r-email"
                            v-model="form.email"
                            type="email"
                            placeholder="jane@hotel.com"
                            autocomplete="email"
                        />
                        <InputError :message="form.errors.email" />
                    </div>

                    <!-- National ID -->
                    <div class="grid gap-1.5">
                        <Label
                            for="r-national-id"
                            class="text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                        >
                            National ID
                        </Label>
                        <Input
                            id="r-national-id"
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
                                for="r-password"
                                class="text-xs font-semibold tracking-wider text-muted-foreground uppercase"
                            >
                                Password
                            </Label>
                            <span
                                v-if="editingReceptionist"
                                class="text-xs text-muted-foreground"
                            >
                                Leave blank to keep current
                            </span>
                        </div>
                        <Input
                            id="r-password"
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
                                editingReceptionist
                                    ? 'Save Changes'
                                    : 'Create Receptionist'
                            }}
                        </Button>
                    </div>
                </form>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
