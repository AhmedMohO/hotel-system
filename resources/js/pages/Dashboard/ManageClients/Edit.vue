<template>
    <div class="min-h-screen bg-slate-50">
        <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-8 flex items-center gap-4">
                <Link
                    href="/dashboard/clients"
                    class="rounded-xl border border-transparent p-2 text-slate-500 transition-all hover:border-slate-200 hover:bg-white hover:text-slate-800"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 19l-7-7 7-7"
                        />
                    </svg>
                </Link>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                    Edit Client Profile
                </h1>
            </div>

            <form
                @submit.prevent="submit"
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >
                <div class="px-8 pt-14 pb-8">
                    <div class="mb-8">
                        <h2 class="text-lg font-bold text-slate-900">
                            Personal Information
                        </h2>
                        <p class="text-sm text-slate-500">
                            Update the client details and contact information.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label
                                class="mb-1.5 block text-xs font-medium tracking-wider text-slate-400 uppercase"
                                >Full Name</label
                            >
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full rounded-xl border bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-800 transition focus:border-slate-400 focus:bg-white focus:ring-4 focus:ring-slate-100 focus:outline-none"
                                :class="
                                    form.errors.name
                                        ? 'border-red-400 ring-red-50'
                                        : 'border-slate-100'
                                "
                            />
                            <p
                                v-if="form.errors.name"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <div class="sm:col-span-2">
                            <label
                                class="mb-1.5 block text-xs font-medium tracking-wider text-slate-400 uppercase"
                                >Email Address</label
                            >
                            <input
                                v-model="form.email"
                                type="email"
                                class="w-full rounded-xl border bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-800 transition focus:border-slate-400 focus:bg-white focus:ring-4 focus:ring-slate-100 focus:outline-none"
                                :class="
                                    form.errors.email
                                        ? 'border-red-400 ring-red-50'
                                        : 'border-slate-100'
                                "
                            />
                            <p
                                v-if="form.errors.email"
                                class="mt-1 text-xs text-red-500"
                            >
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="mb-1.5 block text-xs font-medium tracking-wider text-slate-400 uppercase"
                                >Mobile</label
                            >
                            <input
                                v-model="form.mobile"
                                type="text"
                                class="w-full rounded-xl border bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-800 transition focus:border-slate-400 focus:bg-white focus:ring-4 focus:ring-slate-100 focus:outline-none"
                                :class="
                                    form.errors.mobile
                                        ? 'border-red-400 ring-red-50'
                                        : 'border-slate-100'
                                "
                            />
                        </div>

                        <div>
                            <label
                                class="mb-1.5 block text-xs font-medium tracking-wider text-slate-400 uppercase"
                                >Country</label
                            >
                            <input
                                v-model="form.country"
                                type="text"
                                class="w-full rounded-xl border bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-800 transition focus:border-slate-400 focus:bg-white focus:ring-4 focus:ring-slate-100 focus:outline-none"
                                :class="
                                    form.errors.country
                                        ? 'border-red-400 ring-red-50'
                                        : 'border-slate-100'
                                "
                            />
                        </div>

                        <div>
                            <label
                                class="mb-1.5 block text-xs font-medium tracking-wider text-slate-400 uppercase"
                                >Gender</label
                            >
                            <select
                                v-model="form.gender"
                                class="w-full rounded-xl border bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-800 transition focus:border-slate-400 focus:bg-white focus:ring-4 focus:ring-slate-100 focus:outline-none"
                                :class="
                                    form.errors.gender
                                        ? 'border-red-400 ring-red-50'
                                        : 'border-slate-100'
                                "
                            >
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                    </div>

                    <div
                        class="mt-10 flex items-center justify-end gap-3 border-t border-slate-100 pt-8"
                    >
                        <Link
                            href="/dashboard/clients"
                            class="rounded-xl bg-slate-100 px-6 py-2.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-200"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-xl bg-slate-900 px-8 py-2.5 text-sm font-semibold text-white transition-all hover:bg-slate-800 hover:shadow-lg active:scale-95 disabled:bg-slate-400"
                        >
                            {{ form.processing ? 'Saving...' : 'Save Changes' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

const props = defineProps({ client: Object });

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
