<?php

namespace App\Domain\Muscle\Service;

use App\Application\Common\CollectionMapper;
use App\Application\Muscle\DTO\MuscleAreaDTO;
use App\Domain\Common\Filter\EntityFieldFilter;
use App\Domain\Muscle\Entity\MuscleArea;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Filters\FilterCollection;
use Symfony\Component\HttpFoundation\RequestStack;

class MuscleAreaService
{
    use LocaleTrait;

    private array $filterMapping;

    public function __construct(
        private readonly RequestStack      $requestStack,
        private readonly EntityFieldFilter $EntityFieldFilter,
    ) {
        $this->filterMapping = [
            'contents.title'   => fn(MuscleArea $muscleArea) => $muscleArea->getContentByLocale($this->getLocale())?->getTitle(),
            'contents.summary' => fn(MuscleArea $muscleArea) => $muscleArea->getContentByLocale($this->getLocale())?->getSummary(),
            'contents.details' => fn(MuscleArea $muscleArea) => $muscleArea->getContentByLocale($this->getLocale())?->getDetails(),
        ];
    }

    public function transformToDTO(MuscleArea $muscleArea, FilterCollection $filters = null): ?MuscleAreaDTO
    {
        $content = $muscleArea->getContentByLocale($this->getLocale());
        if (!$this->EntityFieldFilter->match($muscleArea, $filters, $this->filterMapping)) {
            return null;
        }

        return new MuscleAreaDTO(
            id     : $muscleArea->getId(),
            slug   : $muscleArea->getSlug(),
            title  : $content ? $content->getTitle() : '',
            summary: $content ? $content->getSummary() : '',
            details: $content ? $content->getDetails() : '',
        );
    }

    public function transformCollectionToDTO(array $muscleAreas, FilterCollection $filters = null): array
    {
        return CollectionMapper::mapAndFilter(
            $muscleAreas,
            fn(MuscleArea $muscleArea) => $this->transformToDTO($muscleArea, $filters)
        );
    }
}
