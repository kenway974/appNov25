<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Plan;
use App\Entity\Feeling;
use App\Entity\Need;
use App\Entity\Action;
use App\Entity\Block;
use App\Entity\User;

class JsonFixturesLoader extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['json'];
    }

    public function load(ObjectManager $manager): void
    {
        /** @var EntityManagerInterface $em */
        $em = $manager;

        $basePath = __DIR__;

        $entityMap = [
            'plans'    => Plan::class,
            'feelings' => Feeling::class,
            'needs'    => Need::class,
            'actions'  => Action::class,
            'blocks'   => Block::class,
            'users'    => User::class,
        ];

        // 1. JSON
        $this->loadEntitiesFromJson($em, $basePath, $entityMap);

        $em->flush();

        echo "[OK] JSON chargé\n";

        // 2. SQL relations
        $this->executeSqlFile($em, __DIR__ . '/relations.sql');

        echo "[OK] SQL exécuté\n";
    }

    private function loadEntitiesFromJson(
        EntityManagerInterface $em,
        string $basePath,
        array $entityMap
    ): void {
        foreach ($entityMap as $fileName => $entityClass) {

            $filePath = $basePath . '/' . $fileName . '.json';

            if (!file_exists($filePath)) {
                echo "[MISS] $fileName.json\n";
                continue;
            }

            $data = json_decode(file_get_contents($filePath), true);

            if (!$data) {
                echo "[ERR] $fileName.json invalide\n";
                continue;
            }

            foreach ($data as $item) {

                if (empty($item['title'])) {
                    continue;
                }

                $entity = $em->getRepository($entityClass)
                    ->findOneBy(['title' => $item['title']])
                    ?? new $entityClass();

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

                $em->persist($entity);
            }

            echo "[OK] $fileName.json\n";
        }
    }

    private function executeSqlFile(EntityManagerInterface $em, string $filePath): void
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

        $connection = $em->getConnection();

        $queries = array_filter(array_map('trim', explode(';', $sql)));

        foreach ($queries as $query) {
            if (!empty($query)) {
                $connection->executeStatement($query);
            }
        }
    }
}