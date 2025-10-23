<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Feeling;
use App\Entity\Need;
use App\Entity\Action;
use App\Entity\Block;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class JsonFixturesLoader extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['json'];
    }

    private ObjectManager $em;

    public function load(ObjectManager $manager): void
    {
        $this->em = $manager;

        $path = __DIR__ . '/'; // chemin vers src/DataFixtures/

        $entityMap = [
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

        // Fichiers JSON à charger
        $files = ['feelings', 'needs', 'actions', 'blocks', 'users'];

        foreach ($files as $fileBase) {
            $file = $path . $fileBase . '.json';

            if (!file_exists($file)) {
                echo "[⚠️] $fileBase.json introuvable, skip.\n";
                continue;
            }

            $jsonData = json_decode(file_get_contents($file), true);
            if (!$jsonData) {
                echo "[❌] $fileBase.json est vide ou invalide.\n";
                continue;
            }

            $entityName = ucfirst(rtrim($fileBase, 's'));
            $entityClass = $entityMap[$entityName];

            foreach ($jsonData as $item) {
                if (empty($item['title'])) {
                    echo "[⚠️] Une entrée sans title dans $fileBase.json a été ignorée.\n";
                    continue;
                }

                // Vérifie si l'entité existe déjà en base
                $existing = $repo[$entityName]->findOneBy(['title' => $item['title']]);
                if ($existing) {
                    echo "[ℹ️] $entityName '{$item['title']}' existe déjà, mise à jour.\n";
                    $entity = $existing;
                } else {
                    $entity = new $entityClass();
                }

                // Remplit tous les champs dynamiquement si le setter existe
                foreach ($item as $key => $value) {
                    $setter = 'set' . ucfirst($key);
                    if (method_exists($entity, $setter)) {
                        $entity->$setter($value);
                    }
                }

                $this->em->persist($entity);
            }

            echo "[✅] $fileBase.json chargé avec succès !\n";
        }

        $this->em->flush();
        echo "[🚀] Toutes les entités ont été importées.\n";
    }
}
