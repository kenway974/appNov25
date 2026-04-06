<?php

namespace App\Command;

use App\Repository\UserActionRepository;
use App\Service\NotificationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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

        // Récupère les actions concernées
        $actions = $this->actionRepo->findPastDeadlines($now);

        $notifCount = 0;

        foreach ($actions as $action) {

            $user = $action->getUser();

            if (!$user) {
                continue;
            }

            $actionTitle = $action->getAction()?->getTitle() ?? 'Action';

            // 🔔 Création de la notification
            try {
                $this->notificationService->create(
                    $user,
                    'Deadline dépassée',
                    sprintf('L\'action "%s" a dépassé sa deadline.', $action->getAction()?->getTitle()),
                    'deadline',
                    $action
                );

                $notifCount++;

            } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
                // déjà notifié → on ignore
                continue;
            }
        }

        $output->writeln(sprintf('%d notifications créées', $notifCount));

        return Command::SUCCESS;
    }
}