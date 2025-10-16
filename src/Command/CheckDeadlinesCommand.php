<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserActionRepository;
use App\Service\NotificationService;
use Symfony\Bundle\SecurityBundle\Security;
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
        $actions = $this->actionRepo->findPastDeadlinesWithoutNotification($now);

        foreach ($actions as $action) {
            $this->notificationService->createNotification($action);
        }

        $output->writeln(sprintf('%d notifications créées', count($actions)));

        return Command::SUCCESS;
    }
}
