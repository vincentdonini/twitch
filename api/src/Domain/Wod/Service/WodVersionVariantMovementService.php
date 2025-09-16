<?php

namespace App\Domain\Wod\Service;

use App\Domain\Wod\Entity\WodVersionVariantMovement;

final class WodVersionVariantMovementService
{
    public function __construct(
        private readonly WodVersionVariantService $wodVersionVariantMovementService,
    ) {
    }

    public function enrichEntity(WodVersionVariantMovement $wodVersionVariantMovement): WodVersionVariantMovement
    {
        $wodVersionVariantMovement->setWodVersionVariant(
            $this->wodVersionVariantMovementService->enrichEntity($wodVersionVariantMovement->getWodVersionVariant())
        );

        return $wodVersionVariantMovement;
    }
}
