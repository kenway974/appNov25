<?php

namespace App\Controller;

use App\Repository\PlanRepository;
use App\Repository\SubscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SubscriptionController extends AbstractController
{
    #[Route('/subscription', name: 'app_subscription')]
    #[IsGranted('ROLE_USER')]
    public function index(
        PlanRepository $planRepository,
        SubscriptionRepository $subscriptionRepository
    ): Response {
        
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Récupère la souscription de l’utilisateur (si elle existe)
        $subscription = $subscriptionRepository->findOneByUserId($user->getId());

        // Si l’utilisateur a un rôle subscriber → affiche la souscription
        if ($this->isGranted('ROLE_SUBSCRIBER') && $subscription) {
            return $this->render('subscription/show.html.twig', [
                'subscription' => $subscription,
            ]);
        }

        // Sinon, affiche la liste des plans disponibles
        $plans = $planRepository->findAllActive();

        return $this->render('subscription/choose_plan.html.twig', [
            'plans' => $plans,
        ]);
    }
}
