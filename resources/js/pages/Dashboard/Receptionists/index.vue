<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import type { ColumnDef } from '@tanstack/vue-table';
import { Eye, Pencil, Trash2, ShieldAlert, ShieldCheck } from 'lucide-vue-next';
import { ref, h } from 'vue';
import ActionIcon from '@/components/ActionIcon.vue';
import AvatarUpload from '@/components/AvatarUpload.vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import DataTable from '@/components/DataTable.vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
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
            showModal.value = false;
            form.reset();
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
        header: 'Name',
        enableSorting: true,
        cell: ({ row }) => {
            const r = row.original;

            return h('div', { class: 'flex items-center gap-3' }, [
                h(UserAvatar, {
                    user: r,
                    class: 'h-8 w-8 rounded-full border border-border shadow-none',
                }),
                h('span', { class: 'font-medium' }, r.name),
            ]);
        },
    },
    { accessorKey: 'email', header: 'Email', enableSorting: true },
    {
        accessorKey: 'created_at',
        header: 'Created',
        enableSorting: true,
        cell: ({ getValue }) =>
            new Date(getValue<string>()).toLocaleDateString(),
    },
    ...(isAdmin
        ? [
              {
                  accessorKey: 'created_by_name',
                  header: 'Manager Name',
                  enableSorting: false,
                  cell: ({ getValue }: any) => getValue() ?? '—',
              } as ColumnDef<Receptionist, any>,
          ]
        : []),
    {
        id: 'status',
        header: 'Status',
        enableSorting: false,
        cell: ({ row }) =>
            h(
                Badge,
                {
                    variant: row.original.banned_at
                        ? 'destructive'
                        : 'secondary',
                },
                () => (row.original.banned_at ? 'Banned' : 'Active'),
            ),
    },
    {
        id: 'actions',
        header: 'Actions',
        enableSorting: false,
        cell: ({ row }) => {
            const r = row.original;

            return h('div', { class: 'flex gap-2' }, [
                h(ActionIcon, {
                    icon: Eye,
                    tooltip: 'View',
                    variant: 'ghost',
                    href: Receptionists.show.url(r.id),
                }),
                ...(r.can_manage ? [
                    h(ActionIcon, {
                        icon: Pencil,
                        tooltip: 'Edit',
                        variant: 'ghost',
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
                              triggerVariant: 'ghost',
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
                              triggerVariant: 'ghost',
                              confirmLabel: 'Ban',
                              confirmVariant: 'destructive',
                          }),
                    h(ConfirmDialog, {
                        url: Receptionists.destroy.url(r.id),
                        method: 'delete',
                        title: 'Delete Receptionist?',
                        description: `Delete ${r.name}? Their account will be soft-deleted.`,
                        triggerLabel: 'Delete',
                        triggerIcon: Trash2,
                        triggerTooltip: 'Delete',
                        triggerVariant: 'ghost',
                        confirmLabel: 'Delete',
                        confirmVariant: 'destructive',
                    }),
                ] : []),
            ]);
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
        <div class="space-y-6 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Manage Receptionists</h1>
                <Button @click="openCreate">+ Add Receptionist</Button>
            </div>

            <DataTable
                :data="receptionists"
                :columns="columns"
                :filters="filters"
            />
        </div>

        <Dialog v-model:open="showModal">
            <DialogContent class="max-w-lg">
                <DialogHeader>
                    <DialogTitle>{{
                        editingReceptionist
                            ? 'Edit Receptionist'
                            : 'Add Receptionist'
                    }}</DialogTitle>
                </DialogHeader>

                <form class="space-y-4" @submit.prevent="submit">
                    <AvatarUpload
                        v-model="form.avatar_image"
                        v-model:removed="form.remove_avatar"
                        :current-avatar="editingReceptionist?.avatar_image"
                        :error="form.errors.avatar_image"
                    />

                    <div class="grid gap-2">
                        <Label for="r-name">Name</Label>
                        <Input id="r-name" v-model="form.name" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="r-email">Email</Label>
                        <Input id="r-email" v-model="form.email" type="email" />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="r-national-id">National ID</Label>
                        <Input id="r-national-id" v-model="form.national_id" />
                        <InputError :message="form.errors.national_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="r-password">
                            Password
                            <span
                                v-if="editingReceptionist"
                                class="text-xs text-muted-foreground"
                                >(leave blank to keep)</span
                            >
                        </Label>
                        <Input
                            id="r-password"
                            v-model="form.password"
                            type="password"
                            autocomplete="new-password"
                        />
                        <InputError :message="form.errors.password" />
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <Button
                            type="button"
                            variant="outline"
                            @click="showModal = false"
                            >Cancel</Button
                        >
                        <Button type="submit" :disabled="form.processing">
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
