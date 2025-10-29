<?php

namespace App\Controller;

use App\Entity\Plan;
use App\Entity\User;
use App\Repository\PlanRepository;
use App\Service\StripePaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    public function __construct(private StripePaymentService $stripePaymentService) {}

    #[Route('/create-checkout-session/{planId}', name:'app_create_checkout_session', methods: ['POST'])]
    public function createCheckoutSession(int $planId, PlanRepository $planRepository, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $plan = $planRepository->find($planId);
        if (!$plan || !$plan->getStripePriceId()) {
            return $this->json(['error' => 'Plan not found or no stripe price configured'], 404);
        }

        $successUrl = $this->generateUrl('app_subscription_success', [], true) . '?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl  = $this->generateUrl('app_subscription_cancel', [], true);

        // Appel du service pour créer la session
        $session = $this->stripePaymentService->createCheckoutSession(
            (float)$plan->getPrice(), // si tu as un champ price pour paiement unique
            $successUrl,
            $cancelUrl
        );

        return $this->json(['id' => $session->id]);
    }

    #[Route('/subscription/success', name:'app_subscription_success', methods:['GET'])]
    public function success(Request $request): Response
    {
        return $this->render('subscription/success.html.twig');
    }

    #[Route('/subscription/cancel', name:'app_subscription_cancel', methods:['GET'])]
    public function cancel(): Response
    {
        return $this->render('subscription/cancel.html.twig');
    }
}
