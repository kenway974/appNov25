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
        //$subscription = $subscriptionRepository->findBy(['user' => $user]);
        $subscription = $user->getSubscription();

        // Sinon, affiche la liste des plans disponibles
        $plans = $planRepository->findPaidPlans();

        return $this->render('subscription/plan.html.twig', [
            'plans' => $plans,
            'subscription' => $subscription,
            'user' => $user,
        ]);
    }
}
