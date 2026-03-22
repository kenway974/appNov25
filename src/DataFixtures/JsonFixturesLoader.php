<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use App\Entity\Plan;
use App\Entity\Feeling;
use App\Entity\Need;
use App\Entity\Action;
use App\Entity\Block;
use App\Entity\User;

class JsonFixturesLoader extends Fixture implements FixtureGroupInterface
{
    private ObjectManager $em;

    public static function getGroups(): array
    {
        return ['json'];
    }

    public function load(ObjectManager $manager): void
    {
        $this->em = $manager;
        $path = __DIR__ . '/'; // chemin vers src/DataFixtures/

        // -----------------------------
        // Mapping entités et repositories
        // -----------------------------
        $entityMap = [
            'Plan'    => Plan::class,
            'Feeling' => Feeling::class,
            'Need'    => Need::class,
            'Action'  => Action::class,
            'Block'   => Block::class,
            'User'    => User::class
        ];

        $repo = [];
        foreach ($entityMap as $name => $class) {
            $repo[$name] = $this->em->getRepository($class);
        }

        // -----------------------------
        // Chargement des entités depuis JSON
        // -----------------------------
        $this->loadEntitiesFromFiles($repo, $path, ['plans', 'feelings', 'needs', 'actions', 'blocks', 'users']);

        $this->em->flush();
        echo "[🚀] Toutes les entités et ont été importées avec succès.\n";
    }

    // -----------------------------
    // Charge les entités depuis un tableau de fichiers JSON
    // -----------------------------
    private function loadEntitiesFromFiles(array $repo, string $path, array $files): void
    {
        foreach ($files as $fileBase) {
            // trouve chaque fichier JSON correspondant
            $file = $path . $fileBase . '.json';
            if (!file_exists($file)) {
                echo "[⚠️] $fileBase.json introuvable, skip.\n";
                continue;
            }

            // lit et décode le JSON
            $jsonData = json_decode(file_get_contents($file), true);
            if (!$jsonData) {
                echo "[❌] $fileBase.json est vide ou invalide.\n";
                continue;
            }

            // trouve la classe d'entité correspondante
            $entityName  = ucfirst(rtrim($fileBase, 's'));
            $entityClass = $repo[$entityName]->getClassName() ?? null;

            if (!$entityClass) continue;

            // crée entités à partir des données JSON
            foreach ($jsonData as $item) {
                if (empty($item['title'])) {
                    echo "[⚠️] Une entrée sans title dans $fileBase.json a été ignorée.\n";
                    continue;
                }

                $entity = $repo[$entityName]->findOneBy(['title' => $item['title']]) ?? new $entityClass();

                // remplit tous les champs avec les setters
                foreach ($item as $key => $value) {
                    $setter = 'set' . ucfirst($key);
                    if (method_exists($entity, $setter)) {
                        if (in_array($key, ['createdAt', 'updatedAt']) && is_string($value)) {
                        $value = new \DateTimeImmutable($value);}
                        $entity->$setter($value);
                    }
                }

                $this->em->persist($entity);
            }

            echo "[✅] $fileBase.json chargé avec succès !\n";
        }
    }
}