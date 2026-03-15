<?php

namespace App\Domain\Wod\Service;

use App\Application\Common\CollectionMapper;
use App\Application\Wod\DTO\WodCategoryDTO;
use App\Domain\Wod\Entity\WodCategory;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Filters\FilterCollection;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class WodCategoryService
{
    use LocaleTrait;

    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    public function transformToDTO(WodCategory $wodCategory, FilterCollection $filters = null, int $wodCount = 0): WodCategoryDTO
    {
        $content = $wodCategory->getContentByLocale($this->getLocale());

        return new WodCategoryDTO(
            id      : $wodCategory->getId(),
            slug    : $wodCategory->getSlug(),
            title   : $content ? $content->getTitle() : '',
            summary : $content ? $content->getSummary() : '',
            details : $content ? $content->getDetails() : '',
            wodCount: $wodCount,
        );
    }

    public function transformCollectionToDTO(array $wodCategories, FilterCollection $filters = null, array $wodCounts = []): array
    {
        return CollectionMapper::mapAndFilter(
            $wodCategories,
            fn(WodCategory $wodCategory) => $this->transformToDTO($wodCategory, $filters, $wodCounts[(string)$wodCategory->getId()] ?? 0)
        );
    }
}