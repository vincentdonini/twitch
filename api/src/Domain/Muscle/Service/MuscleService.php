<?php

namespace App\Domain\Muscle\Service;

use App\Application\Common\CollectionMapper;
use App\Application\Muscle\DTO\MuscleDTO;
use App\Domain\Muscle\Entity\Muscle;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Filters\FilterCollection;
use Symfony\Component\HttpFoundation\RequestStack;

class MuscleService
{
    use LocaleTrait;

    public function __construct(
        private readonly RequestStack       $requestStack,
        private readonly MuscleAreaService  $muscleAreaService,
        private readonly MuscleGroupService $muscleGroupService,
    ) {
    }

    public function transformToDTO(Muscle $muscle, FilterCollection $filters = null): MuscleDTO
    {
        $content = $muscle->getContentByLocale($this->getLocale());

        $muscleAreaDTO  = $this->muscleAreaService->transformToDTO($muscle->getMuscleArea());
        $muscleGroupDTO = $muscle->getMuscleGroup()
            ? $this->muscleGroupService->transformToDTO($muscle->getMuscleGroup())
            : null;

        return new MuscleDTO(
            id     : $muscle->getId(),
            slug   : $muscle->getSlug(),
            title  : $content ? $content->getTitle() : '',
            summary: $content ? $content->getSummary() : '',
            details: $content ? $content->getDetails() : '',
            area   : $muscleAreaDTO,
            group  : $muscleGroupDTO
        );
    }

    public function transformCollectionToDTO(array $muscles, FilterCollection $filters = null): array
    {
        return CollectionMapper::mapAndFilter(
            $muscles,
            fn(Muscle $muscle) => $this->transformToDTO($muscle, $filters)
        );
    }
}
