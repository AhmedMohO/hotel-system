<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import CountrySelect from '@/components/CountrySelect.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

defineProps<{ countries: Array<{ name: string }> }>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Clients', href: '/dashboard/clients' },
    { title: 'Add Client', href: '/dashboard/clients/create' },
];

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    mobile: '',
    country: '',
    gender: '',
});

function submit() {
    form.post('/dashboard/clients', {
        onSuccess: () => {
            toast.success('Client created successfully.');
            form.reset();
        },
        onError: () => toast.error('Failed to create client. Please check the form.'),
    });
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="mx-auto max-w-2xl overflow-hidden rounded-xl border border-border bg-card shadow-sm">

                <!-- Card Header -->
                <div class="px-6 pt-6 pb-4">
                    <h1 class="text-base font-semibold">Add Client</h1>
                    <p class="mt-0.5 text-sm text-muted-foreground">
                        Create a new pre-approved client account.
                    </p>
                </div>

                <Separator />

                <form class="flex flex-col gap-4 px-6 py-5" @submit.prevent="submit">

                    <!-- Name -->
                    <div class="grid gap-1.5">
                        <Label for="name" class="text-xs font-semibold tracking-wider text-muted-foreground uppercase">
                            Full Name
                        </Label>
                        <Input id="name" v-model="form.name" type="text" placeholder="John Doe" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <!-- Email -->
                    <div class="grid gap-1.5">
                        <Label for="email" class="text-xs font-semibold tracking-wider text-muted-foreground uppercase">
                            Email Address
                        </Label>
                        <Input id="email" v-model="form.email" type="email" placeholder="john@example.com" />
                        <InputError :message="form.errors.email" />
                    </div>

                    <!-- Password -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-1.5">
                            <Label for="password" class="text-xs font-semibold tracking-wider text-muted-foreground uppercase">
                                Password
                            </Label>
                            <Input id="password" v-model="form.password" type="password" placeholder="Min. 8 characters" />
                            <InputError :message="form.errors.password" />
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="password_confirmation" class="text-xs font-semibold tracking-wider text-muted-foreground uppercase">
                                Confirm Password
                            </Label>
                            <Input id="password_confirmation" v-model="form.password_confirmation" type="password" placeholder="Repeat password" />
                        </div>
                    </div>

                    <!-- Mobile -->
                    <div class="grid gap-1.5">
                        <Label for="mobile" class="text-xs font-semibold tracking-wider text-muted-foreground uppercase">
                            Mobile Number
                        </Label>
                        <Input id="mobile" v-model="form.mobile" type="text" placeholder="+1 234 567 8900" />
                        <InputError :message="form.errors.mobile" />
                    </div>

                    <!-- Country & Gender -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-1.5">
                            <Label class="text-xs font-semibold tracking-wider text-muted-foreground uppercase">
                                Country
                            </Label>
                            <CountrySelect v-model="form.country" :countries="countries" />
                            <InputError :message="form.errors.country" />
                        </div>

                        <div class="grid gap-1.5">
                            <Label class="text-xs font-semibold tracking-wider text-muted-foreground uppercase">
                                Gender
                            </Label>
                            <div class="flex gap-6 pt-1">
                                <Label class="flex cursor-pointer items-center gap-2 text-sm font-normal">
                                    <input v-model="form.gender" type="radio" value="Male" class="accent-primary" />
                                    Male
                                </Label>
                                <Label class="flex cursor-pointer items-center gap-2 text-sm font-normal">
                                    <input v-model="form.gender" type="radio" value="Female" class="accent-primary" />
                                    Female
                                </Label>
                            </div>
                            <InputError :message="form.errors.gender" />
                        </div>
                    </div>

                    <Separator />

                    <!-- Footer -->
                    <div class="flex justify-end gap-2">
                        <Button type="button" variant="outline" size="sm" as-child>
                            <Link href="/dashboard/clients">Cancel</Link>
                        </Button>
                        <Button type="submit" size="sm" :disabled="form.processing">
                            <span
                                v-if="form.processing"
                                class="mr-1.5 h-3.5 w-3.5 animate-spin rounded-full border-2 border-current border-t-transparent"
                            />
                            Add Client
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>