<?php

namespace App\Domain\Achievement\Service;

use App\Application\Achievement\DTO\AchievementCategoryDTO;
use App\Application\Common\CollectionMapper;
use App\Domain\Achievement\Entity\AchievementCategory;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Filters\FilterCollection;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class AchievementCategoryService
{
    use LocaleTrait;

    private array $filterMapping;

    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    public function transformToDTO(AchievementCategory $achievementCategory, FilterCollection $filters = null): ?AchievementCategoryDTO
    {
        $content = $achievementCategory->getContentByLocale($this->getLocale());

        return new AchievementCategoryDTO(
            id         : $achievementCategory->getId(),
            code       : $achievementCategory->getCode(),
            position   : $achievementCategory->getPosition(),
            title      : $content ? $content->getTitle() : '',
            description: $content ? $content->getDescription() : '',
        );
    }

    public function transformCollectionToDTO(array $achievementCategories, FilterCollection $filters = null): array
    {
        return CollectionMapper::mapAndFilter(
            $achievementCategories,
            fn(AchievementCategory $achievementCategory) => $this->transformToDTO($achievementCategory, $filters)
        );
    }
}