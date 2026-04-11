<?php

namespace App\Domain\Muscle\Service;

use App\Application\Common\CollectionMapper;
use App\Application\Muscle\DTO\MuscleGroupDTO;
use App\Domain\Common\Filter\EntityFieldFilter;
use App\Domain\Muscle\Entity\MuscleGroup;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Filters\FilterCollection;
use Symfony\Component\HttpFoundation\RequestStack;

class MuscleGroupService
{
    use LocaleTrait;

    private array $filterMapping;

    public function __construct(
        private readonly RequestStack      $requestStack,
        private readonly MuscleAreaService $muscleAreaService,
        private readonly EntityFieldFilter $EntityFieldFilter,
    ) {
        $this->filterMapping = [
            'contents.title'   => fn(MuscleGroup $muscleGroup) => $muscleGroup->getContentByLocale($this->getLocale())?->getTitle(),
            'contents.summary' => fn(MuscleGroup $muscleGroup) => $muscleGroup->getContentByLocale($this->getLocale())?->getSummary(),
            'contents.details' => fn(MuscleGroup $muscleGroup) => $muscleGroup->getContentByLocale($this->getLocale())?->getDetails(),
        ];
    }

    public function transformToDTO(MuscleGroup $muscleGroup, FilterCollection $filters = null, int $wodCount = 0): ?MuscleGroupDTO
    {
        $content = $muscleGroup->getContentByLocale($this->getLocale());
        if (!$this->EntityFieldFilter->match($muscleGroup, $filters, $this->filterMapping)) {
            return null;
        }

        $muscleArea    = $muscleGroup->getMuscleArea();
        $muscleAreaDTO = null;
        if ($muscleArea) {
            $muscleAreaDTO = $this->muscleAreaService->transformToDTO($muscleArea, $filters);
        }

        return new MuscleGroupDTO(
            id        : $muscleGroup->getId(),
            slug      : $muscleGroup->getSlug(),
            title     : $content ? $content->getTitle() : '',
            summary   : $content ? $content->getSummary() : '',
            details   : $content ? $content->getDetails() : '',
            muscleArea: $muscleAreaDTO,
            wodCount  : $wodCount,
        );
    }

    public function transformCollectionToDTO(array $muscleGroups, FilterCollection $filters = null, array $wodCounts = []): array
    {
        return CollectionMapper::mapAndFilter(
            $muscleGroups,
            fn(MuscleGroup $muscleGroup) => $this->transformToDTO($muscleGroup, $filters, $wodCounts[(string)$muscleGroup->getId()] ?? 0)
        );
    }
}
