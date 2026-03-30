<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { toast } from 'vue-sonner';
import AvatarUpload from '@/components/AvatarUpload.vue';
import DeleteUser from '@/components/DeleteUser.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';
import type { BreadcrumbItem } from '@/types';

type Props = {
    mustVerifyEmail: boolean;
    status?: string;
};

defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    { title: 'Profile settings', href: edit() },
];

const page = usePage<{
    auth: {
        user: { name: string; email: string; avatar_image: string | null };
    };
}>();
const user = computed(() => page.props.auth.user);

// Separate form for avatar + password (requires multipart)
const avatarForm = useForm({
    name: user.value.name,
    email: user.value.email,
    avatar_image: null as File | null,
    password: '',
    password_confirmation: '',
});

function submitAvatar() {
    avatarForm
        .transform((data) => ({ ...data, _method: 'PATCH' }))
        .post('/settings/profile', {
            forceFormData: true,
            onSuccess: () => {
                toast.success('Profile updated successfully!');
                avatarForm.password = '';
                avatarForm.password_confirmation = '';
                avatarForm.avatar_image = null;
            },
            onError: () => {
                toast.error('Failed to update profile. Please check the form fields.');
            },
        });
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Profile settings" />
        <h1 class="sr-only">Profile settings</h1>

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <!-- Avatar + Name + Email + Password (multipart form) -->
                <Heading
                    variant="small"
                    title="Profile information"
                    description="Update your name, email address, profile picture, and password"
                />

                <form class="space-y-6" @submit.prevent="submitAvatar">
                    <!-- Avatar -->
                    <AvatarUpload
                        v-model="avatarForm.avatar_image"
                        :current-avatar="user.avatar_image"
                        :error="avatarForm.errors.avatar_image"
                    />

                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input
                            id="name"
                            v-model="avatarForm.name"
                            class="mt-1 block w-full"
                            autocomplete="name"
                            placeholder="Full name"
                        />
                        <InputError
                            class="mt-2"
                            :message="avatarForm.errors.name"
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input
                            id="email"
                            v-model="avatarForm.email"
                            type="email"
                            class="mt-1 block w-full"
                            autocomplete="username"
                            placeholder="Email address"
                        />
                        <InputError
                            class="mt-2"
                            :message="avatarForm.errors.email"
                        />
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="-mt-4 text-sm text-muted-foreground">
                            Your email address is unverified.
                            <Link
                                :href="send()"
                                as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                            >
                                Click here to resend the verification email.
                            </Link>
                        </p>
                        <div
                            v-if="status === 'verification-link-sent'"
                            class="mt-2 text-sm font-medium text-green-600"
                        >
                            A new verification link has been sent to your email
                            address.
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="password"
                            >New Password
                            <span class="text-xs text-muted-foreground"
                                >(leave blank to keep current)</span
                            ></Label
                        >
                        <Input
                            id="password"
                            v-model="avatarForm.password"
                            type="password"
                            class="mt-1 block w-full"
                            autocomplete="new-password"
                        />
                        <InputError
                            class="mt-2"
                            :message="avatarForm.errors.password"
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password_confirmation"
                            >Confirm New Password</Label
                        >
                        <Input
                            id="password_confirmation"
                            v-model="avatarForm.password_confirmation"
                            type="password"
                            class="mt-1 block w-full"
                            autocomplete="new-password"
                        />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button
                            :disabled="avatarForm.processing"
                            data-test="update-profile-button"
                            >Save</Button
                        >
                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p
                                v-show="avatarForm.recentlySuccessful"
                                class="text-sm text-neutral-600"
                            >
                                Saved.
                            </p>
                        </Transition>
                    </div>
                </form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
