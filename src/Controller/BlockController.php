<?php

namespace App\Controller;

use App\Entity\Block;
use App\Repository\BlockRepository;
use App\Service\BlockService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/block')]
final class BlockController extends AbstractController
{
    #[Route(name: 'app_block')]
    public function indexBlock(BlockService $blockService): Response
    {
        // On récupère les blocs groupés par type
        $blocksByType = $blockService->getBlocksByType();

        return $this->render('block/index.html.twig', [
            'controller_name' => 'BlockController',
            'blocksByType' => $blocksByType,
        ]);
    }

    #[Route('/block/{id}', name: 'app_block_show_from_need', methods: ['GET'])]
    public function show(Block $block, BlockRepository $blockRepository): Response
    {
        $allBlocks = [];

        foreach ($block->getActions() as $action) {
            $blocksByAction = $blockRepository->findByUserAction($action->getId());
            $allBlocks = array_merge($allBlocks, $blocksByAction);
        }

        // Supprime les doublons
        $allBlocks = array_unique($allBlocks, SORT_REGULAR);

        return $this->render('block/show.html.twig', [
            'block' => $block,
            'allBlocks' => $allBlocks,
        ]);
    }

    #[Route('/block/read/{id}', name: 'app_block_read', methods: ['GET'])]
    public function read(Block $block): Response
    {
        // Affiche juste les détails du bloc
        return $this->render('block/read.html.twig', [
            'block' => $block,
        ]);
    }
}
