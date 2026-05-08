<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PlanRepository;
use App\Service\StripePaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    public function __construct(private StripePaymentService $stripePaymentService) {}

    #[Route('/create-checkout-session', name:'app_create_checkout_session', methods: ['POST'])]
    public function createCheckoutSession(PlanRepository $planRepository, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $planId = $request->request->get('planId');

        if (!$planId) {
            return $this->json(['error' => 'Missing planId'], 400);
        }

        $plan = $planRepository->find($planId);

        if (!$plan) {
            return $this->json(['error' => 'Plan not found'], 404);
        }

        $successUrl = $this->generateUrl(
            'app_subscription_success',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        ) . '?session_id={CHECKOUT_SESSION_ID}';

        $cancelUrl = $this->generateUrl(
            'app_subscription_cancel',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $priceId = $plan->getStripePriceId();

        if ($plan->getDuration() > 24) {
            $session = $this->stripePaymentService->createPaymentSession(
                $priceId,
                $successUrl,
                $cancelUrl,
                $user->getEmail(),
                [
                    'user_id' => $user->getId(),
                    'plan_id' => $plan->getId()
                ]
            );
        } else {
            $session = $this->stripePaymentService->createSubscriptionSession(
                $priceId,
                $successUrl,
                $cancelUrl,
                $user->getEmail(),
                [
                    'user_id' => $user->getId(),
                    'plan_id' => $plan->getId()
                ]
            );
        }

        // ✅ IMPORTANT : redirection vers Stripe
        return $this->redirect($session->url);
    }

    #[Route('/subscription/success', name:'app_subscription_success', methods:['GET'])]
    public function success(): Response
    {
        return $this->render('subscription/success.html.twig');
    }

    #[Route('/subscription/cancel', name:'app_subscription_cancel', methods:['GET'])]
    public function cancel(): Response
    {
        return $this->render('subscription/cancel.html.twig');
    }
}