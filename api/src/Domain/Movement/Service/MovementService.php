<?php

namespace App\Domain\Movement\Service;

use App\Application\Movement\DTO\MovementDTO;
use App\Domain\Movement\Entity\Movement;

class MovementService
{
    public function __construct(
        private readonly MovementTranslatorInterface $movementTranslator,
    ) {

    }

    public function transformToDTO(Movement $movement): MovementDTO
    {
        return new MovementDTO(
            $movement->getId(),
            $movement->getSlug(),
            $this->movementTranslator->getName($movement),
            $this->movementTranslator->getDescription($movement),
        );
    }
}
