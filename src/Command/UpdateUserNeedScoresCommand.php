<?php

namespace App\Command;

use App\Repository\UserNeedRepository;
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
        private EntityManagerInterface $em,
        private UserNeedRepository $userNeedRepo
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $now = new \DateTime();

        $userNeeds = $this->userNeedRepo->findAll();

        foreach ($userNeeds as $userNeed) {
            $last = $userNeed->getLastUpdated();

            $interval = $last->diff($now);
            $days = 3; 
            //$interval->days;

            if ($days >= 1) {
                $decrement = $days * $userNeed->getPriority();
                $newScore = max(0, $userNeed->getScore() - $decrement);

                $userNeed->setScore($newScore);
                $userNeed->setLastUpdated($now);

                $output->writeln(sprintf(
                    'UserNeed #%d: -%d (new score: %d)',
                    $userNeed->getId(),
                    $decrement,
                    $newScore
                ));
            }
        }

        $this->em->flush();

        $output->writeln('Tous les scores ont été mis à jour.');
        return Command::SUCCESS;
    }
}
