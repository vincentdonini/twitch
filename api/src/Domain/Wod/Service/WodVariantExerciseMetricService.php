<?php

namespace App\Domain\Wod\Service;

use App\Application\Common\CollectionMapper;
use App\Application\Wod\DTO\WodVariantExerciseMetricDTO;
use App\Domain\Wod\Entity\WodVariantExerciseMetric;
use App\Infrastructure\Filters\FilterCollection;

final readonly class WodVariantExerciseMetricService
{
    public function transformToDTO(WodVariantExerciseMetric $wodVariantExerciseMetrics, FilterCollection $filters = null): ?WodVariantExerciseMetricDTO
    {
        return new WodVariantExerciseMetricDTO(
            id   : $wodVariantExerciseMetrics->getId(),
            type : $wodVariantExerciseMetrics->getType(),
            value: $wodVariantExerciseMetrics->getValue()
        );
    }

    public function transformCollectionToDTO(array $wodVariantExerciseMetrics, FilterCollection $filters = null): array
    {
        return CollectionMapper::mapAndFilter(
            $wodVariantExerciseMetrics,
            fn(WodVariantExerciseMetric $wodVariantExerciseMetrics) => $this->transformToDTO($wodVariantExerciseMetrics, $filters)
        );
    }
}
