<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft } from 'lucide-vue-next';
import { toast } from 'vue-sonner';

import AvatarUpload from '@/components/AvatarUpload.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import ClientNavbarLayout from '@/layouts/ClientNavbarLayout.vue';
import Client from '@/routes/client';

defineOptions({ layout: ClientNavbarLayout });

type ClientProfile = {
    id: number;
    name: string;
    email: string;
    mobile: string | null;
    country: string | null;
    gender: 'Male' | 'Female' | null;
    avatar_image: string | null;
};

const props = defineProps<{
    client: ClientProfile;
}>();

const form = useForm({
    name: props.client.name ?? '',
    email: props.client.email ?? '',
    mobile: props.client.mobile ?? '',
    country: props.client.country ?? '',
    gender: props.client.gender ?? 'Male',
    avatar_image: null as File | null,
    remove_avatar: false,
    password: '',
    password_confirmation: '',
});

function submit() {
    form.transform((data) => ({ ...data, _method: 'PATCH' })).post('/client/profile', {
        forceFormData: true,
        onSuccess: () => {
            toast.success('Profile updated successfully.');
            form.password = '';
            form.password_confirmation = '';
            form.avatar_image = null;
            form.remove_avatar = false;
        },
        onError: () => {
            toast.error('Please review the highlighted fields.');
        },
    });
}
</script>

<template>
    <Head title="Edit Profile" />

    <div class="mx-auto w-full max-w-4xl space-y-6 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">

        <!-- Back link -->
        <Link :href="Client.dashboard.url()" class="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground transition-colors">
            <ArrowLeft class="w-4 h-4" />
            Back to dashboard
        </Link>

        <form class="space-y-6" @submit.prevent="submit">

            <!-- Avatar -->
            <Card>
                <CardHeader>
                    <CardTitle>Profile picture</CardTitle>
                    <CardDescription>Upload a photo to personalize your account</CardDescription>
                </CardHeader>
                <CardContent>
                    <AvatarUpload
                        v-model="form.avatar_image"
                        v-model:removed="form.remove_avatar"
                        :current-avatar="client.avatar_image"
                        :error="form.errors.avatar_image"
                    />
                </CardContent>
            </Card>

            <!-- Personal Info -->
            <Card>
                <CardHeader>
                    <CardTitle>Personal information</CardTitle>
                    <CardDescription>Update your name, contact details, and location</CardDescription>
                </CardHeader>
                <CardContent class="space-y-5">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="name">Full name</Label>
                            <Input id="name" v-model="form.name" type="text" required />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="space-y-2">
                            <Label for="email">Email address</Label>
                            <Input id="email" v-model="form.email" type="email" required />
                            <InputError :message="form.errors.email" />
                        </div>
                        <div class="space-y-2">
                            <Label for="mobile">Mobile number</Label>
                            <Input id="mobile" v-model="form.mobile" type="text" required />
                            <InputError :message="form.errors.mobile" />
                        </div>
                        <div class="space-y-2">
                            <Label for="country">Country</Label>
                            <Input id="country" v-model="form.country" type="text" required />
                            <InputError :message="form.errors.country" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label for="gender">Gender</Label>
                        <select
                            id="gender"
                            v-model="form.gender"
                            class="h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                            required
                        >
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                        <InputError :message="form.errors.gender" />
                    </div>
                </CardContent>
            </Card>

            <!-- Password -->
            <Card>
                <CardHeader>
                    <CardTitle>Change password</CardTitle>
                    <CardDescription>Leave blank to keep your current password</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="password">New password</Label>
                            <Input id="password" v-model="form.password" type="password" autocomplete="new-password" />
                            <InputError :message="form.errors.password" />
                        </div>
                        <div class="space-y-2">
                            <Label for="password_confirmation">Confirm new password</Label>
                            <Input id="password_confirmation" v-model="form.password_confirmation" type="password" autocomplete="new-password" />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Save -->
            <div class="flex justify-end">
                <Button type="submit" :disabled="form.processing" class="min-w-32">
                    {{ form.processing ? 'Saving...' : 'Save changes' }}
                </Button>
            </div>

        </form>
    </div>
</template>