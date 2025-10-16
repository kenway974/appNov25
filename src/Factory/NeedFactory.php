<?php

namespace App\Factory;

use App\Entity\Need;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Need>
 */
final class NeedFactory extends PersistentObjectFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    public static function class(): string
    {
        return Need::class;
    }

    protected function defaults(): array
    {
        $need = self::faker()->randomElement(self::NEEDS_DATA);

        return [
            'title' => $need['title'],
            'description' => $need['description'] ?? null,
            'type' => $need['type'] ?? null,
        ];
    }

    public const NEEDS_DATA = [
        [
            'title' => 'Évoluer',
            'description' => 'Progresser, apprendre, devenir une meilleure version de soi.',
            'type' => 'Croissance',
        ],
        [
            'title' => 'Que mes proches ne s’inquiètent pas pour moi',
            'description' => 'Savoir que ceux que j’aime se sentent rassurés quant à ma situation.',
            'type' => 'Lien',
        ],
        [
            'title' => 'Être rassuré sur mes capacités',
            'description' => 'Avoir confiance en moi et en mes compétences.',
            'type' => 'Estime',
        ],
        [
            'title' => 'Faire du sport',
            'description' => 'Bouger mon corps pour rester en forme et me sentir vivant.',
            'type' => 'Physique',
        ],
        [
            'title' => 'Être respecté',
            'description' => 'Sentir que ma dignité et mes limites sont reconnues.',
            'type' => 'Relationnel',
        ],
        [
            'title' => 'Être rassuré sur ce qui arrive',
            'description' => 'Avoir une compréhension claire et apaisante des événements.',
            'type' => 'Sécurité',
        ],
        [
            'title' => 'Passer du temps avec des proches',
            'description' => 'Partager des moments authentiques avec les gens que j’aime.',
            'type' => 'Lien',
        ],
        [
            'title' => 'Avoir un sens à sa vie',
            'description' => 'Donner une direction profonde et cohérente à mon existence.',
            'type' => 'Sens',
        ],
        [
            'title' => 'Savoir pourquoi on fait les choses',
            'description' => 'Donner du sens à mes actions quotidiennes.',
            'type' => 'Sens',
        ],
        [
            'title' => 'Bien manger et manger varié',
            'description' => 'Avoir accès à une alimentation nourrissante et diversifiée.',
            'type' => 'Physique',
        ],
    ];

    public static function createAll(): void
    {
        foreach (self::NEEDS_DATA as $data) {
            self::createOne($data);
        }
    }
}
