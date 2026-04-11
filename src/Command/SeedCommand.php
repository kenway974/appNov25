<?php

namespace App\Command;

use App\Service\DataSeeder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:seed')]
class SeedCommand extends Command
{
    private DataSeeder $seeder;

    public function __construct(DataSeeder $seeder)
    {
        parent::__construct();
        $this->seeder = $seeder;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->seeder->seed();
        return Command::SUCCESS;
    }
}