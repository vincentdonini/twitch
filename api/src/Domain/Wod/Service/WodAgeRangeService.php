<?php

namespace App\Domain\Wod\Service;

use App\Application\Common\CollectionMapper;
use App\Application\Wod\DTO\WodAgeRangeDTO;
use App\Domain\Wod\Entity\WodAgeRange;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Filters\FilterCollection;
use Symfony\Component\HttpFoundation\RequestStack;

final class WodAgeRangeService
{
    use LocaleTrait;

    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    public function transformToDTO(WodAgeRange $wodAgeRange, FilterCollection $filters = null): WodAgeRangeDTO
    {
        $content = $wodAgeRange->getContentByLocale($this->getLocale());

        return new WodAgeRangeDTO(
            id     : $wodAgeRange->getId(),
            slug   : $wodAgeRange->getSlug(),
            title  : $content ? $content->getTitle() : '',
            minAge : $wodAgeRange->getMinAge() ?? null,
            maxAge : $wodAgeRange->getMaxAge() ?? null,
            summary: $content ? $content->getSummary() : '',
            details: $content ? $content->getDetails() : '',
        );
    }

    public function transformCollectionToDTO(array $wodAgeRanges, FilterCollection $filters = null): array
    {
        return CollectionMapper::mapAndFilter(
            $wodAgeRanges,
            fn(WodAgeRange $wodAgeRange) => $this->transformToDTO($wodAgeRange, $filters)
        );
    }
}