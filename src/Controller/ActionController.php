<?php

namespace App\Controller;

use App\Entity\UserAction;
use App\Form\UserActionType;
use App\Repository\ActionRepository;
use App\Repository\UserActionRepository;
use App\Repository\UserNeedRepository;
use App\Service\ActionService;
use App\Service\UserActionManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; // <- correction ici
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
 
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


    #[Route('/add-action/{id}', name: 'app_action_add', requirements: ['id' => '\d+'])]
    public function addAction(
        int $id,
        Request $request,
        ActionRepository $actionRepo,
        UserNeedRepository $userNeedRepo,
        UserActionManager $manager,
        SessionInterface $session
    ): Response {
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
        
        dump($action);
        dd($userNeed);

        $userAction = new UserAction();
        $userAction->setAction($action);
        $userAction->setUserNeed($userNeed);

        $form = $this->createForm(UserActionType::class, $userAction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->create($this->getUser(), $userAction);
            $this->addFlash('success', 'Action ajoutée à votre profil.');
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('user_action/_form.html.twig', [
            'form' => $form->createView(),
            'action' => $action,
            'userNeed' => $userNeed,
        ]);
    }

    #[Route('/complete/{id}', name: 'app_action_complete', requirements: ['id' => '\d+'], methods: ['POST', 'GET'])]
    public function completeUserAction(
        int $id,
        UserActionManager $manager,
        UserActionRepository $userActionRepo
    ): Response {
        $userAction = $userActionRepo->findById($id); // je suppose que tu as une méthode find dans ton manager
        if (!$userAction) {
            throw $this->createNotFoundException('UserAction non trouvée');
        }

        $manager->completeUserAction($userAction);

        $this->addFlash('success', 'Action mise à jour avec succès.');

        return $this->redirectToRoute('app_dashboard'); // ou autre route où tu veux rediriger
    }

}
