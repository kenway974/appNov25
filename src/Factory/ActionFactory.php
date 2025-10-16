<?php

namespace App\Factory;

use App\Entity\Action;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Action>
 */
final class ActionFactory extends PersistentObjectFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    public static function class(): string
    {
        return Action::class;
    }

    protected function defaults(): array
    {
        $action = self::faker()->randomElement(self::ACTIONS_DATA);

        return [
            'title' => $action['title'],
            'description' => $action['description'] ?? null,
            'intension' => $action['intension'] ?? null,
            'isDoableNow' => $action['isDoableNow'] ?? null,
            'isRecurring' => $action['isRecurring'] ?? null,
            'duration' => $action['duration'] ?? null,
            'type' => $action['type'] ?? null,
            'icon' => $action['icon'] ?? null,
        ];
    }

    public const ACTIONS_DATA = [
        [
            'title' => 'Apprendre une nouvelle compétence',
            'description' => 'Suivre un cours ou lire un livre pour progresser.',
            'intension' => 'Évolution personnelle',
            'isDoableNow' => true,
            'isRecurring' => false,
            'duration' => 60,
            'type' => 'Évolution',
            'icon' => 'learning.svg',
        ],
        [
            'title' => 'Appeler un proche',
            'description' => 'Prendre des nouvelles et maintenir le lien.',
            'intension' => 'Contact social',
            'isDoableNow' => true,
            'isRecurring' => true,
            'duration' => 15,
            'type' => 'Relationnel',
            'icon' => 'phone.svg',
        ],
        [
            'title' => 'Faire une séance de sport',
            'description' => 'Bouger pour rester en forme et actif.',
            'intension' => 'Santé physique',
            'isDoableNow' => true,
            'isRecurring' => true,
            'duration' => 45,
            'type' => 'Physique',
            'icon' => 'exercise.svg',
        ],
        [
            'title' => 'Méditer 10 minutes',
            'description' => 'Se recentrer et réduire le stress.',
            'intension' => 'Bien-être mental',
            'isDoableNow' => true,
            'isRecurring' => true,
            'duration' => 10,
            'type' => 'Mental',
            'icon' => 'meditation.svg',
        ],
        [
            'title' => 'Planifier la semaine',
            'description' => 'Organiser ses objectifs et tâches.',
            'intension' => 'Clarté et contrôle',
            'isDoableNow' => true,
            'isRecurring' => true,
            'duration' => 30,
            'type' => 'Organisation',
            'icon' => 'calendar.svg',
        ],
        [
            'title' => 'Écrire dans son journal',
            'description' => 'Exprimer ses pensées et émotions.',
            'intension' => 'Réflexion personnelle',
            'isDoableNow' => true,
            'isRecurring' => true,
            'duration' => 20,
            'type' => 'Mental',
            'icon' => 'journal.svg',
        ],
        [
            'title' => 'Partager un repas avec un proche',
            'description' => 'Renforcer les liens en partageant un moment convivial.',
            'intension' => 'Relationnel',
            'isDoableNow' => false,
            'isRecurring' => true,
            'duration' => 90,
            'type' => 'Lien',
            'icon' => 'meal.svg',
        ],
        [
            'title' => 'Faire une promenade en nature',
            'description' => 'Se détendre et se reconnecter avec soi-même.',
            'intension' => 'Bien-être',
            'isDoableNow' => true,
            'isRecurring' => true,
            'duration' => 60,
            'type' => 'Physique',
            'icon' => 'nature.svg',
        ],
        [
            'title' => 'Rechercher du sens à ses actions',
            'description' => 'Réfléchir à la raison profonde de ses engagements.',
            'intension' => 'Sens de la vie',
            'isDoableNow' => true,
            'isRecurring' => false,
            'duration' => 30,
            'type' => 'Sens',
            'icon' => 'meaning.svg',
        ],
        [
            'title' => 'Faire du bénévolat',
            'description' => 'Contribuer à une cause qui a du sens.',
            'intension' => 'Contribution sociale',
            'isDoableNow' => false,
            'isRecurring' => true,
            'duration' => 120,
            'type' => 'Sens',
            'icon' => 'volunteer.svg',
        ],
        [
            'title' => 'Discuter avec un mentor',
            'description' => 'Échanger sur son parcours et ses objectifs.',
            'intension' => 'Évolution et soutien',
            'isDoableNow' => false,
            'isRecurring' => true,
            'duration' => 60,
            'type' => 'Évolution',
            'icon' => 'mentor.svg',
        ],
        [
            'title' => 'Préparer un plan d\'urgence',
            'description' => 'Être prêt face aux imprévus pour se rassurer.',
            'intension' => 'Sécurité',
            'isDoableNow' => true,
            'isRecurring' => false,
            'duration' => 45,
            'type' => 'Sécurité',
            'icon' => 'plan.svg',
        ],
        [
            'title' => 'Faire une séance de yoga',
            'description' => 'Améliorer souplesse et calme intérieur.',
            'intension' => 'Santé physique et mentale',
            'isDoableNow' => true,
            'isRecurring' => true,
            'duration' => 40,
            'type' => 'Physique',
            'icon' => 'yoga.svg',
        ],
        [
            'title' => 'Écouter un podcast inspirant',
            'description' => 'S\'informer et se motiver pour avancer.',
            'intension' => 'Évolution personnelle',
            'isDoableNow' => true,
            'isRecurring' => false,
            'duration' => 30,
            'type' => 'Évolution',
            'icon' => 'podcast.svg',
        ],
        [
            'title' => 'Faire une activité créative',
            'description' => 'Exprimer sa créativité et se détendre.',
            'intension' => 'Bien-être',
            'isDoableNow' => true,
            'isRecurring' => true,
            'duration' => 60,
            'type' => 'Mental',
            'icon' => 'creativity.svg',
        ],
    ];

    public static function createAll(): void
    {
        foreach (self::ACTIONS_DATA as $data) {
            self::createOne($data);
        }
    }
}
