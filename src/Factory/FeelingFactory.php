<?php

namespace App\Factory;

use App\Entity\Feeling;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Feeling>
 */
final class FeelingFactory extends PersistentObjectFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    public static function class(): string
    {
        return Feeling::class;
    }

    protected function defaults(): array
    {
        $feeling = self::faker()->randomElement(self::FEELINGS_DATA);

        return [
            'title' => $feeling['title'],
            'emotion' => $feeling['emotion'],
            'description' => $feeling['description'],
            'triggers' => $feeling['triggers'] ?? [],
        ];
    }

    public const FEELINGS_DATA = [
        [
            'title' => 'Je me sens seul',
            'emotion' => 'Tristesse',
            'description' => 'Un sentiment d’isolement et de vide intérieur.',
        ],
        [
            'title' => 'Je me sens stressé',
            'emotion' => 'Peur',
            'description' => 'Une tension intérieure accompagnée d’anxiété.',
        ],
        [
            'title' => 'Je ressens de la honte',
            'emotion' => 'Honte',
            'description' => 'Une gêne profonde liée à un sentiment de faute.',
        ],
        [
            'title' => 'Je ressens de la culpabilité',
            'emotion' => 'Culpabilité',
            'description' => 'Un poids moral lié à une action regrettée.',
        ],
        [
            'title' => 'Ma gorge se noue',
            'emotion' => 'Tristesse',
            'description' => 'Une sensation physique d’étouffement émotionnel.',
        ],
        [
            'title' => 'Mon cœur se serre',
            'emotion' => 'Tristesse',
            'description' => 'Une douleur sourde au niveau de la poitrine.',
        ],
        [
            'title' => 'Mon cœur bat plus vite',
            'emotion' => 'Peur',
            'description' => 'Une accélération du rythme cardiaque liée à l’alerte.',
        ],
        [
            'title' => 'Je me sens agité',
            'emotion' => 'Colère',
            'description' => 'Une agitation nerveuse et une énergie incontrôlée.',
        ],
        [
            'title' => 'Je ressens de la jalousie',
            'emotion' => 'Colère',
            'description' => 'Un mélange d’envie et de frustration envers autrui.',
        ],
    ];

    public static function createAll(): void
    {
        foreach (self::FEELINGS_DATA as $data) {
            self::createOne($data);
        }
    }
}
