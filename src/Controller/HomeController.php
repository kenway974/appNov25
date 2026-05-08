<?php

namespace App\Controller;

use App\Repository\PlanRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PlanRepository $planRepository): Response
    {
        $paidPlans = $planRepository->findPaidPlans();

        $freePlans = $planRepository->createQueryBuilder('p')
            ->andWhere('p.price = 0')
            ->getQuery()
            ->getResult();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'paidPlans' => $paidPlans,
            'freePlans' => $freePlans,
        ]);
    }
}