<?php

namespace App\Command;

use App\Repository\UserActionRepository;
use App\Service\NotificationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\Action;

#[AsCommand(name: 'app:check-deadlines')]
class CheckDeadlinesCommand extends Command
{
    public function __construct(
        private UserActionRepository $actionRepo,
        private NotificationService $notificationService
    ) { 
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $now = new \DateTimeImmutable();

        // Récupère toutes les actions dont la deadline est passée et qui n'ont pas encore de notification
        $actions = $this->actionRepo->findPastDeadlines($now);

        $notifCount = 0;

        foreach ($actions as $action) {
            // $action doit avoir un userId et un titre
            $userId = $action->getUser()->getId();
            $actionTitle = $action->getAction()->getTitle();

            // Création de la notification via le service
            $notification = $this->notificationService->createDeadlineNotification(
                $userId,
                $action->getId(),
                $actionTitle
            );

            // Si une notification a effectivement été créée (pas de doublon)
            if ($notification !== null) {
                $notifCount++;
            }
        }

        $output->writeln(sprintf('%d notifications créées', $notifCount));

        return Command::SUCCESS;
    }
}