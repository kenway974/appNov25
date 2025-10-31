<?php

namespace App\Command;

use App\Repository\UserNeedRepository;
use App\Service\UserNeedManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-user-need-scores',
    description: 'Diminue le score des UserNeed selon leur priorité',
)]
class UpdateUserNeedScoresCommand extends Command
{
    public function __construct(
        private UserNeedManager $userNeedManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $count = $this->userNeedManager->updateScores();

        $output->writeln(sprintf('%d UserNeed ont été mis à jour.', $count));
        return Command::SUCCESS;
    }
}

