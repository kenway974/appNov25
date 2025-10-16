<?php 

namespace App\Command;

use App\Service\SubscriptionManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeactivateExpiredSubscriptionsCommand extends Command
{
    protected static $defaultName = 'app:deactivate-expired-subscriptions';
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
