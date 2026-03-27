<script setup lang="ts">
import { router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import * as Floors from '@/routes/floors';

const form = useForm({ name: '' });

function submit() {
    form.post(Floors.store.url());
}
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Floors', href: Floors.index.url() }, { title: 'Add Floor', href: Floors.create.url() }]">
       <div class="p-6">
    <div class="mx-auto max-w-2xl rounded-lg border border-border bg-card p-8 shadow-sm">
            <h1 class="text-2xl font-semibold">Add Floor</h1>

            <form class="space-y-4" @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="name">Floor Name</Label>
                    <Input id="name" v-model="form.name" placeholder="e.g. Ground Floor" />
                    <InputError :message="form.errors.name" />
                </div>

                <p class="text-sm text-muted-foreground">
                    Floor number is auto-generated and cannot be changed.
                </p>

                <div class="flex justify-end gap-2 pt-2">
                    <Button type="button" variant="outline" @click="router.visit(Floors.index.url())">Cancel</Button>
                    <Button type="submit" :disabled="form.processing">Create Floor</Button>
                </div>
            </form>
        </div>
        </div>
    </AppLayout>
</template>