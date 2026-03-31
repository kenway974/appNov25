<?php

namespace App\Service;

use App\Repository\ActionRepository;

class ActionService
{ 
    private ActionRepository $actionRepository;

    private array $types = [
        'Physique',
        'Mental',
        'Social',
        'Créatif',
    ];

    private array $intensions = [
        'Me recentrer',
        'Me calmer',
        'M\'orienter',
        'Me challenger',
        'M\'ouvrir',
        'Me surpasser',
        'Avancer',
    ];

    public function __construct(ActionRepository $actionRepository)
    {
        $this->actionRepository = $actionRepository;
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    public function getIntensions(): array
    {
        return $this->intensions;
    }

    public function getActionsByType(): array
    {
        $actionsByType = [];

        foreach ($this->types as $type) {
            $actionsByType[$type] = $this->actionRepository->findByType($type);
        }

        return $actionsByType;
    }

    public function getActionsByIntension(): array
    {
        $actionsByIntension = [];

        foreach ($this->intensions as $intension) {
            $actionsByIntension[$intension] = $this->actionRepository->findByIntension($intension);
        }

        return $actionsByIntension;
    }

    public function getInstantActions(): array
    {
        return $this->actionRepository->findByIsDoableNow(true);
    }

    public function getDelayedActions(): array
    {
        return $this->actionRepository->findByIsDoableNow(false);
    }
}