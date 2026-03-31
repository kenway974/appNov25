<?php

namespace App\Service;

use App\Repository\NeedRepository;

class NeedService
{
    private NeedRepository $needRepository;

    private array $types = [
        'Physique',
        'Relationnel',
        'Sens',
        'Accomplissement',
        'Émotionnel',
        'Mental',
    ];

    public function __construct(NeedRepository $needRepository)
    {
        $this->needRepository = $needRepository;
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * Récupère les besoins groupés par type
     */
    public function getNeedsByType(): array
    {
        $needsByType = [];

        foreach ($this->types as $type) {
            $needsByType[$type] = $this->needRepository->findByType($type);
        }

        return $needsByType;
    }

    /**
     * Récupère toutes les actions liées aux besoins fournis
     */
    public function getAllActionsFromNeeds(array $needsByType): array
    {
        $allActions = [];

        foreach ($needsByType as $needs) {
            foreach ($needs as $need) {
                foreach ($need->getActions() as $action) {
                    $allActions[$action->getId()] = $action;
                }
            }
        }

        return $allActions;
    }
}