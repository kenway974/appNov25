<?php

namespace App\Service;

use App\Repository\BlockRepository;

class BlockService
{
    private BlockRepository $blockRepository;
    private array $types;

    public function __construct(BlockRepository $blockRepository)
    {
        $this->blockRepository = $blockRepository;

        // On charge les types depuis un JSON
        $this->types = json_decode(file_get_contents(__DIR__ . '/../../assets/data/block_types.json'), true);
    }

    /**
     * Retourne tous les types disponibles
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * Récupère tous les blocs triés par type
     */
    public function getAllBlocksOrderedByType(): array
    {
        return $this->blockRepository->findAllOrderByType();
    }

    /**
     * Récupère tous les blocs regroupés par type
     */
    public function getBlocksByType(): array
    {
        $result = [];
        foreach ($this->types as $type) {
            $result[$type] = $this->blockRepository->findByType($type);
        }
        return $result;
    }
}
