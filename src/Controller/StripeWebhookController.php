<?php

namespace App\Controller;

use App\Service\StripeWebhookHandler;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Webhook;

class StripeWebhookController extends AbstractController
{
    public function __construct(
        private string $webhookSecret,
        private StripeWebhookHandler $webhookHandler,
        private LoggerInterface $logger
    ) {}

    #[Route('/webhook', name: 'app_stripe_webhook', methods: ['POST'])]
    public function handle(Request $request): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('stripe-signature');

        $this->logger->info('Payload Stripe brut : ' . $request->getContent());

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $this->webhookSecret);
        } catch (\UnexpectedValueException $e) {
            $this->logger->warning('Stripe webhook: invalid payload');
            return new Response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            $this->logger->warning('Stripe webhook: invalid signature');
            return new Response('Invalid signature', 400);
        }

        try {
            //$this->logger->info("Stripe webhook called successfully: {$event->type}");
            $this->webhookHandler->handle($event);
            $this->logger->info("Stripe webhook processed successfully: {$event->type}");
            return new Response('OK', 200);
        } catch (\Throwable $e) {
            $this->logger->error('Stripe webhook failed: ' . $e->getMessage(), [
                'exception' => $e,
                'event_type' => $event->type,
            ]);
            return new Response('Internal error', 500);
        }
    }
}