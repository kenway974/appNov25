<?php

namespace App\Controller;

use App\Entity\Feeling;
use App\Entity\UserNeed;
use App\Form\UserNeedFormType;
use App\Repository\FeelingRepository;
use App\Repository\NeedRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\FeelingService;
use App\Service\UserNeedManager;
use App\Service\UserNeedService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FeelingController extends AbstractController
{
#[Route('/feeling', name: 'app_feeling')]
    public function indexFeeling(
        FeelingService $feelingService,
        UserNeedManager $userNeedManager, // <-- nouveau service
        FeelingRepository $feelingRepository,
        NeedRepository $needRepository,
        Request $request
    ): Response {
        $emotions = ['Tristesse', 'Colère', 'Peur', 'Anxiété'];

        $feelingsByEmotion = $feelingService->getFeelingsGroupedByEmotion($emotions, $feelingRepository);
        $allNeeds = $feelingService->getAllNeedsFromFeelings($feelingsByEmotion);

        if ($request->isMethod('POST') && $this->getUser()) {
            $needId = (int) $request->request->get('need_id');
            $priority = (int) $request->request->get('priority');

            if ($needId > 0 && $priority >= 1 && $priority <= 5) {

                $need = $needRepository->find($needId);

                if ($need) {
                    $userNeedManager->createUserNeed($this->getUser(), $need, [
                        'priority' => $priority
                    ]);

                    return $this->redirectToRoute('app_dashboard');
                } else {
                    $this->addFlash('error', 'Besoin introuvable.');
                }
            } else {
                $this->addFlash('error', 'Valeurs invalides.');
            }
        }


        return $this->render('feeling/index.html.twig', [
            'feelingsByEmotion' => $feelingsByEmotion,
            'allNeeds' => $allNeeds,
        ]);
    }


    #[Route('/feeling/{id}/read', name: 'app_feeling_read')]
    public function read(Feeling $feeling): Response
    {
        return $this->render('feeling/_read.html.twig', [
            'feeling' => $feeling,
        ]);
    }
}
