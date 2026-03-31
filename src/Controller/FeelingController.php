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

/*
    #[Route('/feeling', name: 'app_feeling')]
    public function indexFeeling(
        FeelingService $feelingService,
        UserNeedManager $userNeedManager, // <-- nouveau service
        FeelingRepository $feelingRepository,
        NeedRepository $needRepository,
        Request $request
    ): Response {
        $emotions = ['Tristesse', 'Colère', 'Peur'];

        $feelingsByEmotion = $feelingService->getFeelingsGroupedByEmotion($emotions, $feelingRepository);
        $allNeeds = $feelingService->getAllNeedsFromFeelings($feelingsByEmotion);

        //dump($feelingsByEmotion);
        //dd($allNeeds);

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
*/


    #[Route('/feeling', name: 'app_feeling', methods: ['GET', 'POST'])]
    public function indexFeeling(
        FeelingService $feelingService,
        UserNeedManager $userNeedManager,
        FeelingRepository $feelingRepository,
        NeedRepository $needRepository,
        Request $request
    ): Response {

        // Liste des émotions à afficher
        $emotions = ['Tristesse', 'Colère', 'Stress', 'Anxiété', 'Comportement'];

        // Récupération des feelings groupés par émotion
        $feelingsByEmotion = $feelingService->getFeelingsGroupedByEmotion($emotions, $feelingRepository);

        // Récupération de tous les besoins associés aux feelings
        $allNeeds = $feelingService->getAllNeedsFromFeelings($feelingsByEmotion);

        // Traitement du formulaire uniquement si requête POST et utilisateur connecté
        if ($request->isMethod('POST') && $this->getUser()) {

            // Récupération des données envoyées en POST
            $needId = (int) $request->request->get('need_id');
            $priority = (int) $request->request->get('priority');

            // Vérification des valeurs (priority entre 1 et 10)
            if ($needId > 0 && $priority >= 1 && $priority <= 10) {

                // Recherche du besoin en base
                $need = $needRepository->find($needId);

                if ($need) {

                    // Création de l'association User-Need avec priorité
                    $userNeedManager->createUserNeed(
                        $this->getUser(),
                        $need,
                        ['priority' => $priority]
                    );

                    // Redirection après succès (Post/Redirect/Get pattern)
                    return $this->redirectToRoute('app_dashboard');

                } else {
                    // Cas où le besoin n'existe pas
                    $this->addFlash('error', 'Besoin introuvable.');
                }

            } else {
                // Cas où les valeurs envoyées sont invalides
                $this->addFlash('error', 'Valeurs invalides.');
            }
        }

        // Affichage de la page en GET ou en cas d'erreur
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
