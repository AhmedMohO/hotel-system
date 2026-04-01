<script setup lang="ts">
import AvatarUpload from '@/components/AvatarUpload.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import ClientNavbarLayout from '@/layouts/ClientNavbarLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

defineOptions({
    layout: ClientNavbarLayout,
});

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
        <Card>
            <CardHeader>
                <CardTitle>Client Profile</CardTitle>
            </CardHeader>
            <CardContent>
                <form class="space-y-6" @submit.prevent="submit">
                    <div class="space-y-3">
                        <Label>Avatar</Label>
                        <AvatarUpload
                            v-model="form.avatar_image"
                            v-model:removed="form.remove_avatar"
                            :current-avatar="client.avatar_image"
                            :error="form.errors.avatar_image"
                        />
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="name">Name</Label>
                            <Input id="name" v-model="form.name" type="text" required />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="space-y-2">
                            <Label for="email">Email</Label>
                            <Input id="email" v-model="form.email" type="email" required />
                            <InputError :message="form.errors.email" />
                        </div>

                        <div class="space-y-2">
                            <Label for="mobile">Mobile</Label>
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

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="password">New Password (optional)</Label>
                            <Input id="password" v-model="form.password" type="password" />
                            <InputError :message="form.errors.password" />
                        </div>

                        <div class="space-y-2">
                            <Label for="password_confirmation">Confirm Password</Label>
                            <Input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                            />
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <Button type="submit" :disabled="form.processing">
                            {{ form.processing ? 'Saving...' : 'Save Changes' }}
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>


