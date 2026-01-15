<?php

namespace App\Domain\Wod\Service;

use App\Application\Common\CollectionMapper;
use App\Application\Wod\DTO\WodTypeDTO;
use App\Domain\Wod\Entity\WodType;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Filters\FilterCollection;
use Symfony\Component\HttpFoundation\RequestStack;

final class WodTypeService
{
    use LocaleTrait;

    public function __construct(
        private readonly RequestStack $requestStack,
    ) {
    }

    public function transformToDTO(WodType $wodType, FilterCollection $filters = null): WodTypeDTO
    {
        $content = $wodType->getContentByLocale($this->getLocale());

        return new WodTypeDTO(
            id            : $wodType->getId(),
            slug          : $wodType->getSlug(),
            allowedMetrics: $wodType->getAllowedMetrics(),
            title         : $content ? $content->getTitle() : '',
            summary       : $content ? $content->getSummary() : '',
            details       : $content ? $content->getDetails() : '',
        );
    }

    public function transformCollectionToDTO(array $wodTypes, FilterCollection $filters = null): array
    {
        return CollectionMapper::mapAndFilter(
            $wodTypes,
            fn(WodType $wodType) => $this->transformToDTO($wodType, $filters)
        );
    }
}