<?php

namespace App\Domain\Muscle\Service;

use App\Application\Common\CollectionMapper;
use App\Application\Muscle\DTO\MuscleSegmentDTO;
use App\Domain\Common\Filter\EntityFieldFilter;
use App\Domain\Muscle\Entity\MuscleSegment;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Filters\FilterCollection;
use Symfony\Component\HttpFoundation\RequestStack;

class MuscleSegmentService
{
    use LocaleTrait;

    private array $filterMapping;

    public function __construct(
        private readonly RequestStack      $requestStack,
        private readonly MuscleService     $muscleService,
        private readonly EntityFieldFilter $EntityFieldFilter,
    ) {
        $this->filterMapping = [
            'contents.title'   => fn(MuscleSegment $segment) => $segment->getContentByLocale($this->getLocale())?->getTitle(),
            'contents.summary' => fn(MuscleSegment $segment) => $segment->getContentByLocale($this->getLocale())?->getSummary(),
            'contents.details' => fn(MuscleSegment $segment) => $segment->getContentByLocale($this->getLocale())?->getDetails(),
        ];
    }

    public function transformToDTO(MuscleSegment $segment, FilterCollection $filters = null, int $wodCount = 0): ?MuscleSegmentDTO
    {
        $content = $segment->getContentByLocale($this->getLocale());

        if (!$this->EntityFieldFilter->match($segment, $filters, $this->filterMapping)) {
            return null;
        }

        $muscleDTO = $this->muscleService->transformToDTO($segment->getMuscle());

        return new MuscleSegmentDTO(
            id      : $segment->getId(),
            slug    : $segment->getSlug(),
            title   : $content ? $content->getTitle() : '',
            summary : $content ? $content->getSummary() : '',
            details : $content ? $content->getDetails() : null,
            muscle  : $muscleDTO,
            wodCount: $wodCount,
        );
    }

    public function transformCollectionToDTO(array $segments, FilterCollection $filters = null, array $wodCounts = []): array
    {
        return CollectionMapper::mapAndFilter(
            $segments,
            fn(MuscleSegment $segment) => $this->transformToDTO($segment, $filters, $wodCounts[(string)$segment->getId()] ?? 0)
        );
    }
}
