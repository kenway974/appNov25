<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Factory\{ActionFactory, BlockFactory, FeelingFactory, NeedFactory};

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        BlockFactory::createAll();

        // Le flush est géré automatiquement par Foundry, pas besoin ici
        // mais tu peux le garder si tu ajoutes d’autres persistance manuelles
        $manager->flush();
    }
}
