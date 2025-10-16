<?php

namespace App\Service;

use App\Repository\FeelingRepository;

class FeelingService
{
    public function getFeelingsGroupedByEmotion(array $emotions, FeelingRepository $feelingRepository): array
    {
        $feelingsByEmotion = [];
        foreach ($emotions as $emotion) {
            $feelingsByEmotion[$emotion] = $feelingRepository->findByEmotion($emotion);
        }
        return $feelingsByEmotion;
    }

    public function getAllNeedsFromFeelings(array $feelingsByEmotion): array
    {
        $allNeeds = [];
        foreach ($feelingsByEmotion as $feelings) {
            foreach ($feelings as $feeling) {
                foreach ($feeling->getNeeds() as $need) {
                    $allNeeds[$need->getId()] = $need;
                }
            }
        }
        return $allNeeds;
    }
}
