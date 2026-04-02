<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { ArrowLeft, CreditCard, Users } from 'lucide-vue-next';
import { computed, ref } from 'vue';

import InputError from '@/components/InputError.vue';
import StripePaymentElement from '@/components/StripePaymentElement.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    CardDescription,
} from '@/components/ui/card';
import { DatePicker } from '@/components/ui/date-picker';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import ClientNavbarLayout from '@/layouts/ClientNavbarLayout.vue';
import Client from '@/routes/client';

defineOptions({ layout: ClientNavbarLayout });

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
    accompany_number: props.defaults.accompany_number
        ? Number(props.defaults.accompany_number)
        : 1,
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
const totalPriceFormatted = computed(() =>
    (totalPriceCents.value / 100).toFixed(2),
);

async function initializePayment() {
    paymentError.value = '';
    quoteLoading.value = true;
    paymentClientSecret.value = '';
    form.payment_intent_id = '';

    try {
        const response = await axios.post(
            `/client/reservations/rooms/${props.room.id}/payment-intent`,
            {
                check_in: form.check_in,
                check_out: form.check_out,
                accompany_number: form.accompany_number,
            },
        );
        paymentClientSecret.value = response.data.client_secret;
        form.payment_intent_id = response.data.payment_intent_id;
    } catch (error: any) {
        const responseErrors = error?.response?.data?.errors;

        if (responseErrors) {
            form.setError(responseErrors);
            paymentError.value = Object.values(responseErrors).flat().join(' ');
        } else {
            paymentError.value =
                error?.response?.data?.message ??
                'Unable to initialize payment.';
        }
    } finally {
        quoteLoading.value = false;
    }
}

function onPaymentSucceeded(paymentIntentId: string) {
    form.payment_intent_id = paymentIntentId;
    form.post(`/client/reservations/rooms/${props.room.id}`, {
        onError: () => {
            paymentError.value =
                form.errors.payment_intent_id ??
                'Unable to complete reservation.';
        },
    });
}
</script>

<template>
    <Head :title="`Reserve Room ${room.number}`" />

    <div
        class="mx-auto w-full max-w-4xl space-y-6 px-4 py-6 sm:px-6 lg:px-8 lg:py-8"
    >
        <!-- Back link -->
        <Link
            :href="Client.reservations.index.url()"
            class="inline-flex items-center gap-1.5 text-sm text-muted-foreground transition-colors hover:text-foreground"
        >
            <ArrowLeft class="h-4 w-4" />
            Back to rooms
        </Link>

        <!-- Room Summary -->
        <Card>
            <CardHeader>
                <CardTitle>Room {{ room.number }}</CardTitle>
                <CardDescription
                    >Review room details before booking</CardDescription
                >
            </CardHeader>
            <CardContent>
                <div class="grid grid-cols-2 gap-3">
                    <div
                        class="rounded-lg border border-border bg-muted/40 p-4"
                    >
                        <div class="mb-1 flex items-center gap-2">
                            <CreditCard class="h-4 w-4 text-muted-foreground" />
                            <p class="text-xs text-muted-foreground">
                                Price per night
                            </p>
                        </div>
                        <p class="text-2xl font-semibold text-foreground">
                            ${{ room.price_formatted }}
                        </p>
                    </div>
                    <div
                        class="rounded-lg border border-border bg-muted/40 p-4"
                    >
                        <div class="mb-1 flex items-center gap-2">
                            <Users class="h-4 w-4 text-muted-foreground" />
                            <p class="text-xs text-muted-foreground">
                                Capacity
                            </p>
                        </div>
                        <p class="text-2xl font-semibold text-foreground">
                            {{ room.capacity }} guests
                        </p>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Reservation Form -->
        <Card>
            <CardHeader>
                <CardTitle>Reservation details</CardTitle>
                <CardDescription
                    >Choose your dates and number of guests</CardDescription
                >
            </CardHeader>
            <CardContent class="space-y-5">
                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="flex flex-col space-y-2">
                        <Label for="check-in" class="text-sm">Check-in</Label>
                        <DatePicker
                            id="check-in"
                            v-model="form.check_in"
                            placeholder="Pick a date"
                            required
                        />
                        <InputError :message="form.errors.check_in" />
                    </div>
                    <div class="flex flex-col space-y-2">
                        <Label for="check-out" class="text-sm">Check-out</Label>
                        <DatePicker
                            id="check-out"
                            v-model="form.check_out"
                            placeholder="Pick a date"
                            :min-value="form.check_in"
                            required
                        />
                        <InputError :message="form.errors.check_out" />
                    </div>
                    <div class="space-y-2">
                        <Label for="accompany" class="text-sm"
                            >Number of guests</Label
                        >
                        <Input
                            id="accompany"
                            v-model.number="form.accompany_number"
                            type="number"
                            min="1"
                            :max="room.capacity"
                            required
                        />
                        <InputError :message="form.errors.accompany_number" />
                    </div>
                </div>

                <!-- Price Summary -->
                <div
                    class="space-y-2 rounded-lg border border-border bg-muted/40 p-4"
                >
                    <div
                        class="flex justify-between text-sm text-muted-foreground"
                    >
                        <span
                            >{{ nights }} night{{ nights !== 1 ? 's' : '' }} ×
                            ${{ room.price_formatted }}</span
                        >
                        <span>${{ totalPriceFormatted }}</span>
                    </div>
                    <div
                        class="flex justify-between border-t border-border pt-2 font-medium text-foreground"
                    >
                        <span>Total</span>
                        <span>${{ totalPriceFormatted }}</span>
                    </div>
                </div>

                <Button
                    type="button"
                    class="w-full gap-2"
                    :disabled="quoteLoading || stripeProcessing || nights === 0"
                    @click="initializePayment"
                >
                    <CreditCard class="h-4 w-4" />
                    {{
                        quoteLoading ? 'Preparing payment...' : 'Pay & reserve'
                    }}
                </Button>

                <p v-if="paymentError" class="text-sm text-destructive">
                    {{ paymentError }}
                </p>
                <InputError :message="form.errors.payment_intent_id" />
            </CardContent>
        </Card>

        <!-- Stripe Payment -->
        <Card v-if="paymentClientSecret">
            <CardHeader>
                <CardTitle>Payment</CardTitle>
                <CardDescription
                    >Complete your secure payment below</CardDescription
                >
            </CardHeader>
            <CardContent>
                <StripePaymentElement
                    :publishable-key="stripe.publishable_key"
                    :client-secret="paymentClientSecret"
                    button-text="Confirm payment"
                    @processing="(state) => (stripeProcessing = state)"
                    @error="(message) => (paymentError = message)"
                    @succeeded="onPaymentSucceeded"
                />
            </CardContent>
        </Card>
    </div>
</template>
