<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import AvatarUpload from '@/components/AvatarUpload.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';

interface User {
    id: number;
    name: string;
    email: string;
    avatar_image: string | null;
}

const props = defineProps<{
    user: User;
}>();

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    avatar_image: null as File | null,
    remove_avatar: false,
});

function submit() {
    form.transform((data) => ({ ...data, _method: 'PATCH' })).post(
        '/dashboard/profile',
        {
            forceFormData: true,
            onSuccess: () => {
                toast.success('Profile updated successfully!');
                form.password = '';
                form.password_confirmation = '';
                form.avatar_image = null;
                form.remove_avatar = false;
            },
            onError: () => {
                toast.error('Failed to update profile. Please check the form fields.');
            },
        },
    );
}
</script>

<template>
    <AppLayout
        :breadcrumbs="[{ title: 'My Profile', href: '/dashboard/profile' }]"
    >
        <div class="mx-auto w-full space-y-6 p-6">
            <h1 class="text-2xl font-semibold">Edit Profile</h1>

            <form
                class="space-y-6 rounded-lg border p-6"
                @submit.prevent="submit"
            >
                <AvatarUpload
                    v-model="form.avatar_image"
                    v-model:removed="form.remove_avatar"
                    :current-avatar="user.avatar_image"
                    :error="form.errors.avatar_image"
                />

                <div class="grid gap-2">
                    <Label for="p-name">Name</Label>
                    <Input id="p-name" v-model="form.name" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="p-email">Email</Label>
                    <Input id="p-email" v-model="form.email" type="email" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="p-password">
                        New Password
                        <span class="text-xs text-muted-foreground"
                            >(leave blank to keep)</span
                        >
                    </Label>
                    <Input
                        id="p-password"
                        v-model="form.password"
                        type="password"
                        autocomplete="new-password"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="p-confirm">Confirm New Password</Label>
                    <Input
                        id="p-confirm"
                        v-model="form.password_confirmation"
                        type="password"
                        autocomplete="new-password"
                    />
                </div>

                <div class="flex justify-end">
                    <Button type="submit" :disabled="form.processing"
                        >Save Changes</Button
                    >
                </div>
            </form>
        </div>
    </AppLayout>
</template>
