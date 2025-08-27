<?php

namespace App\Application\Muscle\UseCase;

use App\Application\Muscle\DTO\MuscleAreaDTO;
use App\Domain\Muscle\Repository\MuscleAreaRepositoryInterface;
use App\Domain\Muscle\Service\MuscleAreaTranslatorInterface;

class GetMuscleArea
{
    public function __construct(
        private readonly MuscleAreaRepositoryInterface $repository,
        private readonly MuscleAreaTranslatorInterface $translator
    ) {

    }

    public function findOneById(string $id): ?MuscleAreaDTO
    {
        $muscleArea = $this->repository->findOneById($id);

        if (!$muscleArea) {
            return null;
        }

        return new MuscleAreaDTO(
            $muscleArea->getId(),
            $muscleArea->getSlug(),
            $this->translator->getName($muscleArea),
            $this->translator->getDescription($muscleArea),
        );
    }
}