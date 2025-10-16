<?php

namespace App\Controller;

use App\Entity\Feeling;
use App\Entity\UserNeed;
use App\Form\UserNeedFormType;
use App\Repository\FeelingRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\FeelingService;
use App\Service\UserNeedManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FeelingController extends AbstractController
{
    #[Route('/feeling', name: 'app_feeling')]
    public function indexFeeling(
        FeelingService $feelingService,
        UserNeedManager $userNeedManager,
        FeelingRepository $feelingRepository,
        Request $request
    ): Response {
        $emotions = ['Tristesse', 'Colère', 'Peur', 'Honte', 'Culpabilité', 'Frustration'];

        $feelingsByEmotion = $feelingService->getFeelingsGroupedByEmotion($emotions, $feelingRepository);
        $allNeeds = $feelingService->getAllNeedsFromFeelings($feelingsByEmotion);

        // ⚡ Gestion du POST manuel
        if ($request->isMethod('POST')) {
            $needId = $request->request->get('need_id');
            $priority = $request->request->get('priority');

            // on retrouve le Need correspondant
            $need = null;
            foreach ($allNeeds as $n) {
                if ($n->getId() == $needId) {
                    $need = $n;
                    break;
                }
            }

            if ($need) {
                $userNeedManager->createUserNeed($this->getUser(), $need, [
                    'priority' => $priority
                ]);

                return $this->redirectToRoute('app_dashboard');
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
