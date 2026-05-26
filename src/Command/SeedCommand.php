<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use App\Entity\Plan;
use App\Entity\Feeling;
use App\Entity\Need;
use App\Entity\Action;
use App\Entity\Block;
use App\Entity\User;

#[AsCommand(
    name: 'app:seed',
    description: 'Charge les fixtures JSON et exécute relations.sql'
)]
class SeedCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $basePath = dirname(__DIR__) . '/DataFixtures';

        $entityMap = [
            'plans'    => Plan::class,
            'feelings' => Feeling::class,
            'needs'    => Need::class,
            'actions'  => Action::class,
            'blocks'   => Block::class,
            'users'    => User::class,
        ];

        $this->loadEntitiesFromJson($basePath, $entityMap, $io);
        $this->em->flush();
        $io->success('JSON chargé');

        $this->executeSqlFile($basePath . '/relations.sql', $io);
        $io->success('SQL exécuté');

        return Command::SUCCESS;
    }

    private function loadEntitiesFromJson(string $basePath, array $entityMap, SymfonyStyle $io): void
    {
        foreach ($entityMap as $fileName => $entityClass) {

            $filePath = $basePath . '/' . $fileName . '.json';

            if (!file_exists($filePath)) {
                $io->warning("$fileName.json introuvable — ignoré");
                continue;
            }

            $data = json_decode(file_get_contents($filePath), true);

            if (!$data) {
                $io->error("$fileName.json invalide");
                continue;
            }

            foreach ($data as $item) {
                $entity = new $entityClass();

                foreach ($item as $key => $value) {
                    $setter = 'set' . ucfirst($key);

                    if (!method_exists($entity, $setter)) {
                        continue;
                    }

                    if (in_array($key, ['createdAt', 'updatedAt']) && is_string($value)) {
                        $value = new \DateTimeImmutable($value);
                    }

                    $entity->$setter($value);
                }

                $this->em->persist($entity);
            }

            $io->text("[OK] $fileName.json");
        }
    }

    private function executeSqlFile(string $filePath, SymfonyStyle $io): void
    {
        if (!file_exists($filePath)) {
            $io->warning('relations.sql introuvable — ignoré');
            return;
        }

        $sql = file_get_contents($filePath);

        if (!$sql) {
            $io->warning('relations.sql vide');
            return;
        }

        $connection = $this->em->getConnection();
        $queries    = array_filter(array_map('trim', explode(';', $sql)));

        foreach ($queries as $query) {
            if (!empty($query)) {
                $connection->executeStatement($query);
            }
        }
    }
}
