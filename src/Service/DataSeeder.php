<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Plan;
use App\Entity\Feeling;
use App\Entity\Need;
use App\Entity\Action;
use App\Entity\Block;
use App\Entity\User;

class DataSeeder
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function seed(): void
    {
        // 🔥 CONDITION : ne seed que si DB vide
        if ($this->em->getRepository(Plan::class)->count([]) > 0) {
            echo "[Seeder] DB déjà remplie, skip.\n";
            return;
        }

        echo "[Seeder] Initialisation des données...\n";

        $path = __DIR__ . '/../DataFixtures/';

        $entityMap = [
            'Plan'    => Plan::class,
            'Feeling' => Feeling::class,
            'Need'    => Need::class,
            'Action'  => Action::class,
            'Block'   => Block::class,
            'User'    => User::class
        ];

        foreach (['plans', 'feelings', 'needs', 'actions', 'blocks', 'users'] as $fileBase) {

            $file = $path . $fileBase . '.json';

            if (!file_exists($file)) {
                echo "[⚠️] $fileBase.json introuvable\n";
                continue;
            }

            $data = json_decode(file_get_contents($file), true);

            if (!$data) continue;

            $entityName  = ucfirst(rtrim($fileBase, 's'));
            $class       = $entityMap[$entityName];
            $repo        = $this->em->getRepository($class);

            foreach ($data as $item) {

                if (empty($item['title'])) continue;

                $entity = $repo->findOneBy(['title' => $item['title']]) ?? new $class();

                foreach ($item as $key => $value) {
                    $setter = 'set' . ucfirst($key);

                    if (method_exists($entity, $setter)) {

                        if (in_array($key, ['createdAt', 'updatedAt']) && is_string($value)) {
                            $value = new \DateTimeImmutable($value);
                        }

                        $entity->$setter($value);
                    }
                }

                $this->em->persist($entity);
            }

            echo "[✅] $fileBase chargé\n";
        }

        $this->em->flush();

        echo "[Seeder] DONE\n";
    }
}