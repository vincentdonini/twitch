<?php

namespace App\Application\Muscle\UseCase;

use App\Application\Muscle\DTO\MuscleGroupDTO;
use App\Domain\Muscle\Repository\MuscleGroupRepositoryInterface;
use App\Domain\Muscle\Service\MuscleGroupTranslatorInterface;

class GetMuscleGroups
{
    public function __construct(
        private readonly MuscleGroupRepositoryInterface $repository,
        private readonly MuscleGroupTranslatorInterface $translator
    ) {

    }

    /**
     * @return MuscleGroupDTO[]
     */
    public function findAll(): array
    {
        $muscleGroups = $this->repository->findAll();

        return array_map(function ($muscleGroup) {
            return new MuscleGroupDTO(
                $muscleGroup->getId(),
                $muscleGroup->getSlug(),
                $this->translator->getName($muscleGroup),
                $this->translator->getDescription($muscleGroup),
            );
        }, $muscleGroups);
    }

    /**
     * @return MuscleGroupDTO[]
     */
    public function findAllByAreaId(int $areaId): array
    {
        $muscleGroups = $this->repository->findBy(['area' => $areaId]);

        return array_map(function ($muscleGroup) {
            return new MuscleGroupDTO(
                $muscleGroup->getId(),
                $muscleGroup->getSlug(),
                $this->translator->getName($muscleGroup),
                $this->translator->getDescription($muscleGroup)
            );
        }, $muscleGroups);
    }
}