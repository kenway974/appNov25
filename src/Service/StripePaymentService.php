<?php
namespace App\Service;

use Stripe\Checkout\Session;

class StripePaymentService
{
    public function __construct(
        private StripeService $stripeService
    ) {}

    public function createCheckoutSession(string $priceId, string $successUrl, string $cancelUrl): Session
    {
        $client = $this->stripeService->client();

        return $client->checkout->sessions->create([
            'mode' => 'subscription',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => $priceId,
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
