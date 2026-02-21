<?php

namespace App\Domain\Achievement\Service;

use App\Application\Achievement\DTO\AchievementDTO;
use App\Application\Common\CollectionMapper;
use App\Domain\Achievement\Entity\Achievement;
use App\Infrastructure\Doctrine\Repository\Common\LocaleTrait;
use App\Infrastructure\Filters\FilterCollection;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class AchievementService
{
    use LocaleTrait;

    private array $filterMapping;

    public function __construct(
        private RequestStack               $requestStack,
        private AchievementGroupService    $achievementGroupService,
        private AchievementCategoryService $achievementCategoryService,
        private AchievementLevelService    $achievementLevelService,
    ) {
    }

    public function transformToDTO(Achievement $achievement, FilterCollection $filters = null): ?AchievementDTO
    {
        $content = $achievement->getContentByLocale($this->getLocale());

        $achievementGroupDTO    = $this->achievementGroupService->transformToDTO($achievement->getAchievementGroup());
        $achievementCategoryDTO = $this->achievementCategoryService->transformToDTO($achievement->getAchievementGroup()->getAchievementCategory());

        $achievementLevelsDTOs = $this->achievementLevelService->transformCollectionToDTO(
            $achievement->getAchievementLevels()->toArray(),
            $filters
        );

        return new AchievementDTO(
            id         : $achievement->getId(),
            code       : $achievement->getCode(),
            position   : $achievement->getPosition(),
            title      : $content ? $content->getTitle() : '',
            description: $content ? $content->getDescription() : '',
            category   : $achievementCategoryDTO,
            group      : $achievementGroupDTO,
            levels     : $achievementLevelsDTOs,
        );
    }

    public function transformCollectionToDTO(array $achievements, FilterCollection $filters = null): array
    {
        return CollectionMapper::mapAndFilter(
            $achievements,
            fn(Achievement $achievement) => $this->transformToDTO($achievement, $filters)
        );
    }
}
