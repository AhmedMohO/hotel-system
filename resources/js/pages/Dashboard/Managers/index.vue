<script setup lang="ts">
import { useForm, router } from '@inertiajs/vue3';
import type { ColumnDef } from '@tanstack/vue-table';
import { ref, h } from 'vue';
import AvatarUpload from '@/components/AvatarUpload.vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import DataTable from '@/components/DataTable.vue';
import UserAvatar from '@/components/UserAvatar.vue';
import ActionIcon from '@/components/ActionIcon.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Eye, Pencil, Trash2 } from 'lucide-vue-next';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
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
            showModal.value = false;
            form.reset();
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
    { accessorKey: 'national_id', header: 'National ID', enableSorting: false },
    {
        accessorKey: 'created_at',
        header: 'Created',
        enableSorting: true,
        cell: ({ getValue }) =>
            new Date(getValue<string>()).toLocaleDateString(),
    },
    {
        id: 'actions',
        header: 'Actions',
        enableSorting: false,
        cell: ({ row }) =>
            h('div', { class: 'flex gap-2' }, [
                h(ActionIcon, {
                    icon: Eye,
                    tooltip: 'View',
                    variant: 'ghost',
                    href: Managers.show.url(row.original.id),
                }),
                h(ActionIcon, {
                    icon: Pencil,
                    tooltip: 'Edit',
                    variant: 'ghost',
                    onClick: () => openEdit(row.original),
                }),
                h(ConfirmDialog, {
                    url: Managers.destroy.url(row.original.id),
                    method: 'delete',
                    title: 'Delete Manager?',
                    description: `Delete ${row.original.name}? Their account will be soft-deleted.`,
                    triggerLabel: 'Delete',
                    triggerIcon: Trash2,
                    triggerTooltip: 'Delete',
                    triggerVariant: 'ghost',
                    confirmLabel: 'Delete',
                    confirmVariant: 'destructive',
                }),
            ]),
    },
];
</script>

<template>
    <AppLayout
        :breadcrumbs="[{ title: 'Managers', href: Managers.index.url() }]"
    >
        <div class="space-y-6 p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Manage Managers</h1>
                <Button @click="openCreate">+ Add Manager</Button>
            </div>

            <DataTable :data="managers" :columns="columns" :filters="filters" />
        </div>

        <Dialog v-model:open="showModal">
            <DialogContent class="max-w-lg">
                <DialogHeader>
                    <DialogTitle>{{
                        editingManager ? 'Edit Manager' : 'Add Manager'
                    }}</DialogTitle>
                </DialogHeader>

                <form class="space-y-4" @submit.prevent="submit">
                    <AvatarUpload
                        v-model="form.avatar_image"
                        v-model:removed="form.remove_avatar"
                        :current-avatar="editingManager?.avatar_image"
                        :error="form.errors.avatar_image"
                    />

                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input id="name" v-model="form.name" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email</Label>
                        <Input id="email" v-model="form.email" type="email" />
                        <InputError :message="form.errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="national_id">National ID</Label>
                        <Input id="national_id" v-model="form.national_id" />
                        <InputError :message="form.errors.national_id" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password">
                            Password
                            <span
                                v-if="editingManager"
                                class="text-xs text-muted-foreground"
                                >(leave blank to keep)</span
                            >
                        </Label>
                        <Input
                            id="password"
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
