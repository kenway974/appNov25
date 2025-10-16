<?php

namespace App\Service;

use App\Repository\NeedRepository;

class NeedService
{
    private array $types;

    public function __construct()
    {
        // On charge les JSON en mémoire une fois pour toutes
        $this->types = json_decode(file_get_contents(__DIR__ . '/../../assets/data/need_types.json'), true);
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * Récupère les besoins groupés par type
     *
     * @param array $types
     * @param NeedRepository $needRepository
     * @return array
     */
    public function getNeedsGroupedByType(NeedRepository $needRepository): array
    {
        $needsByType = [];

        foreach ($this->types as $type) {
            $needsByType[$type] = $needRepository->findByType($type);
        }

        return $needsByType;
    }

    /**
     * Récupère toutes les actions liées aux besoins fournis (groupés par type)
     *
     * @param array $needsByType
     * @return array
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
