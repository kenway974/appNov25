<?php
namespace App\Controller;

use App\Entity\Subscription;
use App\Repository\PlanRepository;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Subscription as StripeSubscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeWebhookController extends AbstractController
{
    public function __construct(private string $webhookSecret) {}

    #[Route('/webhook', name:'app_stripe_webhook', methods:['POST'])]
    public function handle(
        Request $request,
        EntityManagerInterface $em,
        SubscriptionRepository $subscriptionRepository,
        PlanRepository $planRepository
    ): Response {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('stripe-signature');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $this->webhookSecret);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return new Response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return new Response('Invalid signature', 400);
        }

        $type = $event->type;

        switch ($type) {
            case 'checkout.session.completed':
                // When checkout creates a subscription
                $session = $event->data->object;
                if (!empty($session->subscription)) {
                    // retrieve subscription from stripe to get details
                    $stripe = new \Stripe\StripeClient(getenv('STRIPE_SECRET_KEY'));
                    $stripeSub = $stripe->subscriptions->retrieve($session->subscription, []);
                    $this->createOrUpdateLocalSubscriptionFromStripe($stripeSub, $em, $subscriptionRepository, $planRepository);
                }
                break;

            case 'customer.subscription.created':
            case 'customer.subscription.updated':
                $stripeSub = $event->data->object;
                $this->createOrUpdateLocalSubscriptionFromStripe($stripeSub, $em, $subscriptionRepository, $planRepository);
                break;

            case 'customer.subscription.deleted':
                $stripeSub = $event->data->object;
                $local = $subscriptionRepository->findOneBy(['stripeSubscriptionId' => $stripeSub->id]);
                if ($local) {
                    $local->setIsActive(false);
                    $local->setStatus($stripeSub->status ?? 'canceled');
                    $em->persist($local);
                    $em->flush();
                }
                break;

            case 'invoice.payment_failed':
                // handle failed payment: notify user or mark past_due
                $invoice = $event->data->object;
                $stripeSubId = $invoice->subscription ?? null;
                if ($stripeSubId) {
                    $local = $subscriptionRepository->findOneBy(['stripeSubscriptionId' => $stripeSubId]);
                    if ($local) {
                        $local->setStatus('past_due');
                        $em->persist($local);
                        $em->flush();
                    }
                }
                break;

            case 'invoice.paid':
                $invoice = $event->data->object;
                $stripeSubId = $invoice->subscription ?? null;
                if ($stripeSubId) {
                    $local = $subscriptionRepository->findOneBy(['stripeSubscriptionId' => $stripeSubId]);
                    if ($local) {
                        $local->setStatus('active');
                        $em->persist($local);
                        $em->flush();
                    }
                }
                break;

            default:
                // log unprocessed events if desired
                break;
        }

        return new Response('OK', 200);
    }

    private function createOrUpdateLocalSubscriptionFromStripe($stripeSub, EntityManagerInterface $em, $subscriptionRepository, PlanRepository $planRepository)
    {
        // Try to find by stripe subscription id
        $local = $subscriptionRepository->findOneBy(['stripeSubscriptionId' => $stripeSub->id]);

        // Find user via metadata if available
        $userId = $stripeSub->metadata['user_id'] ?? null;
        $planId = $stripeSub->metadata['plan_id'] ?? null;

        // If not, try invoice/customer lookups (left as extension)

        if (!$local) {
            // create new local subscription
            $local = new Subscription();
            if ($userId) {
                // load user reference
                $user = $em->getRepository(\App\Entity\User::class)->find($userId);
                if ($user) {
                    $local->setUser($user);
                }
            }
        }

        // set plan if we have planId
        if ($planId) {
            $plan = $planRepository->find($planId);
            if ($plan) {
                $local->setPlan($plan);
            }
        }

        $local->setStripeSubscriptionId($stripeSub->id);
        $local->setStripePriceId($stripeSub->items->data[0]->price->id ?? null);
        $local->setStatus($stripeSub->status ?? null);

        // convert timestamps
        if (!empty($stripeSub->current_period_start)) {
            $local->setStartDate((new \DateTime())->setTimestamp($stripeSub->current_period_start));
        }
        if (!empty($stripeSub->current_period_end)) {
            $local->setEndDate((new \DateTime())->setTimestamp($stripeSub->current_period_end));
        }
        $local->setIsRecurring($stripeSub->cancel_at_period_end ? false : true);
        $local->setIsActive(in_array($stripeSub->status, ['active', 'trialing']));

        $now = new \DateTimeImmutable();
        $local->setUpdatedAt($now);
        if (!$local->getCreatedAt()) {
            $local->setCreatedAt($now);
        }

        $em->persist($local);
        $em->flush();
    }
}
