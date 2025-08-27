<?php

namespace App\Application\Muscle\UseCase;

use App\Application\Muscle\DTO\MuscleAreaDTO;
use App\Domain\Muscle\Repository\MuscleAreaRepositoryInterface;
use App\Domain\Muscle\Service\MuscleAreaTranslatorInterface;

class GetMuscleAreas
{
    public function __construct(
        private readonly MuscleAreaRepositoryInterface $repository,
        private readonly MuscleAreaTranslatorInterface $translator
    ) {

    }

    /**
     * @return MuscleAreaDTO[]
     */
    public function findAll(): array
    {
        $muscleAreas = $this->repository->findAll();

        return array_map(function ($muscleArea) {
            return new MuscleAreaDTO(
                $muscleArea->getId(),
                $muscleArea->getSlug(),
                $this->translator->getName($muscleArea),
                $this->translator->getDescription($muscleArea)
            );
        }, $muscleAreas);
    }
}