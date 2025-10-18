<?php

namespace App\Domain\Movement\Service;

use App\Domain\Movement\Entity\Movement;
use App\Domain\Equipment\Service\EquipmentService;
use App\Domain\Muscle\Entity\Muscle;
use App\Domain\Muscle\Service\MuscleService;

final class MovementService
{
    public function __construct(
        private readonly MovementTranslatorInterface $movementTranslator,
        private readonly EquipmentService            $equipmentService,
        private readonly MovementCategoryService     $movementCategoryService,
        private readonly MuscleService               $muscleService,
    ) {
    }

    public function enrichEntity(Movement $movement): Movement
    {
        $movement->setName($this->movementTranslator->getName($movement));
        $movement->setDescription($this->movementTranslator->getDescription($movement));

        // Equipment
        $equipment = $movement->getEquipment();
        if ($equipment !== null) {
            $movement->setEquipment($this->equipmentService->enrichEntity($equipment));
        }

        // MovementCategory
        $movement->setMovementCategory($this->movementCategoryService->enrichEntity($movement->getMovementCategory()));

        // Muscles
        /** @var Muscle $muscle **/
        foreach ($movement->getMuscles() as $muscle) {
            $movement->addMuscle($this->muscleService->enrichEntity($muscle));
        }

        return $movement;
    }
}
