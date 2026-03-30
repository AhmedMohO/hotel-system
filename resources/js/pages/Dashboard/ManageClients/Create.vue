<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-screen bg-slate-50">
            <div class="mx-auto max-w-2xl px-4 py-8 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1
                        class="text-2xl font-bold tracking-tight text-slate-900"
                    >
                        Add Client
                    </h1>
                    <p class="mt-1 text-sm text-slate-500">
                        Create a new pre-approved client account
                    </p>
                </div>

                <!-- Form Card -->
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm"
                >
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Name -->
                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-slate-700"
                                >Full Name</label
                            >
                            <input
                                v-model="form.name"
                                type="text"
                                placeholder="John Doe"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 transition outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                            />
                            <p
                                v-if="form.errors.name"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-slate-700"
                                >Email Address</label
                            >
                            <input
                                v-model="form.email"
                                type="email"
                                placeholder="john@example.com"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 transition outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                            />
                            <p
                                v-if="form.errors.email"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <!-- Password -->
                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-slate-700"
                                >Password</label
                            >
                            <input
                                v-model="form.password"
                                type="password"
                                placeholder="Min. 8 characters"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 transition outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                            />
                            <p
                                v-if="form.errors.password"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-slate-700"
                                >Confirm Password</label
                            >
                            <input
                                v-model="form.password_confirmation"
                                type="password"
                                placeholder="Repeat password"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 transition outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                            />
                        </div>

                        <!-- Mobile -->
                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-slate-700"
                                >Mobile Number</label
                            >
                            <input
                                v-model="form.mobile"
                                type="text"
                                placeholder="+1 234 567 8900"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 transition outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                            />
                            <p
                                v-if="form.errors.mobile"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.mobile }}
                            </p>
                        </div>

                        <!-- Country -->
                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-slate-700"
                                >Country</label
                            >
                            <input
                                v-model="form.country"
                                type="text"
                                placeholder="United States"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 transition outline-none focus:border-slate-400 focus:ring-2 focus:ring-slate-100"
                            />
                            <p
                                v-if="form.errors.country"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.country }}
                            </p>
                        </div>

                        <!-- Gender -->
                        <div>
                            <label
                                class="mb-1.5 block text-sm font-medium text-slate-700"
                                >Gender</label
                            >
                            <div class="flex gap-6">
                                <label
                                    class="flex cursor-pointer items-center gap-2 text-sm text-slate-700"
                                >
                                    <input
                                        v-model="form.gender"
                                        type="radio"
                                        value="Male"
                                        class="accent-slate-900"
                                    />
                                    Male
                                </label>
                                <label
                                    class="flex cursor-pointer items-center gap-2 text-sm text-slate-700"
                                >
                                    <input
                                        v-model="form.gender"
                                        type="radio"
                                        value="Female"
                                        class="accent-slate-900"
                                    />
                                    Female
                                </label>
                            </div>
                            <p
                                v-if="form.errors.gender"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.gender }}
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-3 pt-2">
                            <Link
                                href="/dashboard/clients"
                                class="flex-1 rounded-xl border border-slate-200 px-4 py-2.5 text-center text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="flex-1 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-slate-800 disabled:opacity-60"
                            >
                                {{ form.processing ? 'Adding…' : 'Add Client' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Manage Clients', href: '/dashboard/clients' },
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
