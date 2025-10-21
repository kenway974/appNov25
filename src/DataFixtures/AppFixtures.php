<?php

namespace App\DataFixtures;

use App\Factory\ActionFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Crée 10 actions aléatoires
        ActionFactory::createMany(10);

        // On flush pas besoin car Zenstruck Foundry gère la persistance
    }
}
