<?php

namespace App\Application\Muscle\UseCase;

use App\Domain\Muscle\Repository\MuscleGroupRepositoryInterface;
use App\Domain\Muscle\Service\MuscleGroupTranslatorInterface;
use App\Application\Muscle\DTO\MuscleGroupDTO;

class GetMuscleGroup
{
    public function __construct(
        private readonly MuscleGroupRepositoryInterface $repository,
        private readonly MuscleGroupTranslatorInterface $translator
    ) {

    }

    public function findOneById(string $id): ?MuscleGroupDTO
    {
        $muscleArea = $this->repository->findOneById($id);

        if (!$muscleArea) {
            return null;
        }

        return new MuscleGroupDTO(
            $muscleArea->getId(),
            $muscleArea->getSlug(),
            $this->translator->getName($muscleArea),
            $this->translator->getDescription($muscleArea),
        );
    }

    public function findOneByIdByAreaId(string $id, string $areaId): ?MuscleGroupDTO
    {
        $muscleArea = $this->repository->findOneBy([
            'area' => $areaId,
            'id'   => $id,
        ]);

        if (!$muscleArea) {
            return null;
        }

        return new MuscleGroupDTO(
            $muscleArea->getId(),
            $muscleArea->getSlug(),
            $this->translator->getName($muscleArea),
            $this->translator->getDescription($muscleArea),
        );
    }
}