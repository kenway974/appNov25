<?php

namespace App\Controller;

use App\Service\StripeWebhookHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Webhook;

class StripeWebhookController extends AbstractController
{
    public function __construct(
        private string $webhookSecret,
        private StripeWebhookHandler $webhookHandler // Injection du service
    ) {}

    #[Route('/webhook', name: 'app_stripe_webhook', methods: ['POST'])]
    public function handle(Request $request): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('stripe-signature');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $this->webhookSecret);
        } catch (\UnexpectedValueException $e) {
            return new Response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return new Response('Invalid signature', 400);
        }

        // On délègue toute la logique au service
        $this->webhookHandler->handle($event);

        return new Response('OK', 200);
    }
}
