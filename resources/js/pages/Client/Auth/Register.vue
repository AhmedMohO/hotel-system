<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import AvatarUpload from '@/components/AvatarUpload.vue';
import CountrySelect from '@/components/CountrySelect.vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/auth/AuthSplitLayout.vue';
import { login } from '@/routes/client';
import { store } from '@/routes/client/register';

defineProps<{
    countries?: { name: string }[];
}>();
</script>

<template>
    <AuthLayout
        title="Create an account"
        description="Enter your details below to create your account"
    >
        <Head title="Register" />

        <Form
            v-bind="store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            @success="toast.success('Account created successfully!')"
            @error="
                toast.error('Registration failed. Please check your inputs.')
            "
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <div class="flex justify-center">
                    <AvatarUpload
                        name="avatar_image"
                        :error="errors.avatar_image"
                    />
                </div>

                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input
                        id="name"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        name="name"
                        placeholder="Full name"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        :tabindex="2"
                        autocomplete="email"
                        name="email"
                        placeholder="email@example.com"
                    />
                    <InputError :message="errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="mobile">Mobile</Label>
                    <Input
                        id="mobile"
                        type="tel"
                        required
                        :tabindex="3"
                        autocomplete="tel"
                        name="mobile"
                        placeholder="Mobile number"
                    />
                    <InputError :message="errors.mobile" />
                </div>

                <div class="flex gap-4">
                    <div class="grid w-full gap-2">
                        <Label>Country</Label>
                        <CountrySelect
                            name="country"
                            :countries="countries"
                            placeholder="Select country"
                            :required="true"
                            tabindex="4"
                        />
                        <InputError :message="errors.country" />
                    </div>

                    <div class="grid w-full gap-2">
                        <Label>Gender</Label>
                        <Select name="gender" required>
                            <SelectTrigger class="w-full" tabindex="5">
                                <SelectValue placeholder="Select gender" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="Male">Male</SelectItem>
                                <SelectItem value="Female">Female</SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="errors.gender" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <PasswordInput
                        id="password"
                        required
                        :tabindex="6"
                        autocomplete="new-password"
                        name="password"
                        placeholder="Password"
                    />
                    <InputError :message="errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm password</Label>
                    <PasswordInput
                        id="password_confirmation"
                        required
                        :tabindex="7"
                        autocomplete="new-password"
                        name="password_confirmation"
                        placeholder="Confirm password"
                    />
                    <InputError :message="errors.password_confirmation" />
                </div>

                <Button
                    type="submit"
                    class="mt-2 w-full"
                    tabindex="8"
                    :disabled="processing"
                    data-test="register-user-button"
                >
                    <Spinner v-if="processing" />
                    Create account
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink
                    :href="login()"
                    class="underline decoration-muted-foreground/30 underline-offset-4 hover:decoration-current"
                    :tabindex="9"
                    >Log in</TextLink
                >
            </div>
        </Form>
    </AuthLayout>
</template>
