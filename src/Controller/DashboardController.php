<?php

namespace App\Controller;

use App\Entity\UserAction;
use App\Form\UserActionType;
use App\Form\UserDashboardIllustrationType;
use App\Repository\UserNeedRepository;
use App\Repository\UserActionRepository;
use App\Repository\UserNeedHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard')]
final class DashboardController extends AbstractController
{
    #[Route(name: 'app_dashboard', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        UserNeedRepository $userNeedRepo,
        UserActionRepository $userActionRepo,
        EntityManagerInterface $em
    ): Response {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Form global pour l'utilisateur
        $dashboardForm = $this->createForm(UserDashboardIllustrationType::class, $user);
        $dashboardForm->handleRequest($request);

        if ($dashboardForm->isSubmitted() && $dashboardForm->isValid()) {
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('app_dashboard');
        }

        // Traitement des UserActions envoyées via <form> HTML
        if ($request->isMethod('POST') && $request->request->has('user_action')) {
            $data = $request->request->get('user_action');

            // Récupération des IDs passés en GET (si tu les ajoutes dans l'action du form)
            $needId = $request->query->get('needId');
            $actionId = $request->query->get('actionId');

            if ($needId && $actionId) {
                $userNeed = $userNeedRepo->find($needId);
                $action = $userNeed ? $userNeed->getNeed()->getActions()->filter(fn($a) => $a->getId() == $actionId)->first() : null;

                if ($userNeed && $action) {
                    $userAction = new UserAction();
                    $userAction->setUser($user);
                    $userAction->setAction($action);

                    // Champs personnalisés
                    $userAction->setIsRecurring(isset($data['isRecurring']) && $data['isRecurring'] == 1);
                    $userAction->setDeadline($data['deadline'] ? new \DateTime($data['deadline']) : null);
                    $userAction->setStartDate($data['startDate'] ? new \DateTime($data['startDate']) : null);
                    $userAction->setFrequency($data['frequency'] ?? null);

                    $em->persist($userAction);
                    $em->flush();

                    return $this->redirectToRoute('app_dashboard');
                }
            }
        }

        // Récupération des besoins et actions
        $userNeeds = $userNeedRepo->findBy(['user' => $user]);
        $userActions = $userActionRepo->findBy(['user' => $user]);

        return $this->render('user_dashboard/index.html.twig', [
            'user' => $user,
            'userNeeds' => $userNeeds,
            'userActions' => $userActions,
            'form' => $dashboardForm->createView(),
        ]);
    }







    #[Route('/set-user-need', name: 'set_selected_user_need', methods: ['POST'])]
    public function setSelectedUserNeed(Request $request): Response
    {
        $session = $request->getSession();
        $data = json_decode($request->getContent(), true);

        if (!empty($data['id'])) {
            $session->set('selected_user_need_id', (int)$data['id']);
            return new Response((string) $data['id']);
        }

        return new Response('Aucun ID reçu', 400);
    }

}
