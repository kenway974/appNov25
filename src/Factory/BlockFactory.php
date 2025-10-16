<?php

namespace App\Factory;

use App\Entity\Block;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentObjectFactory<Block>
 */
final class BlockFactory extends PersistentProxyObjectFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    public static function class(): string
    {
        return Block::class;
    }

    protected function defaults(): array
    {
        $block = self::faker()->randomElement(self::BLOCKS_DATA);

        return [
            'title' => $block['title'],
            'description' => $block['description'] ?? null,
            'type' => $block['type'] ?? null,
            'beliefs' => $block['beliefs'] ?? [],
            'reframings' => $block['reframings'] ?? [],
        ];
    }

    public const BLOCKS_DATA = [
        [
            'title' => 'La peur me bloque',
            'description' => 'Blocage dû à la peur de l’échec qui empêche de passer à l’action.',
            'type' => 'Peur de l’échec',
            'beliefs' => [
                'Si j’échoue, je ne vaux rien',
                'Il vaut mieux ne rien faire que mal faire',
            ],
            'reframings' => [
                'Chaque échec est une étape vers la réussite',
                'Essayer est déjà un signe de courage',
            ],
        ],
        [
            'title' => 'Je suis pas motivé, je préfère me poser/sortir',
            'description' => 'Blocage lié au manque de motivation pour commencer les actions.',
            'type' => 'Manque de motivation',
            'beliefs' => [
                'Je n’ai pas envie maintenant',
                'Je n’ai pas la force pour commencer',
            ],
            'reframings' => [
                'Faire un petit pas est déjà une victoire',
                'L’action génère souvent de la motivation',
            ],
        ],
        [
            'title' => 'Tout ce que j\'ai à faire s\'embrouille dans ma tête',
            'description' => 'Blocage dû à une surcharge mentale qui empêche de se concentrer.',
            'type' => 'Surcharge mentale',
            'beliefs' => [
                'Je ne peux pas gérer toutes ces choses à la fois',
                'Tout est trop compliqué pour agir',
            ],
            'reframings' => [
                'Je peux avancer étape par étape',
                'Faire une chose à la fois me libère de la surcharge',
            ],
        ],
        [
            'title' => 'Le stress me paralyse',
            'description' => 'Blocage causé par le stress ou l’anxiété.',
            'type' => 'Stress / anxiété',
            'beliefs' => [
                'Je ne vais pas y arriver',
                'Tout va mal se passer',
            ],
            'reframings' => [
                'Je peux respirer et avancer calmement',
                'Chaque action gère le stress progressivement',
            ],
        ],
        [
            'title' => 'Je ne sais par quoi commencer',
            'description' => 'Blocage lié au manque de clarté sur ce qu’il faut faire.',
            'type' => 'Manque de clarté',
            'beliefs' => [
                'Je ne sais pas par où commencer',
                'Il faut tout planifier parfaitement avant d’agir',
            ],
            'reframings' => [
                'Je peux avancer même sans tout savoir',
                'La clarté vient en agissant et en ajustant',
            ],
        ],
        [
            'title' => 'J\'ai peur qu\'on me juge',
            'description' => 'Blocage dû à la peur du jugement des autres.',
            'type' => 'Peur du jugement',
            'beliefs' => [
                'Les autres vont me critiquer',
                'Je dois plaire à tout le monde',
            ],
            'reframings' => [
                'Mon action est pour moi, pas pour les autres',
                'Je peux apprendre de chaque retour sans le prendre personnellement',
            ],
        ],
        [
            'title' => 'Je ne me sens pas soutenu',
            'description' => 'Blocage lié à l’absence de soutien pour passer à l’action.',
            'type' => 'Manque de soutien',
            'beliefs' => [
                'Je suis seul pour y arriver',
                'Personne ne va m’aider',
            ],
            'reframings' => [
                'Je peux chercher du soutien ou m’organiser seul',
                'Je peux progresser étape par étape sans attendre les autres',
            ],
        ],
        [
            'title' => 'Je suis trop fatigué',
            'description' => 'Blocage dû à la fatigue physique ou mentale.',
            'type' => 'Fatigue physique ou mentale',
            'beliefs' => [
                'Je n’ai pas l’énergie nécessaire',
                'Je suis trop fatigué pour commencer',
            ],
            'reframings' => [
                'Je peux commencer par un petit effort',
                'L’action peut me donner de l’énergie et de la motivation',
            ],
        ],
    ];


    public static function createAll(): void
    {
        foreach (self::BLOCKS_DATA as $data) {
            self::createOne($data);
        }
    }
}
