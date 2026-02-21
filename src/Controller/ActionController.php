<?php

namespace App\Controller;

use App\Entity\UserAction;
use App\Form\UserActionFormType;
use App\Form\UserActionType;
use App\Repository\ActionRepository;
use App\Repository\UserActionRepository;
use App\Repository\UserNeedRepository;
use App\Security\Voter\UserActionVoter;
use App\Service\ActionService;
use App\Service\UserActionManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request; // <- correction ici
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/action')]
final class ActionController extends AbstractController
{ 
    #[Route(name: 'app_action')]
    public function index(ActionService $actionService): Response
    {
       
        $actionsByType = $actionService->getActionsByType();
        $actionsByIntension = $actionService->getActionsByIntension();
        $instantActions = $actionService->getInstantActions(true);
        $delayedActions = $actionService->getDelayedActions(true);

        return $this->render('action/index.html.twig', [
            'controller_name'   => 'ActionController',
            'actionsByType'     => $actionsByType,
            'actionsByIntension'=> $actionsByIntension,
            'instantActions'     => $instantActions,
            'delayedActions'     => $delayedActions,
        ]);
    }

    #[Route('/read/{id}', name: 'app_action_read', requirements: ['id' => '\d+'])]
    public function readAction(int $id, ActionRepository $actionRepo): Response
    {
        $action = $actionRepo->find($id);

        if (!$action) {
            throw $this->createNotFoundException('Action non trouvée');
        }

        return $this->render('action/read.html.twig', [
            'action' => $action,
        ]);
    }

/*
    #[Route('/add-action/{id}', name: 'app_action_add', requirements: ['id' => '\d+'])]
    public function addAction(
        int $id, Request $request, ActionRepository $actionRepo, UserNeedRepository $userNeedRepo, UserActionManager $manager, SessionInterface $session
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            throw new AccessDeniedException('Vous devez être connecté.');
        }

        // Récupère l'action (entité fixe) pour la lier a userAction
        $action = $actionRepo->find($id);

        if (!$action) {
            throw $this->createNotFoundException("Action non trouvée");
        }

        // Récupération de l'id userNeed depuis la session
        $userNeedId = $session->get('selected_user_need_id');

        $userNeed = null;
        if ($userNeedId) {
            $userNeed = $userNeedRepo->find($userNeedId);
        }

        // Check que ce soit le bon user
        if ($userNeed && $userNeed->getUser() !== $user) {
            throw new AccessDeniedException('Accès interdit à ce besoin utilisateur.');
        }

        // lie l'action et le need à un UserAction (entité liée à l'utilisateur)
        $userAction = new UserAction();
        $userAction->setAction($action);
        $userAction->setUserNeed($userNeed);

        $form = $this->createForm(UserActionFormType::class, $userAction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->create($this->getUser(), $userAction); // appel service
            $this->addFlash('success', 'Action ajoutée à votre profil.');
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('user_action/_form.html.twig', [
            'form' => $form->createView(),
            'action' => $action,
            'userNeed' => $userNeed,
        ]);
    }*/

    #[Route('/complete/{id}', name: 'app_action_complete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function completeUserAction(int $id, Request $request, UserActionManager $manager, UserActionRepository $userActionRepo
    ): Response {
        $userAction = $userActionRepo->find($id);

        // vérifie que useraction existe et appartient à l'utilisateur
        if (!$userAction) {
            throw $this->createNotFoundException('UserAction non trouvée');
        } 

        $this->denyAccessUnlessGranted(
            UserActionVoter::COMPLETE,
            $userAction
        );

        // verifie csrf
        if (!$this->isCsrfTokenValid(
            'complete_user_action_' . $userAction->getId(),
            $request->request->get('_token')
            )) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $manager->completeUserAction($userAction); // appel service

        $this->addFlash('success', 'Action mise à jour avec succès.');

        return $this->redirectToRoute('app_dashboard');
    }


    #[Route('/set-selected-user-action', name: 'set_selected_user_action', methods: ['POST'])]
    public function setSelectedUserAction(Request $request, SessionInterface $session, CsrfTokenManagerInterface $csrf): Response
    {
        $token = $request->headers->get('X-CSRF-TOKEN');

        if (!$csrf->isTokenValid(new CsrfToken('set_user_action', $token))) {
            return new Response('Invalid CSRF token', 403);
        }

        $data = json_decode($request->getContent(), true);
        $userNeedId = $data['userNeedId'] ?? null;
        $actionId = $data['actionId'] ?? null;
        $type = $data['type'] ?? null;

        if (!$userNeedId || !$actionId || !$type) {
            return new Response('Missing data', 400);
        }

        // Stockage en session
        $session->set('selected_user_action', [
            'userNeedId' => $userNeedId,
            'actionId' => $actionId,
            'type' => $type
        ]);

        return new Response("UserNeed $userNeedId - Action $actionId ($type) enregistré");
    }

}
