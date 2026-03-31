<?php

namespace App\Service;

use App\Repository\BlockRepository;

class BlockService
{
    private BlockRepository $blockRepository;

    private array $types = [
        'Physique',
        'Emotionnel',
        'Mental',
        'Social'
    ];

    public function __construct(BlockRepository $blockRepository)
    {
        $this->blockRepository = $blockRepository;
    }

    /**
     * Retourne tous les types disponibles
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * Récupère tous les blocs regroupés par type
     */
    public function getBlocksByType(): array
    {
        $blocksByType = [];

        foreach ($this->types as $type) {
            $blocksByType[$type] = $this->blockRepository->findByType($type);
        }

        return $blocksByType;
    }
}