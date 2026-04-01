<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { onBeforeUnmount, ref, watch } from 'vue';

type Props = {
    publishableKey: string;
    clientSecret: string;
    returnUrl?: string;
    buttonText?: string;
};

const props = withDefaults(defineProps<Props>(), {
    returnUrl: undefined,
    buttonText: 'Pay now',
});

const emit = defineEmits<{
    (event: 'processing', value: boolean): void;
    (event: 'error', value: string): void;
    (event: 'succeeded', paymentIntentId: string): void;
}>();

const containerId = `stripe-payment-element-${Math.random().toString(36).slice(2)}`;
const ready = ref(false);
const processing = ref(false);

let stripe: any = null;
let elements: any = null;
let paymentElement: any = null;

function ensureStripeScript(): Promise<void> {
    return new Promise((resolve, reject) => {
        if ((window as any).Stripe) {
            resolve();
            return;
        }

        const existingScript = document.querySelector('script[data-stripe-js="true"]') as HTMLScriptElement | null;

        if (existingScript) {
            existingScript.addEventListener('load', () => resolve(), { once: true });
            existingScript.addEventListener('error', () => reject(new Error('Unable to load Stripe.js.')), { once: true });
            return;
        }

        const script = document.createElement('script');
        script.src = 'https://js.stripe.com/v3';
        script.async = true;
        script.dataset.stripeJs = 'true';
        script.onload = () => resolve();
        script.onerror = () => reject(new Error('Unable to load Stripe.js.'));
        document.head.appendChild(script);
    });
}

async function initializeElements() {
    if (!props.publishableKey || !props.clientSecret) {
        return;
    }

    ready.value = false;
    destroyElement();

    await ensureStripeScript();

    const stripeFactory = (window as any).Stripe;

    if (!stripeFactory) {
        emit('error', 'Unable to initialize Stripe.');
        return;
    }

    stripe = stripeFactory(props.publishableKey);

    elements = stripe.elements({
        clientSecret: props.clientSecret,
        appearance: {
            theme: 'stripe',
        },
    });

    paymentElement = elements.create('payment');
    paymentElement.mount(`#${containerId}`);

    ready.value = true;
}

function destroyElement() {
    paymentElement?.unmount();
    paymentElement = null;
    elements = null;
}

async function confirmPayment() {
    if (!stripe || !elements) {
        emit('error', 'Payment form is not ready yet.');
        return;
    }

    processing.value = true;
    emit('processing', true);

    const result = await stripe.confirmPayment({
        elements,
        confirmParams: props.returnUrl ? { return_url: props.returnUrl } : undefined,
        redirect: 'if_required',
    });

    processing.value = false;
    emit('processing', false);

    if (result.error) {
        emit('error', result.error.message ?? 'Payment failed.');
        return;
    }

    if (result.paymentIntent?.status === 'succeeded' && result.paymentIntent.id) {
        emit('succeeded', result.paymentIntent.id);
        return;
    }

    emit('error', 'Payment did not complete successfully.');
}

watch(
    () => props.clientSecret,
    async (newSecret) => {
        if (newSecret) {
            try {
                await initializeElements();
            } catch (error: any) {
                emit('error', error?.message ?? 'Unable to initialize payment form.');
            }
        }
    },
    { immediate: true },
);

onBeforeUnmount(() => {
    destroyElement();
});
</script>

<template>
    <div class="space-y-4">
        <div :id="containerId" class="rounded-md border p-4" />

        <Button type="button" class="w-full" :disabled="!ready || processing" @click="confirmPayment">
            {{ buttonText }}
        </Button>
    </div>
</template>

