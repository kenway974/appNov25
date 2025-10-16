<?php

namespace App\Controller;

use App\Entity\{Need, UserNeed};
use App\Form\UserNeedFormType;
use App\Repository\NeedRepository;
use App\Repository\UserNeedRepository;
use App\Service\NeedService;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Forwarding\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class NeedController extends AbstractController
{
    #[Route('/need', name: 'app_need')]
    public function indexNeed(NeedService $needService, NeedRepository $needRepository): Response
    {
        // Récupération des besoins groupés par type
        $needsByType = $needService->getNeedsGroupedByType($needRepository);

        // Récupération de toutes les actions associées aux besoins
        $allActions = $needService->getAllActionsFromNeeds($needsByType);

        return $this->render('need/index.html.twig', [
            'controller_name' => 'NeedController',
            'needsByType' => $needsByType,
            'allActions' => $allActions,
        ]);
    }

    /*
    #[Route('/add-need/{id}', name: 'app_need_add')]
    public function addNeed(
        Request $request,
        Need $need,
        EntityManagerInterface $em,
        Security $security,
        UserNeedRepository $userNeedRepo
    ): Response {
        $user = $security->getUser();

        if ($userNeedRepo->findOneBy(['user' => $user, 'need' => $need])) {
            $this->addFlash('warning', 'Ce besoin est déjà suivi.');
            return $this->redirectToRoute('app_user_needs');
        }

        $userNeed = new UserNeed();
        $userNeed->setUser($user);
        $userNeed->setNeed($need);

        $form = $this->createForm(UserNeedFormType::class, $userNeed);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($userNeed);
            $em->flush();

            $this->addFlash('success', 'Besoin ajouté avec priorité personnalisée.');
            return $this->redirectToRoute('app_user_needs');
        }

        return $this->render('need/add.html.twig', [
            'need' => $need,
            'form' => $form->createView(),
        ]);
    }*/

}
