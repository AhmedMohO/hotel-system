<script setup lang="ts">
import StripePaymentElement from '@/components/StripePaymentElement.vue';
import InputError from '@/components/InputError.vue';
import ClientNavbarLayout from '@/layouts/ClientNavbarLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, ref } from 'vue';

defineOptions({
    layout: ClientNavbarLayout,
});

type Room = {
    id: number;
    number: string;
    capacity: number;
    price: number;
    price_formatted: string;
};

const props = defineProps<{
    room: Room;
    defaults: {
        check_in?: string | null;
        check_out?: string | null;
        accompany_number?: number | string | null;
    };
    stripe: {
        publishable_key: string;
        currency: string;
    };
}>();

const form = useForm({
    check_in: props.defaults.check_in ?? '',
    check_out: props.defaults.check_out ?? '',
    accompany_number: props.defaults.accompany_number ? Number(props.defaults.accompany_number) : 1,
    payment_intent_id: '',
});

const paymentClientSecret = ref('');
const quoteLoading = ref(false);
const paymentError = ref('');
const stripeProcessing = ref(false);

const nights = computed(() => {
    if (!form.check_in || !form.check_out) {
        return 0;
    }

    const start = new Date(form.check_in);
    const end = new Date(form.check_out);
    const diffMs = end.getTime() - start.getTime();

    if (diffMs <= 0) {
        return 0;
    }

    return Math.floor(diffMs / (1000 * 60 * 60 * 24));
});

const totalPriceCents = computed(() => nights.value * props.room.price);
const totalPriceFormatted = computed(() => (totalPriceCents.value / 100).toFixed(2));

async function initializePayment() {
    paymentError.value = '';
    quoteLoading.value = true;
    paymentClientSecret.value = '';
    form.payment_intent_id = '';

    try {
        const response = await axios.post(`/client/reservations/rooms/${props.room.id}/payment-intent`, {
            check_in: form.check_in,
            check_out: form.check_out,
            accompany_number: form.accompany_number,
        });

        paymentClientSecret.value = response.data.client_secret;
        form.payment_intent_id = response.data.payment_intent_id;
    } catch (error: any) {
        const responseErrors = error?.response?.data?.errors;

        if (responseErrors) {
            form.setError(responseErrors);
            paymentError.value = Object.values(responseErrors).flat().join(' ');
        } else {
            paymentError.value = error?.response?.data?.message ?? 'Unable to initialize payment.';
        }
    } finally {
        quoteLoading.value = false;
    }
}

function onPaymentSucceeded(paymentIntentId: string) {
    form.payment_intent_id = paymentIntentId;

    form.post(`/client/reservations/rooms/${props.room.id}`, {
        onError: () => {
            paymentError.value = form.errors.payment_intent_id ?? 'Unable to complete reservation.';
        },
    });
}
</script>

<template>
    <Head :title="`Reserve Room ${room.number}`" />

    <div class="mx-auto w-full max-w-4xl space-y-6 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
        <Card>
            <CardHeader>
                <CardTitle class="text-2xl">Reserve Room {{ room.number }}</CardTitle>
            </CardHeader>
            <CardContent class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-md border p-4">
                    <p class="text-xs text-muted-foreground sm:text-sm">Price per night</p>
                    <p class="text-xl font-semibold sm:text-2xl">${{ room.price_formatted }}</p>
                </div>
                <div class="rounded-md border p-4">
                    <p class="text-xs text-muted-foreground sm:text-sm">Capacity</p>
                    <p class="text-xl font-semibold sm:text-2xl">{{ room.capacity }} guests</p>
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Reservation Details</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="space-y-2">
                        <Label for="check-in" class="text-sm">Check-in</Label>
                        <Input id="check-in" v-model="form.check_in" type="date" required />
                        <InputError :message="form.errors.check_in" />
                    </div>

                    <div class="space-y-2">
                        <Label for="check-out" class="text-sm">Check-out</Label>
                        <Input id="check-out" v-model="form.check_out" type="date" required />
                        <InputError :message="form.errors.check_out" />
                    </div>

                    <div class="space-y-2">
                        <Label for="accompany" class="text-sm">Accompany Number</Label>
                        <Input id="accompany" v-model.number="form.accompany_number" type="number" min="1" :max="room.capacity" required />
                        <InputError :message="form.errors.accompany_number" />
                    </div>
                </div>

                <div class="rounded-md border p-4">
                    <p class="text-sm text-muted-foreground">Total nights: {{ nights }}</p>
                    <p class="text-lg font-semibold sm:text-xl">Total price: ${{ totalPriceFormatted }}</p>
                </div>

                <Button type="button" class="w-full" :disabled="quoteLoading || stripeProcessing" @click="initializePayment">
                    {{ quoteLoading ? 'Preparing Payment...' : 'Pay & Reserve' }}
                </Button>

                <p v-if="paymentError" class="text-sm text-destructive">{{ paymentError }}</p>
                <InputError :message="form.errors.payment_intent_id" />
            </CardContent>
        </Card>

        <Card v-if="paymentClientSecret">
            <CardHeader>
                <CardTitle>Payment</CardTitle>
            </CardHeader>
            <CardContent>
                <StripePaymentElement
                    :publishable-key="stripe.publishable_key"
                    :client-secret="paymentClientSecret"
                    button-text="Confirm Payment"
                    @processing="(state) => (stripeProcessing = state)"
                    @error="(message) => (paymentError = message)"
                    @succeeded="onPaymentSucceeded"
                />
            </CardContent>
        </Card>
    </div>
</template>

