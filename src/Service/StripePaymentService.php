<?php
namespace App\Service;

use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;

class StripePaymentService
{
    public function __construct(
        private StripeService $stripeService
    ) {}

    public function createSubscriptionSession(
        string $priceId,
        string $successUrl,
        string $cancelUrl,
        ?string $customerEmail = null,
        array $metadata = []
    ): Session {
        return $this->createCheckoutSession(
            'subscription',
            $priceId,
            $successUrl,
            $cancelUrl,
            $customerEmail,
            $metadata
        );
    }

    public function createPaymentSession(
        string $priceId,
        string $successUrl,
        string $cancelUrl,
        ?string $customerEmail = null,
        array $metadata = []
    ): Session {
        return $this->createCheckoutSession(
            'payment',
            $priceId,
            $successUrl,
            $cancelUrl,
            $customerEmail,
            $metadata
        );
    }

    private function createCheckoutSession(
        string $mode,
        string $priceId,
        string $successUrl,
        string $cancelUrl,
        ?string $customerEmail = null,
        array $metadata = []
    ): Session {
        $this->assertValidUrl($successUrl);
        $this->assertValidUrl($cancelUrl);

        $client = $this->stripeService->client();

        $params = [
            'mode' => $mode,
            'line_items' => [[
                'price' => $priceId,
                'quantity' => 1,
            ]],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'metadata' => $metadata,
        ];

        if ($customerEmail) {
            $params['customer_email'] = $customerEmail;
        }

        try {
            return $client->checkout->sessions->create($params);
        } catch (ApiErrorException $e) {
            // log ici si besoin (Monolog par exemple)
            throw new \RuntimeException('Stripe error: ' . $e->getMessage(), 0, $e);
        }
    }

    private function assertValidUrl(string $url): void
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException("Invalid URL: $url");
        }
    }

    public function getPublicKey(): string
    {
        return $this->stripeService->getPublicKey();
    }
}