<?php

namespace App\Controller;

use App\Entity\UserAction;
use App\Form\UserActionType;
use App\Form\UserDashboardIllustrationType;
use App\Repository\UserNeedRepository;
use App\Repository\UserActionRepository;
use App\Repository\UserNeedHistoryRepository;
use App\Service\UserActionManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/dashboard')]
final class DashboardController extends AbstractController
{
    #[Route(name: 'app_dashboard', methods: ['GET', 'POST'])]
    public function index(Request $request, CsrfTokenManagerInterface $csrfTokenManager, UserNeedRepository $userNeedRepo, UserActionRepository $userActionRepo, EntityManagerInterface $em, UserActionManager $manager    
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

        $data = $request->request->all('user_action');

        // Traitement du formulaire d'ajout d'action
        if ($request->isMethod('POST') && !empty($data)) {            

            // Vérification CSRF
            $submittedToken = $data['_token'] ?? '';
            if (!$csrfTokenManager->isTokenValid(new CsrfToken('user_action', $submittedToken))) {
                throw $this->createAccessDeniedException('Token CSRF invalide.');
            }

            // Récupération des IDs passés en GET
            $needId = $request->request->get('needId');
            $actionId = $request->request->get('actionId');

            if ($needId && $actionId) {
                // Récupère le UserNeed et l'Action correspondante
                $userNeed = $userNeedRepo->find($needId);
                $action = $userNeed
                    ? $userNeed->getNeed()->getActions()->filter(fn($a) => $a->getId() == $actionId)->first()
                    : null;

                // Lie l'action et le need à UserAction, persiste, appel service et redirige
                if ($userNeed && $action) {
                    $userAction = new UserAction();
                    $userAction->setAction($action);
                    $userAction->setUserNeed($userNeed);

                    $manager->create($user, $userAction, $data);

                    return $this->redirectToRoute('app_dashboard');
                }
            }
        }

        // Récupération des besoins et actions
        $userNeeds = $userNeedRepo->findBy(['user' => $user]);
        $userActions = $userActionRepo->findBy(['user' => $user]);

        return $this->render('dashboard/index.html.twig', [
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
