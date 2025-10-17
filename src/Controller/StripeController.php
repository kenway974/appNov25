<?php

namespace App\Controller;

use App\Entity\Plan;
use App\Entity\User;
use App\Repository\PlanRepository;
use App\Repository\SubscriptionRepository;
use App\Service\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    #[Route('/create-checkout-session/{planId}', name:'app_create_checkout_session', methods: ['POST'])]
    public function createCheckoutSession(
        int $planId,
        PlanRepository $planRepository,
        StripeService $stripeService,
        EntityManagerInterface $em,
        Request $request
    ): JsonResponse {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $plan = $planRepository->find($planId);
        if (!$plan || !$plan->getStripePriceId()) {
            return $this->json(['error' => 'Plan not found or no stripe price configured'], 404);
        }

        $stripe = $stripeService->client();

        // Create or retrieve Stripe Customer
        if (!$user->getStripeCustomerId()) {
            $customer = $stripe->customers->create([
                'email' => $user->getEmail(),
                'metadata' => ['app_user_id' => $user->getId()]
            ]);
            $user->setStripeCustomerId($customer->id);
            $em->persist($user);
            $em->flush();
        } else {
            $customer = $stripe->customers->retrieve($user->getStripeCustomerId(), []);
        }

        // Create Checkout Session (mode=subscription)
        $session = $stripe->checkout->sessions->create([
            'customer' => $customer->id,
            'mode' => 'subscription',
            'line_items' => [[
                'price' => $plan->getStripePriceId(),
                'quantity' => 1
            ]],
            'subscription_data' => [
                'metadata' => [
                    'user_id' => $user->getId(),
                    'plan_id' => $plan->getId()
                ]
            ],
            'success_url' => $this->generateUrl('app_subscription_success', [], true) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $this->generateUrl('app_subscription_cancel', [], true)
        ]);

        return $this->json(['id' => $session->id]);
    }

    #[Route('/subscription/success', name:'app_subscription_success', methods:['GET'])]
    public function success(Request $request): Response
    {
        // session_id disponible si besoin : $request->query->get('session_id')
        return $this->render('subscription/success.html.twig');
    }

    #[Route('/subscription/cancel', name:'app_subscription_cancel', methods:['GET'])]
    public function cancel(): Response
    {
        return $this->render('subscription/cancel.html.twig');
    }
}
