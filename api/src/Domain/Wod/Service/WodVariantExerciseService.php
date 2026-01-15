<?php

namespace App\Domain\Wod\Service;

use App\Application\Common\CollectionMapper;
use App\Application\Wod\DTO\WodVariantExerciseDTO;
use App\Domain\Exercise\Service\ExerciseService;
use App\Domain\Wod\Entity\WodVariantExercise;
use App\Infrastructure\Filters\FilterCollection;

final readonly class WodVariantExerciseService
{
    public function __construct(
        private ExerciseService                 $exerciseService,
        private WodVariantExerciseMetricService $wodVariantExerciseMetricService,
    ) {
    }

    public function transformToDTO(WodVariantExercise $wodVariantExercise, FilterCollection $filters = null): ?WodVariantExerciseDTO
    {
        $exerciseDTO = $this->exerciseService->transformToDTO(
            $wodVariantExercise->getExercise(),
            $filters
        );

        $wodVariantExerciseMetricsDTOs = $this->wodVariantExerciseMetricService->transformCollectionToDTO(
            $wodVariantExercise->getMetrics()->toArray(),
            $filters
        );

        return new WodVariantExerciseDTO(
            id      : $wodVariantExercise->getId(),
            position: $wodVariantExercise->getPosition(),
            exercise: $exerciseDTO,
            metrics : $wodVariantExerciseMetricsDTOs,
        );
    }

    public function transformCollectionToDTO(array $wodVariantExercises, FilterCollection $filters = null): array
    {
        return CollectionMapper::mapAndFilter(
            $wodVariantExercises,
            fn(WodVariantExercise $wodVariantExercise) => $this->transformToDTO($wodVariantExercise, $filters)
        );
    }
}
