<?php

namespace App\Service;

use App\Repository\ActionRepository;

class ActionService
{ 
    private ActionRepository $actionRepository;
    private array $types;
    private array $intensions;

    public function __construct(ActionRepository $actionRepository)
    {
        $this->actionRepository = $actionRepository;

        // On charge les JSON en mémoire une fois pour toutes
        $this->types = json_decode(file_get_contents(__DIR__ . '/../../assets/data/action_types.json'), true);
        $this->intensions = json_decode(file_get_contents(__DIR__ . '/../../assets/data/action_intensions.json'), true);
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    public function getIntensions(): array
    {
        return $this->intensions;
    }

    /**
     * Récupère toutes les actions regroupées par type
     */
    public function getActionsByType(): array
    {
        $result = [];
        foreach ($this->types as $type) {
            $result[$type] = $this->actionRepository->findByType($type);
        }
        return $result;
    }

    /**
     * Récupère toutes les actions regroupées par intension
     */
    public function getActionsByIntension(): array
    {
        $result = [];
        foreach ($this->intensions as $intension) {
            $result[$intension] = $this->actionRepository->findByIntension($intension);
        }
        return $result;
    }

    /**
     * Récupère les actions faisables maintenant ou non
     */
    public function getInstantActions(): array
    {
        return $this->actionRepository->findByIsDoableNow(true);
    }

    public function getDelayedActions(): array
    {
        return $this->actionRepository->findByIsDoableNow(false);
    }
}
