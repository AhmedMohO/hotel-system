<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import CountrySelect from '@/components/CountrySelect.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({ client: Object, countries: Array });

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Clients', href: '/dashboard/clients' },
    { title: 'Edit Client', href: '' },
];

const form = useForm({
    name: props.client.name,
    email: props.client.email,
    mobile: props.client.mobile,
    country: props.client.country,
    gender: props.client.gender,
    _method: 'PUT',
});

function submit() {
    form.post(`/dashboard/clients/${props.client.id}`, {
        preserveScroll: true,
        onSuccess: () => toast.success('Client updated successfully.'),
        onError: () => toast.error('Failed to update client. Please check the form.'),
    });
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="mx-auto max-w-2xl overflow-hidden rounded-xl border border-border bg-card shadow-sm">

                <!-- Card Header -->
                <div class="px-6 pt-6 pb-4">
                    <h1 class="text-base font-semibold">Edit Client</h1>
                    <p class="mt-0.5 text-sm text-muted-foreground">
                        Updating profile for <span class="font-medium text-foreground">{{ client.name }}</span>.
                    </p>
                </div>

                <Separator />

                <form class="flex flex-col gap-4 px-6 py-5" @submit.prevent="submit">

                    <!-- Name -->
                    <div class="grid gap-1.5">
                        <Label for="name" class="text-xs font-semibold tracking-wider text-muted-foreground uppercase">
                            Full Name
                        </Label>
                        <Input id="name" v-model="form.name" type="text" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <!-- Email -->
                    <div class="grid gap-1.5">
                        <Label for="email" class="text-xs font-semibold tracking-wider text-muted-foreground uppercase">
                            Email Address
                        </Label>
                        <Input id="email" v-model="form.email" type="email" />
                        <InputError :message="form.errors.email" />
                    </div>

                    <!-- Mobile & Country -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-1.5">
                            <Label for="mobile" class="text-xs font-semibold tracking-wider text-muted-foreground uppercase">
                                Mobile
                            </Label>
                            <Input id="mobile" v-model="form.mobile" type="text" />
                            <InputError :message="form.errors.mobile" />
                        </div>

                        <div class="grid gap-1.5">
                            <Label class="text-xs font-semibold tracking-wider text-muted-foreground uppercase">
                                Country
                            </Label>
                            <CountrySelect v-model="form.country" :countries="countries" />
                        </div>
                    </div>

                    <!-- Gender -->
                    <div class="grid gap-1.5">
                        <Label class="text-xs font-semibold tracking-wider text-muted-foreground uppercase">
                            Gender
                        </Label>
                        <select
                            v-model="form.gender"
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-ring"
                        >
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                        <InputError :message="form.errors.gender" />
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
                            Save Changes
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>