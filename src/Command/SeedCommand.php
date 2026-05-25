<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Plan;
use App\Entity\Feeling;
use App\Entity\Need;
use App\Entity\Action;
use App\Entity\Block;
use App\Entity\User;

class SeedCommand
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function seed(): void
    {
        $basePath = dirname(__DIR__) . '/DataFixtures';

        $entityMap = [
            'plans'    => Plan::class,
            'feelings' => Feeling::class,
            'needs'    => Need::class,
            'actions'  => Action::class,
            'blocks'   => Block::class,
            'users'    => User::class,
        ];

        $this->loadEntitiesFromJson($basePath, $entityMap);

        $this->em->flush();

        echo "[OK] JSON chargé\n";

        $this->executeSqlFile(
            $basePath . '/relations.sql'
        );

        echo "[OK] SQL exécuté\n";
    }

    private function loadEntitiesFromJson(
        string $basePath,
        array $entityMap
    ): void {
        foreach ($entityMap as $fileName => $entityClass) {

            $filePath = $basePath . '/' . $fileName . '.json';

            if (!file_exists($filePath)) {
                echo "[MISS] $fileName.json\n";
                continue;
            }

            $data = json_decode(
                file_get_contents($filePath),
                true
            );

            if (!$data) {
                echo "[ERR] $fileName.json invalide\n";
                continue;
            }

            foreach ($data as $item) {

                $entity = new $entityClass();

                foreach ($item as $key => $value) {

                    $setter = 'set' . ucfirst($key);

                    if (!method_exists($entity, $setter)) {
                        continue;
                    }

                    if (
                        in_array($key, ['createdAt', 'updatedAt']) &&
                        is_string($value)
                    ) {
                        $value = new \DateTimeImmutable($value);
                    }

                    $entity->$setter($value);
                }

                $this->em->persist($entity);
            }

            echo "[OK] $fileName.json\n";
        }
    }

    private function executeSqlFile(string $filePath): void
    {
        if (!file_exists($filePath)) {
            echo "[MISS] relations.sql\n";
            return;
        }

        $sql = file_get_contents($filePath);

        if (!$sql) {
            echo "[ERR] SQL vide\n";
            return;
        }

        $connection = $this->em->getConnection();

        $queries = array_filter(
            array_map('trim', explode(';', $sql))
        );

        foreach ($queries as $query) {

            if (!empty($query)) {
                $connection->executeStatement($query);
            }
        }
    }
}