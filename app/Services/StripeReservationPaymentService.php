<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use RuntimeException;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\StripeClient;

class StripeReservationPaymentService
{
    public function __construct(private readonly ReservationPricingService $pricingService)
    {
    }

    /**
     * @param array<string, string|int> $metadata
     *
     * @throws ApiErrorException
     */
    public function createPaymentIntent(int $amountCents, string $currency, array $metadata): PaymentIntent
    {
        return $this->stripe()->paymentIntents->create([
            'amount' => $amountCents,
            'currency' => strtolower($currency),
            'automatic_payment_methods' => ['enabled' => true],
            'metadata' => Arr::map($metadata, fn ($value) => (string) $value),
        ]);
    }

    /**
     * @param array<string, string|int> $expectedMetadata
     *
     * @throws ApiErrorException
     */
    public function assertSuccessfulPaymentIntent(
        string $paymentIntentId,
        int $expectedAmountCents,
        string $expectedCurrency,
        array $expectedMetadata,
    ): PaymentIntent {
        $paymentIntent = $this->stripe()->paymentIntents->retrieve($paymentIntentId, []);

        if ($paymentIntent->status !== 'succeeded') {
            throw new RuntimeException('Payment has not been completed yet.');
        }

        if ((int) $paymentIntent->amount !== $expectedAmountCents) {
            throw new RuntimeException('Payment amount mismatch detected.');
        }

        if (strtolower((string) $paymentIntent->currency) !== strtolower($expectedCurrency)) {
            throw new RuntimeException('Payment currency mismatch detected.');
        }

        foreach ($expectedMetadata as $key => $value) {
            if (($paymentIntent->metadata[$key] ?? null) !== (string) $value) {
                throw new RuntimeException('Payment metadata mismatch detected.');
            }
        }

        if (Cache::has($this->usedPaymentIntentCacheKey($paymentIntentId))) {
            throw new RuntimeException('This payment intent was already used.');
        }

        return $paymentIntent;
    }

    public function markPaymentIntentAsUsed(string $paymentIntentId): void
    {
        Cache::forever($this->usedPaymentIntentCacheKey($paymentIntentId), true);
    }

    public function currency(): string
    {
        return strtolower((string) config('services.stripe.currency', 'usd'));
    }

    public function publishableKey(): string
    {
        return (string) config('services.stripe.key');
    }

    private function stripe(): StripeClient
    {
        $secret = (string) config('services.stripe.secret');

        if ($secret === '') {
            throw new RuntimeException('Stripe secret key is not configured.');
        }

        return new StripeClient($secret);
    }

    private function usedPaymentIntentCacheKey(string $paymentIntentId): string
    {
        return 'reservations:payment_intent_used:'.$paymentIntentId;
    }
}
