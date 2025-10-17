<?php
namespace App\Service;

use Stripe\StripeClient;

class StripeService
{
    private StripeClient $client;
    private string $publicKey;

    public function __construct(string $secretKey, string $publicKey)
    {
        $this->client = new StripeClient($secretKey);
        $this->publicKey = $publicKey;
    }

    public function client(): StripeClient
    {
        return $this->client;
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }
}
