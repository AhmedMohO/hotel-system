<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import * as Floors from '@/routes/floors';
import { toast } from 'vue-sonner';

const props = defineProps<{ floor: any }>();

const form = useForm({ name: props.floor.name });

// function submit() {
//     form.put(Floors.update.url(props.floor.id));
// }


function submit() {
    form.put(Floors.update.url(props.floor.id), {
        onSuccess: () => {
            toast.success('Floor updated successfully!');
            // router.visit(Floors.index.url());
        },
        onError: () => toast.error('Please fix the errors below.'),
    });
}
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Floors', href: Floors.index.url() }, { title: 'Edit Floor', href: Floors.edit.url(props.floor.id) }]">
        <div class="p-6">
    <div class="mx-auto max-w-2xl rounded-lg border border-border bg-card p-8 shadow-sm">
            <h1 class="text-2xl font-semibold">Edit Floor</h1>

            <form class="space-y-4" @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label>Floor Number (immutable)</Label>
                    <Input :value="floor.number" disabled class="bg-muted text-muted-foreground" />
                </div>

                <div class="grid gap-2">
                    <Label for="name">Floor Name</Label>
                    <Input id="name" v-model="form.name" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <Button type="button" variant="outline" @click="router.visit(Floors.index.url())">Cancel</Button>
                    <Button type="submit" :disabled="form.processing">Save Changes</Button>
                </div>
            </form>
        </div>
        </div>
    </AppLayout>
</template>