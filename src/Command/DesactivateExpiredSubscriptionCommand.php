<?php 

namespace App\Command;

use App\Service\SubscriptionManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:desactivate-expired-subscription', description: 'Désactive les subscriptions expirées')]
class DesactivateExpiredSubscriptionCommand extends Command
{
    private SubscriptionManager $subscriptionManager;

    public function __construct(SubscriptionManager $subscriptionManager)
    {
        parent::__construct();
        $this->subscriptionManager = $subscriptionManager;
    }

    protected function configure()
    {
        $this->setDescription('Désactive les abonnements expirés.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $count = $this->subscriptionManager->deactivateExpiredSubscriptions();
        $output->writeln("$count abonnement(s) désactivé(s).");

        return Command::SUCCESS;
    }
}
