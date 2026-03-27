<script setup lang="ts">
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import * as Rooms from '@/routes/rooms';

const props = defineProps<{ floors: any[] }>();

const form = useForm({
    number:   '',
    floor_id: '',
    capacity: '',
    price:    '',
});

function submit() {
    form.post(Rooms.store.url());
}
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Rooms', href: Rooms.index.url() }, { title: 'Add Room', href: '' }]">
        <div class="p-6">
            <div class="mx-auto max-w-2xl rounded-lg border border-border bg-card p-8 shadow-sm">
                <h1 class="mb-6 text-2xl font-semibold">Add Room</h1>

                <form class="space-y-4" @submit.prevent="submit">
                    <div class="grid gap-2">
                        <Label for="number">Room Number (min 4 digits)</Label>
                        <Input id="number" v-model="form.number" type="text" />
                        <InputError :message="form.errors.number" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="floor_id">Floor</Label>
                        <select v-model="form.floor_id" id="floor_id"
                                class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-ring">
                            <option value="" disabled>Select a floor</option>
                            <option v-for="floor in floors" :key="floor.id" :value="floor.id">
                                {{ floor.name }} (#{{ floor.number }})
                            </option>
                        </select>
                        <InputError :message="form.errors.floor_id" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="capacity">Capacity</Label>
                            <Input id="capacity" v-model="form.capacity" type="number" min="1" />
                            <InputError :message="form.errors.capacity" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="price">Price (in dollars)</Label>
                            <Input id="price" v-model="form.price" type="number" min="0" step="0.01" />
                            <InputError :message="form.errors.price" />
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <Button type="button" variant="outline" @click="router.visit(Rooms.index.url())">Cancel</Button>
                        <Button type="submit" :disabled="form.processing">Create Room</Button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>