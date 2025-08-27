<?php

namespace App\Domain\Muscle\Service;

use App\Application\Muscle\DTO\MuscleAreaDTO;
use App\Application\Muscle\DTO\MuscleGroupDTO;
use App\Application\Muscle\DTO\MuscleDTO;
use App\Domain\Muscle\Entity\Muscle;

class MuscleService
{
    public function __construct(
        private readonly MuscleTranslatorInterface      $muscleTranslator,
        private readonly MuscleAreaTranslatorInterface  $muscleAreaTranslator,
        private readonly MuscleGroupTranslatorInterface $groupTranslator,
    ) {

    }

    public function transformToDTO(Muscle $muscle): MuscleDTO
    {
        $muscleArea    = $muscle->getArea();
        $muscleAreaDTO = new MuscleAreaDTO(
            $muscleArea->getId(),
            $muscleArea->getSlug(),
            $this->muscleAreaTranslator->getName($muscleArea),
            $this->muscleAreaTranslator->getDescription($muscleArea),
        );

        $group = $muscle->getGroup();
        if ($group) {
            $groupDTO = new MuscleGroupDTO(
                $group->getId(),
                $group->getSlug(),
                $this->groupTranslator->getName($group),
                $this->groupTranslator->getDescription($group),
                $muscleAreaDTO,
            );
        } else {
            $groupDTO = null;
        }

        return new MuscleDTO(
            $muscle->getId(),
            $muscle->getSlug(),
            $this->muscleTranslator->getName($muscle),
            $this->muscleTranslator->getDescription($muscle),
            $muscleAreaDTO,
            $groupDTO
        );
    }
}
