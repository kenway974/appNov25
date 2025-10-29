<?php
namespace App\Service;

use Stripe\Checkout\Session;

class StripePaymentService
{
    public function __construct(
        private StripeService $stripeService
    ) {}

    public function createCheckoutSession(float $amount, string $successUrl, string $cancelUrl): Session
    {
        $client = $this->stripeService->client();
        $amountInCents = (int) ($amount * 100);

        return $client->checkout->sessions->create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => ['name' => 'Paiement Abonnement Premium'],
                    'unit_amount' => $amountInCents,
                ],
                'quantity' => 1,
            ]],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
        ]);
    }

    public function getPublicKey(): string
    {
        return $this->stripeService->getPublicKey();
    }
}
