<?php

namespace App\Domain\Exercise\Service;

use App\Application\Common\CollectionMapper;
use App\Application\Exercise\DTO\ExerciseCategoryDTO;
use App\Domain\Common\Filter\EntityFieldFilter;
use App\Domain\Exercise\Entity\ExerciseCategory;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Filters\FilterCollection;
use Symfony\Component\HttpFoundation\RequestStack;

class ExerciseCategoryService
{
    use LocaleTrait;

    private array $filterMapping;

    public function __construct(
        private readonly RequestStack      $requestStack,
        private readonly EntityFieldFilter $EntityFieldFilter,
    ) {
        $this->filterMapping = [
            'contents.title'   => fn(ExerciseCategory $exerciseCategory) => $exerciseCategory->getContentByLocale($this->getLocale())?->getTitle(),
            'contents.summary' => fn(ExerciseCategory $exerciseCategory) => $exerciseCategory->getContentByLocale($this->getLocale())?->getSummary(),
            'contents.details' => fn(ExerciseCategory $exerciseCategory) => $exerciseCategory->getContentByLocale($this->getLocale())?->getDetails(),
        ];
    }

    public function transformToDTO(ExerciseCategory $exerciseCategory, FilterCollection $filters = null): ?ExerciseCategoryDTO
    {
        $content = $exerciseCategory->getContentByLocale($this->getLocale());
        if (!$this->EntityFieldFilter->match($exerciseCategory, $filters, $this->filterMapping)) {
            return null;
        }

        return new ExerciseCategoryDTO(
            id     : $exerciseCategory->getId(),
            slug   : $exerciseCategory->getSlug(),
            title  : $content ? $content->getTitle() : '',
            summary: $content ? $content->getSummary() : '',
            details: $content ? $content->getDetails() : '',
        );
    }

    public function transformCollectionToDTO(array $exerciseCategories, FilterCollection $filters = null): array
    {
        return CollectionMapper::mapAndFilter(
            $exerciseCategories,
            fn(ExerciseCategory $exerciseCategory) => $this->transformToDTO($exerciseCategory, $filters)
        );
    }
}
