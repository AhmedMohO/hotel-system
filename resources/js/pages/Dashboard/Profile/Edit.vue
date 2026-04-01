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
                toast.error(
                    'Failed to update profile. Please check the form fields.',
                );
            },
        },
    );
}
</script>

<template>
    <AppLayout
        :breadcrumbs="[{ title: 'My Profile', href: '/dashboard/profile' }]"
    >
        <div class="mx-auto w-full space-y-0 p-6">
            <!-- Page Header -->
            <div class="mb-8">
                <h1
                    class="text-xl font-semibold tracking-tight text-foreground"
                >
                    Profile Settings
                </h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    Manage your account information and credentials.
                </p>
            </div>

            <form @submit.prevent="submit" class="space-y-0">
                <!-- Section: Avatar -->
                <div class="rounded-t-lg border border-b-0 bg-card px-6 py-5">
                    <div class="mb-4 flex items-center gap-2">
                        <span class="h-4 w-0.5 rounded-full bg-primary"></span>
                        <h2
                            class="text-sm font-semibold tracking-widest text-muted-foreground uppercase"
                        >
                            Avatar
                        </h2>
                    </div>
                    <AvatarUpload
                        v-model="form.avatar_image"
                        v-model:removed="form.remove_avatar"
                        :current-avatar="user.avatar_image"
                        :error="form.errors.avatar_image"
                    />
                </div>

                <!-- Section: Personal Info -->
                <div class="border border-b-0 bg-card px-6 py-5">
                    <div class="mb-5 flex items-center gap-2">
                        <span class="h-4 w-0.5 rounded-full bg-primary"></span>
                        <h2
                            class="text-sm font-semibold tracking-widest text-muted-foreground uppercase"
                        >
                            Personal Information
                        </h2>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="grid gap-1.5">
                            <Label for="p-name" class="text-sm font-medium">
                                Full Name
                            </Label>
                            <Input
                                id="p-name"
                                v-model="form.name"
                                placeholder="Your full name"
                                class="h-9"
                            />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-1.5">
                            <Label for="p-email" class="text-sm font-medium">
                                Email Address
                            </Label>
                            <Input
                                id="p-email"
                                v-model="form.email"
                                type="email"
                                placeholder="you@example.com"
                                class="h-9"
                            />
                            <InputError :message="form.errors.email" />
                        </div>
                    </div>
                </div>

                <!-- Section: Password -->
                <div class="rounded-b-none border bg-card px-6 py-5">
                    <div class="mb-5 flex items-center gap-2">
                        <span class="h-4 w-0.5 rounded-full bg-primary"></span>
                        <h2
                            class="text-sm font-semibold tracking-widest text-muted-foreground uppercase"
                        >
                            Change Password
                        </h2>
                    </div>
                    <p class="mb-5 text-sm text-muted-foreground">
                        Leave both fields blank if you don't want to change your
                        password.
                    </p>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="grid gap-1.5">
                            <Label for="p-password" class="text-sm font-medium">
                                New Password
                            </Label>
                            <Input
                                id="p-password"
                                v-model="form.password"
                                type="password"
                                autocomplete="new-password"
                                placeholder="••••••••"
                                class="h-9"
                            />
                            <InputError :message="form.errors.password" />
                        </div>

                        <div class="grid gap-1.5">
                            <Label for="p-confirm" class="text-sm font-medium">
                                Confirm Password
                            </Label>
                            <Input
                                id="p-confirm"
                                v-model="form.password_confirmation"
                                type="password"
                                autocomplete="new-password"
                                placeholder="••••••••"
                                class="h-9"
                            />
                        </div>
                    </div>
                </div>

                <!-- Footer / Actions -->
                <div
                    class="flex items-center justify-between rounded-b-lg border border-t-0 bg-muted/40 px-6 py-4"
                >
                    <p class="text-xs text-muted-foreground">
                        All changes are saved immediately after submission.
                    </p>
                    <Button
                        type="submit"
                        :disabled="form.processing"
                        class="h-9 min-w-28"
                    >
                        <span
                            v-if="form.processing"
                            class="flex items-center gap-2"
                        >
                            <svg
                                class="h-3.5 w-3.5 animate-spin"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                />
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                                />
                            </svg>
                            Saving…
                        </span>
                        <span v-else>Save Changes</span>
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
